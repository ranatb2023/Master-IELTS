<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\NoteAttachment;
use App\Models\Lesson;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NoteController extends Controller
{
    /**
     * Display a listing of notes
     */
    public function index(Request $request)
    {
        $query = Note::with(['lesson', 'course', 'attachments'])
            ->forUser(auth()->user())
            ->recent();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by lesson
        if ($request->filled('lesson_id')) {
            $query->forLesson($request->lesson_id);
        }

        // Filter by course
        if ($request->filled('course_id')) {
            $query->forCourse($request->course_id);
        }

        // Filter by color
        if ($request->filled('color')) {
            $query->byColor($request->color);
        }

        // Filter by tag
        if ($request->filled('tag')) {
            $query->withTag($request->tag);
        }

        // Filter pinned
        if ($request->boolean('pinned')) {
            $query->pinned();
        }

        $notes = $query->paginate(12);

        // Get user's courses and lessons for filters
        $userCourses = auth()->user()
            ->enrollments()
            ->with('course')
            ->get()
            ->pluck('course')
            ->unique('id');

        // Get all unique tags
        $allTags = Note::getTagsForUser(auth()->user());

        return view('student.notes.index', compact('notes', 'userCourses', 'allTags'));
    }

    /**
     * Show the form for creating a new note
     */
    public function create(Request $request)
    {
        $lessonId = $request->query('lesson_id');
        $courseId = $request->query('course_id');

        $lesson = $lessonId ? Lesson::find($lessonId) : null;
        $course = $courseId ? Course::find($courseId) : null;

        // Get user's courses for dropdown
        $userCourses = auth()->user()
            ->enrollments()
            ->with('course')
            ->get()
            ->pluck('course')
            ->unique('id');

        return view('student.notes.create', compact('lesson', 'course', 'userCourses'));
    }

    /**
     * Store a newly created note
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:50000',
            'lesson_id' => 'nullable|exists:lessons,id',
            'course_id' => 'nullable|exists:courses,id',
            'color' => 'nullable|string|in:yellow,green,blue,red,purple,pink,orange,gray',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'is_pinned' => 'boolean',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // 10MB max per file
        ]);

        DB::beginTransaction();
        try {
            // Create note
            $note = Note::create([
                'user_id' => auth()->id(),
                'title' => $validated['title'],
                'body' => $validated['body'],
                'lesson_id' => $validated['lesson_id'] ?? null,
                'course_id' => $validated['course_id'] ?? null,
                'color' => $validated['color'] ?? null,
                'tags' => $validated['tags'] ?? null,
                'is_pinned' => $request->boolean('is_pinned'),
            ]);

            // Handle file attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('notes', $filename, 'private');

                    $note->attachments()->create([
                        'file_name' => $filename,
                        'original_filename' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $file->getClientOriginalExtension(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('student.notes.show', $note)
                ->with('success', 'Note created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create note: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified note
     */
    public function show(Note $note)
    {
        // Authorization check
        if (!$note->canBeViewedBy(auth()->user())) {
            abort(403);
        }

        $note->load(['lesson', 'course', 'attachments']);

        // Get related notes from same lesson
        $relatedNotes = null;
        if ($note->lesson_id) {
            $relatedNotes = Note::forUser(auth()->user())
                ->forLesson($note->lesson_id)
                ->where('id', '!=', $note->id)
                ->recent()
                ->take(5)
                ->get();
        }

        return view('student.notes.show', compact('note', 'relatedNotes'));
    }

    /**
     * Show the form for editing the specified note
     */
    public function edit(Note $note)
    {
        // Authorization check
        if (!$note->canBeEditedBy(auth()->user())) {
            abort(403);
        }

        $note->load('attachments');

        // Get user's courses for dropdown
        $userCourses = auth()->user()
            ->enrollments()
            ->with('course')
            ->get()
            ->pluck('course')
            ->unique('id');

        return view('student.notes.edit', compact('note', 'userCourses'));
    }

    /**
     * Update the specified note
     */
    public function update(Request $request, Note $note)
    {
        // Authorization check
        if (!$note->canBeEditedBy(auth()->user())) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:50000',
            'lesson_id' => 'nullable|exists:lessons,id',
            'course_id' => 'nullable|exists:courses,id',
            'color' => 'nullable|string|in:yellow,green,blue,red,purple,pink,orange,gray',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'is_pinned' => 'boolean',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240',
            'remove_attachments' => 'nullable|array',
            'remove_attachments.*' => 'exists:note_attachments,id',
        ]);

        DB::beginTransaction();
        try {
            // Update note
            $note->update([
                'title' => $validated['title'],
                'body' => $validated['body'],
                'lesson_id' => $validated['lesson_id'] ?? null,
                'course_id' => $validated['course_id'] ?? null,
                'color' => $validated['color'] ?? null,
                'tags' => $validated['tags'] ?? null,
                'is_pinned' => $request->boolean('is_pinned'),
            ]);

            // Remove specified attachments
            if ($request->filled('remove_attachments')) {
                foreach ($request->remove_attachments as $attachmentId) {
                    $attachment = NoteAttachment::where('note_id', $note->id)
                        ->where('id', $attachmentId)
                        ->first();

                    if ($attachment) {
                        // Delete file from storage
                        Storage::disk('private')->delete($attachment->file_path);
                        $attachment->delete();
                    }
                }
            }

            // Handle new file attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('notes', $filename, 'private');

                    $note->attachments()->create([
                        'file_name' => $filename,
                        'original_filename' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $file->getClientOriginalExtension(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('student.notes.show', $note)
                ->with('success', 'Note updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update note: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete the specified note
     */
    public function destroy(Note $note)
    {
        // Authorization check
        if (!$note->canBeDeletedBy(auth()->user())) {
            abort(403);
        }

        $note->delete();

        return redirect()
            ->route('student.notes.index')
            ->with('success', 'Note moved to trash.');
    }

    /**
     * Toggle pin status
     */
    public function togglePin(Note $note)
    {
        // Authorization check
        if (!$note->canBeEditedBy(auth()->user())) {
            abort(403);
        }

        $note->is_pinned = !$note->is_pinned;
        $note->save();

        return back()->with('success', $note->is_pinned ? 'Note pinned' : 'Note unpinned');
    }

    /**
     * Show trashed notes
     */
    public function trashed()
    {
        $notes = Note::onlyTrashed()
            ->forUser(auth()->user())
            ->recent()
            ->paginate(12);

        return view('student.notes.trashed', compact('notes'));
    }

    /**
     * Restore a trashed note
     */
    public function restore($id)
    {
        $note = Note::onlyTrashed()
            ->where('id', $id)
            ->forUser(auth()->user())
            ->firstOrFail();

        $note->restore();

        return redirect()
            ->route('student.notes.index')
            ->with('success', 'Note restored successfully.');
    }

    /**
     * Permanently delete a note
     */
    public function forceDelete($id)
    {
        $note = Note::onlyTrashed()
            ->where('id', $id)
            ->forUser(auth()->user())
            ->firstOrFail();

        // Delete all attachments from storage
        foreach ($note->attachments as $attachment) {
            Storage::disk('private')->delete($attachment->file_path);
        }

        $note->forceDelete();

        return redirect()
            ->route('student.notes.trashed')
            ->with('success', 'Note permanently deleted.');
    }

    /**
     * Download note attachment
     */
    public function downloadAttachment(Note $note, NoteAttachment $attachment)
    {
        // Authorization check
        if (!$note->canBeViewedBy(auth()->user()) || $attachment->note_id !== $note->id) {
            abort(403);
        }

        $path = storage_path('app/private/' . $attachment->file_path);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path, $attachment->original_filename ?? $attachment->file_name);
    }

    /**
     * View/preview note attachment
     */
    public function viewAttachment(Note $note, NoteAttachment $attachment)
    {
        // Authorization check
        if (!$note->canBeViewedBy(auth()->user()) || $attachment->note_id !== $note->id) {
            abort(403);
        }

        $path = storage_path('app/private/' . $attachment->file_path);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => $attachment->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . ($attachment->original_filename ?? $attachment->file_name) . '"'
        ]);
    }
}
