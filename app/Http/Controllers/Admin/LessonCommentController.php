<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonComment;
use App\Notifications\LessonCommentReplyNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonCommentController extends Controller
{
    /**
     * Display a listing of all lesson comments
     */
    public function index(Request $request)
    {
        $query = LessonComment::with(['lesson.topic.course', 'user', 'replies'])
            ->topLevel()
            ->recent();

        // Apply filters
        if ($request->filled('course_id')) {
            $query->forCourse($request->course_id);
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

        // Get all courses for filter dropdown
        $courses = \App\Models\Course::orderBy('title')->get();

        return view('admin.lesson-comments.index', compact('comments', 'courses'));
    }

    /**
     * Store a new comment/reply on a lesson
     */
    public function store(Request $request, Lesson $lesson)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:lesson_comments,id',
        ]);

        // Create the comment/reply
        $comment = LessonComment::create([
            'lesson_id' => $lesson->id,
            'user_id' => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'comment' => $validated['comment'],
            'is_from_tutor' => true, // Admin replies are marked as tutor
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
     * Update a comment
     */
    public function update(Request $request, LessonComment $comment)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $comment->update([
            'comment' => $validated['comment'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment updated successfully.',
                'comment' => $comment,
            ]);
        }

        return redirect()->back()->with('success', 'Comment updated successfully.');
    }

    /**
     * Delete a comment
     */
    public function destroy(LessonComment $comment)
    {
        $comment->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Comment deleted successfully.');
    }

    /**
     * Toggle pin status of a comment
     */
    public function togglePin(LessonComment $comment)
    {
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
