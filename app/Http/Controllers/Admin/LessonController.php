<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Topic;
use App\Models\VideoContent;
use App\Models\TextContent;
use App\Models\DocumentContent;
use App\Models\AudioContent;
use App\Models\PresentationContent;
use App\Models\EmbedContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Lesson::with(['topic.course', 'contentable'])
            ->withCount(['progress', 'comments'])
            ->whereHas('topic', function ($q) {
                $q->whereHas('course'); // Only show lessons with valid topic and course
            });

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by topic
        if ($request->filled('topic')) {
            $query->where('topic_id', $request->topic);
        }

        // Filter by course
        if ($request->filled('course')) {
            $query->whereHas('topic', function ($q) use ($request) {
                $q->where('course_id', $request->course);
            });
        }

        // Filter by content type
        if ($request->filled('content_type')) {
            $query->where('content_type', $request->content_type);
        }

        // Filter by published status
        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        $lessons = $query->latest()->paginate(20)->withQueryString();
        $topics = Topic::with('course')->orderBy('title')->get();
        $courses = \App\Models\Course::orderBy('title')->get();

        return view('admin.lessons.index', compact('lessons', 'topics', 'courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $topics = Topic::with('course')->orderBy('title')->get();
        $selectedTopic = $request->query('topic_id');

        return view('admin.lessons.create', compact('topics', 'selectedTopic'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Base validation
        $rules = [
            'topic_id' => 'required|exists:topics,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content_type' => 'required|in:video,text,document,audio,presentation,embed',
            'duration_minutes' => 'nullable|integer|min:0|max:9999',
            'order' => 'required|integer|min:0',
            'is_preview' => 'boolean',
            'is_published' => 'boolean',
            'requires_previous_completion' => 'boolean',
        ];

        // Add content-specific validation based on content_type
        $contentType = $request->input('content_type');
        switch ($contentType) {
            case 'video':
                $rules['video_url'] = 'nullable|url';
                $rules['vimeo_id'] = 'nullable|string';
                $rules['video_transcript'] = 'nullable|string';
                $rules['video_file'] = 'nullable|file|mimes:mp4,mov,avi,webm,mkv|max:524288'; // 512MB max
                break;
            case 'text':
                $rules['text_body'] = 'required|string';
                break;
            case 'document':
                $rules['document_file'] = 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:51200';
                break;
            case 'audio':
                $rules['audio_file'] = 'required|file|mimes:mp3,wav,ogg,m4a|max:102400';
                $rules['audio_transcript'] = 'nullable|string';
                break;
            case 'presentation':
                $rules['presentation_file'] = 'required|file|mimes:pdf,ppt,pptx|max:102400';
                break;
            case 'embed':
                $rules['embed_code'] = 'nullable|string';
                $rules['embed_url'] = 'nullable|url';
                $rules['embed_provider'] = 'nullable|string';
                // Require either embed_code or embed_url
                $rules['embed_url'] = 'required_without:embed_code|nullable|url';
                $rules['embed_code'] = 'required_without:embed_url|nullable|string';
                break;
        }

        $validated = $request->validate($rules, [
            'topic_id.required' => 'Please select a topic for this lesson.',
            'title.required' => 'The lesson title is required.',
            'content_type.required' => 'Please select a content type.',
            'text_body.required' => 'Text content is required for text lessons.',
            'document_file.required' => 'Please upload a document file.',
            'audio_file.required' => 'Please upload an audio file.',
            'presentation_file.required' => 'Please upload a presentation file.',
            'embed_url.required' => 'Please provide an embed URL.',
        ]);

        DB::beginTransaction();
        try {
            // Create content first
            $content = $this->createContent($request, $contentType);

            // Create lesson with content relationship
            $lessonData = [
                'topic_id' => $validated['topic_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'content_type' => $validated['content_type'],
                'duration_minutes' => $validated['duration_minutes'] ?? 0,
                'order' => $validated['order'],
                'is_preview' => $request->has('is_preview'),
                'is_published' => $request->has('is_published'),
                'requires_previous_completion' => $request->has('requires_previous_completion'),
            ];

            // Add contentable relationship if content was created
            if ($content) {
                $lessonData['contentable_type'] = get_class($content);
                $lessonData['contentable_id'] = $content->id;
            }

            $lesson = Lesson::create($lessonData);

            DB::commit();

            return redirect()->route('admin.lessons.show', $lesson)
                ->with('success', 'Lesson and content created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create lesson: ' . $e->getMessage());
        }
    }

    /**
     * Create content based on type
     */
    private function createContent(Request $request, string $contentType)
    {
        switch ($contentType) {
            case 'video':
                $videoData = [
                    'transcript' => $request->input('video_transcript'),
                ];

                // Check if uploading video file or using URL
                if ($request->input('video_source') === 'upload' && $request->hasFile('video_file')) {
                    $file = $request->file('video_file');
                    // Store in private storage (not publicly accessible)
                    $path = $file->store('lessons/videos', 'local');

                    $videoData['file_path'] = $path;
                    $videoData['file_name'] = $file->getClientOriginalName();
                    $videoData['file_type'] = $file->getClientOriginalExtension();
                    $videoData['file_size'] = $file->getSize();
                    $videoData['source'] = 'upload';
                } else {
                    // Using external URL
                    $videoData['url'] = $request->input('video_url');
                    $videoData['vimeo_id'] = $request->input('vimeo_id');
                    $videoData['source'] = 'url';
                }

                return VideoContent::create($videoData);

            case 'text':
                return TextContent::create([
                    'body' => $request->input('text_body'),
                    'reading_time' => $this->calculateReadingTime($request->input('text_body')),
                ]);

            case 'document':
                if ($request->hasFile('document_file')) {
                    $file = $request->file('document_file');
                    // Store in private storage (not publicly accessible)
                    $path = $file->store('lessons/documents', 'local');
                    return DocumentContent::create([
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientOriginalExtension(),
                    ]);
                }
                break;

            case 'audio':
                if ($request->hasFile('audio_file')) {
                    $file = $request->file('audio_file');
                    // Store in private storage (not publicly accessible)
                    $path = $file->store('lessons/audio', 'local');
                    return AudioContent::create([
                        'file_path' => $path,
                        'transcript' => $request->input('audio_transcript'),
                    ]);
                }
                break;

            case 'presentation':
                if ($request->hasFile('presentation_file')) {
                    $file = $request->file('presentation_file');
                    // Store in private storage (not publicly accessible)
                    $path = $file->store('lessons/presentations', 'local');
                    return PresentationContent::create([
                        'file_path' => $path,
                    ]);
                }
                break;

            case 'embed':
                return EmbedContent::create([
                    'embed_url' => $request->input('embed_url'),
                    'provider' => $request->input('embed_provider'),
                    'metadata' => [
                        'embed_code' => $request->input('embed_code'),
                    ],
                ]);
        }

        return null;
    }

    /**
     * Update content based on type
     */
    private function updateContent(Request $request, Lesson $lesson)
    {
        $content = $lesson->contentable;
        if (!$content) {
            return;
        }

        switch ($lesson->content_type) {
            case 'video':
                $videoData = [
                    'transcript' => $request->input('video_transcript'),
                ];

                // Check if uploading new video file
                if ($request->input('video_source') === 'upload' && $request->hasFile('video_file')) {
                    // Delete old file if exists (check both local and public disks)
                    if ($content->file_path) {
                        Storage::disk('local')->delete($content->file_path);
                        Storage::disk('public')->delete($content->file_path);
                    }

                    $file = $request->file('video_file');
                    // Store in private storage (not publicly accessible)
                    $path = $file->store('lessons/videos', 'local');

                    $videoData['file_path'] = $path;
                    $videoData['file_name'] = $file->getClientOriginalName();
                    $videoData['file_type'] = $file->getClientOriginalExtension();
                    $videoData['file_size'] = $file->getSize();
                    $videoData['source'] = 'upload';
                    // Clear URL fields
                    $videoData['url'] = null;
                    $videoData['vimeo_id'] = null;
                } elseif ($request->input('video_source') === 'url' || $request->filled('video_url')) {
                    // Using external URL
                    $videoData['url'] = $request->input('video_url');
                    $videoData['vimeo_id'] = $request->input('vimeo_id');
                    $videoData['source'] = 'url';
                    // Clear file fields if switching from upload to URL
                    if ($content->source === 'upload' && $content->file_path) {
                        Storage::disk('local')->delete($content->file_path);
                        Storage::disk('public')->delete($content->file_path);
                        $videoData['file_path'] = null;
                        $videoData['file_name'] = null;
                        $videoData['file_type'] = null;
                        $videoData['file_size'] = null;
                    }
                }

                $content->update($videoData);
                break;

            case 'text':
                $content->update([
                    'body' => $request->input('text_body'),
                    'reading_time' => $this->calculateReadingTime($request->input('text_body')),
                ]);
                break;

            case 'document':
                if ($request->hasFile('document_file')) {
                    // Delete old file (check both disks)
                    if ($content->file_path) {
                        Storage::disk('local')->delete($content->file_path);
                        Storage::disk('public')->delete($content->file_path);
                    }

                    $file = $request->file('document_file');
                    // Store in private storage
                    $path = $file->store('lessons/documents', 'local');
                    $content->update([
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientOriginalExtension(),
                    ]);
                }
                break;

            case 'audio':
                $updateData = [];
                if ($request->hasFile('audio_file')) {
                    // Delete old file (check both disks)
                    if ($content->file_path) {
                        Storage::disk('local')->delete($content->file_path);
                        Storage::disk('public')->delete($content->file_path);
                    }

                    $file = $request->file('audio_file');
                    // Store in private storage
                    $path = $file->store('lessons/audio', 'local');
                    $updateData['file_path'] = $path;
                }
                if ($request->filled('audio_transcript')) {
                    $updateData['transcript'] = $request->input('audio_transcript');
                }
                if (!empty($updateData)) {
                    $content->update($updateData);
                }
                break;

            case 'presentation':
                if ($request->hasFile('presentation_file')) {
                    // Delete old file (check both disks)
                    if ($content->file_path) {
                        Storage::disk('local')->delete($content->file_path);
                        Storage::disk('public')->delete($content->file_path);
                    }

                    $file = $request->file('presentation_file');
                    // Store in private storage
                    $path = $file->store('lessons/presentations', 'local');
                    $content->update([
                        'file_path' => $path,
                    ]);
                }
                break;

            case 'embed':
                $content->update([
                    'embed_url' => $request->input('embed_url'),
                    'provider' => $request->input('embed_provider'),
                    'metadata' => [
                        'embed_code' => $request->input('embed_code'),
                    ],
                ]);
                break;
        }
    }

    /**
     * Calculate reading time in minutes
     */
    private function calculateReadingTime(string $text): int
    {
        $wordCount = str_word_count(strip_tags($text));
        return max(1, ceil($wordCount / 200)); // Average reading speed: 200 words/min
    }

    /**
     * Display the specified resource.
     */
    public function show(Lesson $lesson)
    {
        $lesson->load([
            'topic.course',
            'contentable',
            'progress.user',
        ]);

        // Load top-level comments with their replies and users for threaded display
        $lessonComments = $lesson->comments()
            ->with(['user', 'replies.user'])
            ->whereNull('parent_id')
            ->latest()
            ->get();

        return view('admin.lessons.show', compact('lesson', 'lessonComments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lesson $lesson)
    {
        $lesson->load(['topic.course', 'contentable']);
        $topics = Topic::with('course')->orderBy('title')->get();

        return view('admin.lessons.edit', compact('lesson', 'topics'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lesson $lesson)
    {
        // Base validation
        $rules = [
            'topic_id' => 'required|exists:topics,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:0|max:9999',
            'order' => 'required|integer|min:0',
            'is_preview' => 'boolean',
            'is_published' => 'boolean',
            'requires_previous_completion' => 'boolean',
        ];

        // Add content-specific validation only if content fields are being updated
        if ($request->filled('update_content')) {
            $contentType = $lesson->content_type;
            switch ($contentType) {
                case 'video':
                    $rules['video_url'] = 'nullable|url';
                    $rules['vimeo_id'] = 'nullable|string';
                    $rules['video_transcript'] = 'nullable|string';
                    $rules['video_file'] = 'nullable|file|mimes:mp4,mov,avi,webm,mkv|max:524288'; // 512MB max
                    break;
                case 'text':
                    $rules['text_body'] = 'required|string';
                    break;
                case 'document':
                    $rules['document_file'] = 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:51200';
                    break;
                case 'audio':
                    $rules['audio_file'] = 'nullable|file|mimes:mp3,wav,ogg,m4a|max:102400';
                    $rules['audio_transcript'] = 'nullable|string';
                    break;
                case 'presentation':
                    $rules['presentation_file'] = 'nullable|file|mimes:pdf,ppt,pptx|max:102400';
                    break;
                case 'embed':
                    $rules['embed_url'] = 'required|url';
                    $rules['embed_provider'] = 'nullable|string';
                    break;
            }
        }

        $validated = $request->validate($rules, [
            'topic_id.required' => 'Please select a topic for this lesson.',
            'title.required' => 'The lesson title is required.',
            'text_body.required' => 'Text content is required for text lessons.',
            'embed_url.required' => 'Please provide an embed URL.',
        ]);

        DB::beginTransaction();
        try {
            // Update lesson data
            $lessonData = [
                'topic_id' => $validated['topic_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'duration_minutes' => $validated['duration_minutes'] ?? 0,
                'order' => $validated['order'],
                'is_preview' => $request->has('is_preview'),
                'is_published' => $request->has('is_published'),
                'requires_previous_completion' => $request->has('requires_previous_completion'),
            ];

            $lesson->update($lessonData);

            // Update content if requested
            if ($request->filled('update_content') && $lesson->contentable) {
                $this->updateContent($request, $lesson);
            }

            DB::commit();

            return redirect()->route('admin.lessons.show', $lesson)
                ->with('success', 'Lesson updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update lesson: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lesson $lesson)
    {
        $topicTitle = $lesson->topic->title;
        $lesson->delete();

        return redirect()->route('admin.lessons.index')
            ->with('success', "Lesson deleted from {$topicTitle} successfully");
    }

    /**
     * Display trashed lessons
     */
    public function trash()
    {
        $lessons = Lesson::onlyTrashed()
            ->with(['topic.course', 'contentable'])
            ->withCount(['progress', 'comments'])
            ->latest('deleted_at')
            ->paginate(20);

        return view('admin.lessons.trash', compact('lessons'));
    }

    /**
     * Restore a trashed lesson
     */
    public function restore($id)
    {
        $lesson = Lesson::onlyTrashed()->findOrFail($id);
        $lesson->restore();

        return redirect()->route('admin.lessons.trash')
            ->with('success', 'Lesson restored successfully');
    }

    /**
     * Permanently delete a lesson
     */
    public function forceDelete($id)
    {
        $lesson = Lesson::onlyTrashed()->findOrFail($id);
        $lesson->forceDelete();

        return redirect()->route('admin.lessons.trash')
            ->with('success', 'Lesson permanently deleted');
    }
}
