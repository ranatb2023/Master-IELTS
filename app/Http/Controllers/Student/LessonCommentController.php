<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonComment;
use App\Models\User;
use App\Notifications\NewLessonCommentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class LessonCommentController extends Controller
{
    /**
     * Store a new comment on a lesson
     */
    public function store(Request $request, Lesson $lesson)
    {
        // Validate the request
        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:lesson_comments,id',
        ]);

        // Eager load relationships
        $lesson->load('topic.course');

        // Check if lesson has topic and course
        if (!$lesson->topic || !$lesson->topic->course) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid lesson structure.'
            ], 400);
        }

        // Check if student is enrolled in the course
        $course = $lesson->topic->course;
        $enrollment = Auth::user()->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'You must be enrolled in this course to comment.'
            ], 403);
        }

        // Create the comment
        $comment = LessonComment::create([
            'lesson_id' => $lesson->id,
            'user_id' => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'comment' => $validated['comment'],
            'is_from_tutor' => false,
        ]);

        // Load the comment with user relationship
        $comment->load('user');

        // Try to send notifications (don't fail the request if this errors)
        try {
            if (!$comment->parent_id) {
                $recipients = [];

                // Add course instructor if they exist
                if ($course->instructor_id) {
                    $instructor = User::find($course->instructor_id);
                    if ($instructor) {
                        $recipients[] = $instructor;
                    }
                }

                // Add all admin users
                $admins = User::role('super_admin')->get();
                foreach ($admins as $admin) {
                    $recipients[] = $admin;
                }

                // Send notifications
                if (!empty($recipients)) {
                    Notification::send($recipients, new NewLessonCommentNotification($lesson, $comment));
                }
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the request
            Log::error('Failed to send comment notification: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Comment posted successfully.',
            'comment' => $comment,
        ]);
    }

    /**
     * Update a comment
     */
    public function update(Request $request, Lesson $lesson, $comment)
    {
        // Find the comment
        $lessonComment = LessonComment::findOrFail($comment);

        // Check authorization
        if ($lessonComment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only edit your own comments.'
            ], 403);
        }

        // Validate the request
        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        // Update the comment
        $lessonComment->update([
            'comment' => $validated['comment'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment updated successfully.',
            'comment' => $lessonComment,
        ]);
    }

    /**
     * Delete a comment
     */
    public function destroy(Lesson $lesson, $comment)
    {
        // Find the comment
        $lessonComment = LessonComment::findOrFail($comment);

        // Check authorization
        if ($lessonComment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only delete your own comments.'
            ], 403);
        }

        // Delete the comment (this will also delete replies due to cascade)
        $lessonComment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully.',
        ]);
    }
}
