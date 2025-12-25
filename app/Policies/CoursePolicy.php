<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Determine if the user can view any courses
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the course
     */
    public function view(User $user, Course $course): bool
    {
        // Super admins can view all courses
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Tutors can view their own courses
        if ($user->hasRole('tutor') && $course->instructor_id === $user->id) {
            return true;
        }

        // Students can view published courses or courses they're enrolled in
        if ($user->hasRole('student')) {
            return $course->status === 'published' ||
                   $user->enrollments()->where('course_id', $course->id)->exists();
        }

        return false;
    }

    /**
     * Determine if the user can create courses
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['super_admin', 'tutor']);
    }

    /**
     * Determine if the user can update the course
     */
    public function update(User $user, Course $course): bool
    {
        // Super admins can update any course
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Tutors can only update their own courses
        return $user->hasRole('tutor') && $course->instructor_id === $user->id;
    }

    /**
     * Determine if the user can delete the course
     */
    public function delete(User $user, Course $course): bool
    {
        // Super admins can delete any course
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Tutors can only delete their own courses if they have no enrollments
        if ($user->hasRole('tutor') && $course->instructor_id === $user->id) {
            return $course->enrollments()->count() === 0;
        }

        return false;
    }

    /**
     * Determine if the user can restore the course
     */
    public function restore(User $user, Course $course): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine if the user can permanently delete the course
     */
    public function forceDelete(User $user, Course $course): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine if the user can publish the course
     */
    public function publish(User $user, Course $course): bool
    {
        // Super admins can publish any course
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Tutors can publish their own courses
        return $user->hasRole('tutor') && $course->instructor_id === $user->id;
    }

    /**
     * Determine if the user can manage course content (topics, lessons)
     */
    public function manageContent(User $user, Course $course): bool
    {
        // Super admins can manage any course content
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Tutors can manage their own course content
        return $user->hasRole('tutor') && $course->instructor_id === $user->id;
    }

    /**
     * Determine if the user can view course analytics
     */
    public function viewAnalytics(User $user, Course $course): bool
    {
        // Super admins can view all analytics
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Tutors can view their own course analytics
        return $user->hasRole('tutor') && $course->instructor_id === $user->id;
    }

    /**
     * Determine if the user can enroll in the course
     */
    public function enroll(User $user, Course $course): bool
    {
        // Only students can enroll
        if (!$user->hasRole('student')) {
            return false;
        }

        // Course must be published
        if ($course->status !== 'published') {
            return false;
        }

        // User must not be already enrolled
        if ($user->enrollments()->where('course_id', $course->id)->exists()) {
            return false;
        }

        return true;
    }
}
