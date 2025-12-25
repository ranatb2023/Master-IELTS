<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Topic;
use App\Models\Lesson;
use App\Models\VideoContent;
use App\Models\TextContent;
use App\Models\DocumentContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LessonController extends Controller
{
    /**
     * Display lessons for a topic
     */
    public function index(Course $course, Topic $topic)
    {
        // Ensure tutor owns this course
        $this->authorize('update', $course);

        if ($topic->course_id !== $course->id) {
            abort(404);
        }

        $lessons = $topic->lessons()
            ->with('content')
            ->orderBy('order')
            ->get();

        return view('tutor.lessons.index', compact('course', 'topic', 'lessons'));
    }

    /**
     * Show form to create lesson
     */
    public function create(Course $course, Topic $topic)
    {
        // Ensure tutor owns this course
        $this->authorize('update', $course);

        if ($topic->course_id !== $course->id) {
            abort(404);
        }

        return view('tutor.lessons.create', compact('course', 'topic'));
    }

    /**
     * Store a new lesson
     */
    public function store(Request $request, Course $course, Topic $topic)
    {
        // Ensure tutor owns this course
        $this->authorize('update', $course);

        if ($topic->course_id !== $course->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content_type' => 'required|in:video,text,document,audio,presentation,embed',
            'duration_minutes' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:0',
            'is_free_preview' => 'boolean',

            // Content type specific fields
            'video_url' => 'required_if:content_type,video|nullable|string',
            'video_provider' => 'required_if:content_type,video|nullable|in:youtube,vimeo,self_hosted',
            'text_content' => 'required_if:content_type,text|nullable|string',
            'document_url' => 'required_if:content_type,document|nullable|string',
            'document_type' => 'required_if:content_type,document|nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Set order if not provided
            if (!isset($validated['order'])) {
                $validated['order'] = $topic->lessons()->max('order') + 1;
            }

            // Create lesson
            $lesson = $topic->lessons()->create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'content_type' => $validated['content_type'],
                'duration_minutes' => $validated['duration_minutes'] ?? 0,
                'order' => $validated['order'],
                'is_free_preview' => $validated['is_free_preview'] ?? false,
            ]);

            // Create content based on type
            $this->createLessonContent($lesson, $validated);

            DB::commit();

            return redirect()
                ->route('tutor.courses.topics.lessons.index', [$course, $topic])
                ->with('success', 'Lesson created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create lesson: ' . $e->getMessage());
        }
    }

    /**
     * Show lesson details
     */
    public function show(Course $course, Topic $topic, Lesson $lesson)
    {
        // Ensure tutor owns this course
        $this->authorize('update', $course);

        if ($topic->course_id !== $course->id || $lesson->topic_id !== $topic->id) {
            abort(404);
        }

        $lesson->load('contentable');

        // Load top-level comments with their replies and users for threaded display
        $lessonComments = $lesson->comments()
            ->with(['user', 'replies.user'])
            ->whereNull('parent_id')
            ->latest()
            ->get();

        return view('tutor.lessons.show', compact('course', 'topic', 'lesson', 'lessonComments'));
    }

    /**
     * Show form to edit lesson
     */
    public function edit(Course $course, Topic $topic, Lesson $lesson)
    {
        // Ensure tutor owns this course
        $this->authorize('update', $course);

        if ($topic->course_id !== $course->id || $lesson->topic_id !== $topic->id) {
            abort(404);
        }

        $lesson->load('content');

        return view('tutor.lessons.edit', compact('course', 'topic', 'lesson'));
    }

    /**
     * Update lesson
     */
    public function update(Request $request, Course $course, Topic $topic, Lesson $lesson)
    {
        // Ensure tutor owns this course
        $this->authorize('update', $course);

        if ($topic->course_id !== $course->id || $lesson->topic_id !== $topic->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content_type' => 'required|in:video,text,document,audio,presentation,embed',
            'duration_minutes' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:0',
            'is_free_preview' => 'boolean',

            // Content type specific fields
            'video_url' => 'required_if:content_type,video|nullable|string',
            'video_provider' => 'required_if:content_type,video|nullable|in:youtube,vimeo,self_hosted',
            'text_content' => 'required_if:content_type,text|nullable|string',
            'document_url' => 'required_if:content_type,document|nullable|string',
            'document_type' => 'required_if:content_type,document|nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update lesson
            $lesson->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'content_type' => $validated['content_type'],
                'duration_minutes' => $validated['duration_minutes'] ?? $lesson->duration_minutes,
                'order' => $validated['order'] ?? $lesson->order,
                'is_free_preview' => $validated['is_free_preview'] ?? false,
            ]);

            // Delete old content if content type changed
            if ($lesson->wasChanged('content_type')) {
                $lesson->content()->delete();
            }

            // Update or create content
            $this->updateLessonContent($lesson, $validated);

            DB::commit();

            return redirect()
                ->route('tutor.courses.topics.lessons.show', [$course, $topic, $lesson])
                ->with('success', 'Lesson updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update lesson: ' . $e->getMessage());
        }
    }

    /**
     * Delete lesson
     */
    public function destroy(Course $course, Topic $topic, Lesson $lesson)
    {
        // Ensure tutor owns this course
        $this->authorize('update', $course);

        if ($topic->course_id !== $course->id || $lesson->topic_id !== $topic->id) {
            abort(404);
        }

        DB::beginTransaction();
        try {
            $lesson->content()->delete();
            $lesson->delete();

            DB::commit();

            return redirect()
                ->route('tutor.courses.topics.lessons.index', [$course, $topic])
                ->with('success', 'Lesson deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete lesson: ' . $e->getMessage());
        }
    }

    /**
     * Reorder lessons
     */
    public function reorder(Request $request, Course $course, Topic $topic)
    {
        // Ensure tutor owns this course
        $this->authorize('update', $course);

        if ($topic->course_id !== $course->id) {
            abort(404);
        }

        $validated = $request->validate([
            'lessons' => 'required|array',
            'lessons.*.id' => 'required|exists:lessons,id',
            'lessons.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['lessons'] as $lessonData) {
            Lesson::where('id', $lessonData['id'])
                ->where('topic_id', $topic->id)
                ->update(['order' => $lessonData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lessons reordered successfully!'
        ]);
    }

    /**
     * Create lesson content based on type
     */
    private function createLessonContent(Lesson $lesson, array $data)
    {
        switch ($lesson->content_type) {
            case 'video':
                VideoContent::create([
                    'lesson_id' => $lesson->id,
                    'video_url' => $data['video_url'],
                    'video_provider' => $data['video_provider'],
                    'duration_seconds' => ($data['duration_minutes'] ?? 0) * 60,
                ]);
                break;

            case 'text':
                TextContent::create([
                    'lesson_id' => $lesson->id,
                    'content' => $data['text_content'],
                ]);
                break;

            case 'document':
                DocumentContent::create([
                    'lesson_id' => $lesson->id,
                    'file_url' => $data['document_url'],
                    'file_type' => $data['document_type'],
                ]);
                break;
        }
    }

    /**
     * Update lesson content based on type
     */
    private function updateLessonContent(Lesson $lesson, array $data)
    {
        $content = $lesson->content;

        switch ($lesson->content_type) {
            case 'video':
                if ($content) {
                    $content->update([
                        'video_url' => $data['video_url'],
                        'video_provider' => $data['video_provider'],
                        'duration_seconds' => ($data['duration_minutes'] ?? 0) * 60,
                    ]);
                } else {
                    $this->createLessonContent($lesson, $data);
                }
                break;

            case 'text':
                if ($content) {
                    $content->update(['content' => $data['text_content']]);
                } else {
                    $this->createLessonContent($lesson, $data);
                }
                break;

            case 'document':
                if ($content) {
                    $content->update([
                        'file_url' => $data['document_url'],
                        'file_type' => $data['document_type'],
                    ]);
                } else {
                    $this->createLessonContent($lesson, $data);
                }
                break;
        }
    }

    /**
     * Display all lessons across all tutor's courses
     */
    public function allLessons(Request $request)
    {
        $query = Lesson::whereHas('topic.course', function ($q) {
            $q->where('instructor_id', auth()->id());
        })
            ->with(['topic.course'])
            ->withCount(['progress']);

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by course
        if ($request->filled('course')) {
            $query->whereHas('topic', function ($q) use ($request) {
                $q->where('course_id', $request->course);
            });
        }

        // Filter by topic
        if ($request->filled('topic')) {
            $query->where('topic_id', $request->topic);
        }

        // Filter by content type
        if ($request->filled('content_type')) {
            $query->where('content_type', $request->content_type);
        }

        $lessons = $query->latest()->paginate(15);

        // Get tutor's courses and topics for filters
        $courses = auth()->user()->createdCourses()->get();
        $topics = Topic::whereHas('course', function ($q) {
            $q->where('instructor_id', auth()->id());
        })->get();

        return view('tutor.lessons.index', compact('lessons', 'courses', 'topics'));
    }

    /**
     * Display trashed lessons
     */
    public function trash(Request $request)
    {
        $query = Lesson::onlyTrashed()
            ->whereHas('topic.course', function ($q) {
                $q->where('instructor_id', auth()->id());
            })
            ->with('topic.course');

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by topic
        if ($request->filled('topic')) {
            $query->where('topic_id', $request->topic);
        }

        $lessons = $query->latest('deleted_at')->paginate(15);

        // Get tutor's topics for filter
        $topics = Topic::whereHas('course', function ($q) {
            $q->where('instructor_id', auth()->id());
        })->get();

        return view('tutor.lessons.trash', compact('lessons', 'topics'));
    }

    /**
     * Restore a trashed lesson
     */
    public function restore($id)
    {
        $lesson = Lesson::onlyTrashed()
            ->whereHas('topic.course', function ($q) {
                $q->where('instructor_id', auth()->id());
            })
            ->findOrFail($id);

        $lesson->restore();

        return redirect()
            ->route('tutor.lessons.trash')
            ->with('success', 'Lesson restored successfully!');
    }

    /**
     * Permanently delete a lesson
     */
    public function forceDelete($id)
    {
        $lesson = Lesson::onlyTrashed()
            ->whereHas('topic.course', function ($q) {
                $q->where('instructor_id', auth()->id());
            })
            ->findOrFail($id);

        // Delete content first
        $lesson->contentable()->forceDelete();
        $lesson->forceDelete();

        return redirect()
            ->route('tutor.lessons.trash')
            ->with('success', 'Lesson permanently deleted!');
    }
}
