<?php

namespace App\Observers;

use App\Models\Topic;

class TopicObserver
{
    /**
     * Handle the Topic "deleting" event.
     * This fires before the topic is deleted (both soft and hard delete)
     */
    public function deleting(Topic $topic): void
    {
        // Only cascade delete when force deleting (hard delete)
        // For soft delete, relationships remain intact
        if ($topic->isForceDeleting()) {
            // Hard delete: Remove all related records permanently
            foreach ($topic->lessons as $lesson) {
                $lesson->forceDelete();
            }

            foreach ($topic->quizzes as $quiz) {
                $quiz->forceDelete();
            }

            foreach ($topic->assignments as $assignment) {
                $assignment->forceDelete();
            }
        }
    }

    /**
     * Handle the Topic "restoring" event.
     * This fires before the topic is restored
     */
    public function restoring(Topic $topic): void
    {
        // When restoring a topic, also restore all its soft-deleted lessons
        $topic->lessons()->onlyTrashed()->each(function ($lesson) {
            $lesson->restore();
        });

        // Note: Quizzes and assignments don't have soft deletes by default
        // Only restore if they use SoftDeletes trait
    }

    /**
     * Handle the Topic "deleted" event.
     * This fires after the topic is deleted (soft delete)
     */
    public function deleted(Topic $topic): void
    {
        // If this was a soft delete (not force delete), cascade soft delete to lessons
        if ($topic->trashed()) {
            foreach ($topic->lessons as $lesson) {
                if (!$lesson->trashed()) {
                    $lesson->delete(); // Soft delete the lesson
                }
            }

            // Note: Quizzes and assignments might not have soft deletes
            // Only soft delete if they use SoftDeletes trait
        }

        \Log::info('Topic deleted successfully', [
            'topic_id' => $topic->id,
            'title' => $topic->title,
        ]);
    }

    /**
     * Handle the Topic "forceDeleting" event.
     */
    public function forceDeleting(Topic $topic): void
    {
        // Same cleanup as deleting
        $this->deleting($topic);
    }
}
