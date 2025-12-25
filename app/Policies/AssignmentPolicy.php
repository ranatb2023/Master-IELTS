<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;

class AssignmentPolicy
{
    /**
     * Determine if the user can view any assignments
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the assignment
     */
    public function view(User $user, Assignment $assignment): bool
    {
        // Super admins can view all assignments
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Tutors can view assignments for their courses
        if ($user->hasRole('tutor') && $assignment->topic->course->instructor_id === $user->id) {
            return true;
        }

        // Students can view assignments if enrolled in the course
        if ($user->hasRole('student')) {
            return $user->enrollments()
                ->where('course_id', $assignment->topic->course_id)
                ->where('status', 'active')
                ->exists();
        }

        return false;
    }

    /**
     * Determine if the user can create assignments
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['super_admin', 'tutor']);
    }

    /**
     * Determine if the user can update the assignment
     */
    public function update(User $user, Assignment $assignment): bool
    {
        // Super admins can update any assignment
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Tutors can update assignments for their courses
        return $user->hasRole('tutor') && $assignment->topic->course->instructor_id === $user->id;
    }

    /**
     * Determine if the user can delete the assignment
     */
    public function delete(User $user, Assignment $assignment): bool
    {
        // Super admins can delete any assignment
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Tutors can delete assignments for their courses if no submissions exist
        if ($user->hasRole('tutor') && $assignment->topic->course->instructor_id === $user->id) {
            return $assignment->submissions()->count() === 0;
        }

        return false;
    }

    /**
     * Determine if the user can submit the assignment
     */
    public function submit(User $user, Assignment $assignment): bool
    {
        // Only students can submit assignments
        if (!$user->hasRole('student')) {
            return false;
        }

        // User must be enrolled in the course
        $enrollment = $user->enrollments()
            ->where('course_id', $assignment->topic->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return false;
        }

        // Check if deadline passed and late submission not allowed
        if ($assignment->due_date && now()->gt($assignment->due_date) && !$assignment->allow_late_submission) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the user can grade submissions
     */
    public function grade(User $user, Assignment $assignment): bool
    {
        // Super admins can grade any assignment
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Tutors can grade assignments for their courses
        return $user->hasRole('tutor') && $assignment->topic->course->instructor_id === $user->id;
    }

    /**
     * Determine if the user can view submissions
     */
    public function viewSubmissions(User $user, Assignment $assignment): bool
    {
        // Super admins can view all submissions
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Tutors can view submissions for their courses
        return $user->hasRole('tutor') && $assignment->topic->course->instructor_id === $user->id;
    }
}
