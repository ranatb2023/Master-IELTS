<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonComment;
use App\Notifications\LessonCommentReplyNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonCommentController extends Controller
{
    /**
     * Display a listing of lesson comments for tutor's courses
     */
    public function index(Request $request)
    {
        // Get tutor's course IDs
        $tutorCourseIds = Auth::user()->createdCourses()->pluck('id');

        $query = LessonComment::with(['lesson.topic.course', 'user', 'replies'])
            ->topLevel()
            ->recent()
            ->whereHas('lesson.topic.course', function ($q) use ($tutorCourseIds) {
                $q->whereIn('id', $tutorCourseIds);
            });

        // Apply filters
        if ($request->filled('course_id')) {
            // Verify the course belongs to this tutor
            if (in_array($request->course_id, $tutorCourseIds->toArray())) {
                $query->forCourse($request->course_id);
            }
        }

        if ($request->filled('lesson_id')) {
            $query->where('lesson_id', $request->lesson_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('pinned')) {
            $query->where('is_pinned', $request->pinned === 'true');
        }

        $comments = $query->paginate(20);

        // Get tutor's courses for filter dropdown
        $courses = Auth::user()->createdCourses()->orderBy('title')->get();

        return view('tutor.lesson-comments.index', compact('comments', 'courses'));
    }

    /**
     * Store a new reply on a lesson
     */
    public function store(Request $request, Lesson $lesson)
    {
        // Verify the lesson belongs to tutor's course
        $course = $lesson->topic->course;
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'You can only reply to comments on your own courses.');
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:lesson_comments,id',
        ]);

        // Create the reply
        $comment = LessonComment::create([
            'lesson_id' => $lesson->id,
            'user_id' => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'comment' => $validated['comment'],
            'is_from_tutor' => true,
        ]);

        $comment->load('user');

        // If this is a reply to a student comment, notify the student
        if ($comment->parent_id) {
            $originalComment = LessonComment::find($comment->parent_id);
            if ($originalComment && $originalComment->user_id !== Auth::id()) {
                $originalComment->user->notify(
                    new LessonCommentReplyNotification($lesson, $comment, $originalComment)
                );
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reply posted successfully.',
                'comment' => $comment,
            ]);
        }

        return redirect()->back()->with('success', 'Reply posted successfully.');
    }

    /**
     * Update a comment (tutor can only edit their own replies)
     */
    public function update(Request $request, LessonComment $comment)
    {
        // Check if this is tutor's own comment
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'You can only edit your own replies.');
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $comment->update([
            'comment' => $validated['comment'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reply updated successfully.',
                'comment' => $comment,
            ]);
        }

        return redirect()->back()->with('success', 'Reply updated successfully.');
    }

    /**
     * Delete a comment (tutor can only delete their own replies)
     */
    public function destroy(LessonComment $comment)
    {
        // Check if this is tutor's own comment
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'You can only delete your own replies.');
        }

        $comment->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reply deleted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Reply deleted successfully.');
    }

    /**
     * Toggle pin status of a comment (only on tutor's courses)
     */
    public function togglePin(LessonComment $comment)
    {
        // Verify the comment's lesson belongs to tutor's course
        $course = $comment->lesson->topic->course;
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'You can only pin comments on your own courses.');
        }

        $comment->update([
            'is_pinned' => !$comment->is_pinned,
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_pinned' => $comment->is_pinned,
                'message' => $comment->is_pinned ? 'Comment pinned.' : 'Comment unpinned.',
            ]);
        }

        return redirect()->back()->with('success', $comment->is_pinned ? 'Comment pinned.' : 'Comment unpinned.');
    }
}
