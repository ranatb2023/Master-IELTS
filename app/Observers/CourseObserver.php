<?php

namespace App\Observers;

use App\Models\Course;
use Illuminate\Support\Str;

class CourseObserver
{
    /**
     * Handle the Course "creating" event.
     */
    public function creating(Course $course): void
    {
        // Auto-generate slug if not provided
        if (empty($course->slug)) {
            $course->slug = Str::slug($course->title);

            // Ensure uniqueness
            $originalSlug = $course->slug;
            $count = 1;
            while (Course::where('slug', $course->slug)->exists()) {
                $course->slug = $originalSlug . '-' . $count;
                $count++;
            }
        }

        // Set default values
        if (!isset($course->enrolled_count)) {
            $course->enrolled_count = 0;
        }

        if (!isset($course->average_rating)) {
            $course->average_rating = 0;
        }

        if (!isset($course->total_reviews)) {
            $course->total_reviews = 0;
        }
    }

    /**
     * Handle the Course "created" event.
     */
    public function created(Course $course): void
    {
        // Log activity
        activity()
            ->performedOn($course)
            ->causedBy($course->instructor)
            ->log('Course created');
    }

    /**
     * Handle the Course "updated" event.
     */
    public function updated(Course $course): void
    {
        // If status changed to published, set published_at
        if ($course->isDirty('status') && $course->status === 'published' && !$course->published_at) {
            $course->published_at = now();
            $course->saveQuietly(); // Save without triggering events again
        }

        // Log activity
        activity()
            ->performedOn($course)
            ->causedBy(auth()->user())
            ->log('Course updated');
    }

    /**
     * Handle the Course "deleting" event.
     * This fires before the course is deleted (both soft and hard delete)
     */
    public function deleting(Course $course): void
    {
        // Only cascade delete related data when force deleting (hard delete)
        // For soft delete, we don't delete anything - relationships remain intact
        if ($course->isForceDeleting()) {
            // Hard delete: Remove all related records permanently
            foreach ($course->topics as $topic) {
                $topic->forceDelete();
            }

            $course->enrollments()->forceDelete();
            $course->reviews()->forceDelete();
        }
    }

    /**
     * Handle the Course "deleted" event.
     * This fires after the course is deleted (soft delete)
     */
    public function deleted(Course $course): void
    {
        // If this was a soft delete (not force delete), cascade soft delete to topics
        if ($course->trashed()) {
            foreach ($course->topics as $topic) {
                if (!$topic->trashed()) {
                    $topic->delete(); // Soft delete the topic
                }
            }
        }

        // Log activity
        activity()
            ->performedOn($course)
            ->causedBy(auth()->user())
            ->log('Course deleted');
    }

    /**
     * Handle the Course "restoring" event.
     * This fires before the course is restored
     */
    public function restoring(Course $course): void
    {
        // When restoring a course, also restore all its soft-deleted topics
        $course->topics()->onlyTrashed()->each(function ($topic) {
            $topic->restore();
        });
    }

    /**
     * Handle the Course "restored" event.
     */
    public function restored(Course $course): void
    {
        // Log activity
        activity()
            ->performedOn($course)
            ->causedBy(auth()->user())
            ->log('Course restored');
    }
}
