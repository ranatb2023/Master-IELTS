<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;

class QuizPolicy
{
    /**
     * Determine if the user can view any quizzes
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the quiz
     */
    public function view(User $user, Quiz $quiz): bool
    {
        // Super admins can view all quizzes
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Tutors can view quizzes for their courses
        if ($user->hasRole('tutor') && $quiz->course->instructor_id === $user->id) {
            return true;
        }

        // Students can view active quizzes if enrolled in the course
        if ($user->hasRole('student')) {
            return $quiz->is_active &&
                   $user->enrollments()
                       ->where('course_id', $quiz->course_id)
                       ->where('status', 'active')
                       ->exists();
        }

        return false;
    }

    /**
     * Determine if the user can create quizzes
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['super_admin', 'tutor']);
    }

    /**
     * Determine if the user can update the quiz
     */
    public function update(User $user, Quiz $quiz): bool
    {
        // Super admins can update any quiz
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Tutors can update quizzes for their courses
        return $user->hasRole('tutor') && $quiz->course->instructor_id === $user->id;
    }

    /**
     * Determine if the user can delete the quiz
     */
    public function delete(User $user, Quiz $quiz): bool
    {
        // Super admins can delete any quiz
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Tutors can delete quizzes for their courses if no attempts exist
        if ($user->hasRole('tutor') && $quiz->course->instructor_id === $user->id) {
            return $quiz->attempts()->count() === 0;
        }

        return false;
    }

    /**
     * Determine if the user can attempt the quiz
     */
    public function attempt(User $user, Quiz $quiz): bool
    {
        // Only students can attempt quizzes
        if (!$user->hasRole('student')) {
            return false;
        }

        // Quiz must be active
        if (!$quiz->is_active) {
            return false;
        }

        // User must be enrolled in the course
        $enrollment = $user->enrollments()
            ->where('course_id', $quiz->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return false;
        }

        // Check max attempts
        if ($quiz->max_attempts) {
            $attemptCount = $enrollment->quizAttempts()
                ->where('quiz_id', $quiz->id)
                ->count();

            return $attemptCount < $quiz->max_attempts;
        }

        return true;
    }

    /**
     * Determine if the user can view quiz results
     */
    public function viewResults(User $user, Quiz $quiz): bool
    {
        // Super admins can view all results
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Tutors can view results for their courses
        if ($user->hasRole('tutor') && $quiz->course->instructor_id === $user->id) {
            return true;
        }

        // Students can view their own results
        if ($user->hasRole('student')) {
            return $user->enrollments()
                ->where('course_id', $quiz->course_id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine if the user can manage quiz questions
     */
    public function manageQuestions(User $user, Quiz $quiz): bool
    {
        // Super admins can manage all questions
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Tutors can manage questions for their courses
        return $user->hasRole('tutor') && $quiz->course->instructor_id === $user->id;
    }
}
