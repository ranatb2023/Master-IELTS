<?php

namespace App\Observers;

use App\Models\Review;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        // Update course rating statistics
        $this->updateCourseRating($review);

        // Log activity
        activity()
            ->performedOn($review)
            ->causedBy($review->user)
            ->withProperties(['rating' => $review->rating])
            ->log('Posted review for course: ' . $review->course->title);

        // TODO: Notify instructor about new review
    }

    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
        // If rating changed, update course statistics
        if ($review->isDirty('rating')) {
            $this->updateCourseRating($review);
        }

        // If status changed to approved
        if ($review->isDirty('status') && $review->status === 'approved') {
            $this->updateCourseRating($review);
        }
    }

    /**
     * Handle the Review "deleted" event.
     */
    public function deleted(Review $review): void
    {
        // Update course rating statistics
        $this->updateCourseRating($review);

        // Log activity
        activity()
            ->performedOn($review)
            ->causedBy(auth()->user())
            ->log('Deleted review');
    }

    /**
     * Update course rating statistics
     */
    private function updateCourseRating(Review $review): void
    {
        $course = $review->course;

        // Calculate new average rating from approved reviews
        $stats = $course->reviews()
            ->where('status', 'approved')
            ->selectRaw('COUNT(*) as total, AVG(rating) as average')
            ->first();

        $course->update([
            'average_rating' => round($stats->average ?? 0, 2),
            'total_reviews' => $stats->total ?? 0,
        ]);
    }
}
