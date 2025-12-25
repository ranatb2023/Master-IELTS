<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\User;

class EnrollmentPolicy
{
    /**
     * Determine if the user can view any enrollments
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super_admin', 'tutor', 'student']);
    }

    /**
     * Determine if the user can view the enrollment
     */
    public function view(User $user, Enrollment $enrollment): bool
    {
        // Super admins can view all enrollments
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Tutors can view enrollments for their courses
        if ($user->hasRole('tutor') && $enrollment->course->instructor_id === $user->id) {
            return true;
        }

        // Students can view their own enrollments
        return $user->hasRole('student') && $enrollment->user_id === $user->id;
    }

    /**
     * Determine if the user can create enrollments
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['super_admin', 'student']);
    }

    /**
     * Determine if the user can update the enrollment
     */
    public function update(User $user, Enrollment $enrollment): bool
    {
        // Super admins can update any enrollment
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Students can update their own enrollments (limited fields)
        return $user->hasRole('student') && $enrollment->user_id === $user->id;
    }

    /**
     * Determine if the user can delete the enrollment
     */
    public function delete(User $user, Enrollment $enrollment): bool
    {
        // Only super admins can delete enrollments
        return $user->hasRole('super_admin');
    }

    /**
     * Determine if the user can extend the enrollment
     */
    public function extend(User $user, Enrollment $enrollment): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine if the user can refund the enrollment
     */
    public function refund(User $user, Enrollment $enrollment): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine if the user can view enrollment progress
     */
    public function viewProgress(User $user, Enrollment $enrollment): bool
    {
        // Super admins can view all progress
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Tutors can view progress for their courses
        if ($user->hasRole('tutor') && $enrollment->course->instructor_id === $user->id) {
            return true;
        }

        // Students can view their own progress
        return $user->hasRole('student') && $enrollment->user_id === $user->id;
    }

    /**
     * Determine if the user can reset enrollment progress
     */
    public function resetProgress(User $user, Enrollment $enrollment): bool
    {
        return $user->hasRole('super_admin');
    }
}
