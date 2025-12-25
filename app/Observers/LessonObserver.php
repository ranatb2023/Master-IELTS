<?php

namespace App\Observers;

use App\Models\Lesson;
use Illuminate\Support\Facades\Storage;

class LessonObserver
{
    /**
     * Handle the Lesson "deleting" event.
     * This fires before the lesson is deleted (both soft and hard delete)
     */
    public function deleting(Lesson $lesson): void
    {
        // Only delete content and files when force deleting (hard delete)
        if ($lesson->isForceDeleting()) {
            // Delete the associated content permanently
            if ($lesson->contentable) {
                $content = $lesson->contentable;

                // Handle file-based content types - delete files from storage
                if (in_array($lesson->content_type, ['document', 'audio', 'presentation', 'video'])) {
                    if (isset($content->file_path) && $content->file_path) {
                        // All protected content (video, audio, document, presentation) are now stored in local (private) disk
                        Storage::disk('local')->delete($content->file_path);
                    }
                }

                // Permanently delete the content record
                $content->forceDelete();
            }

            // Delete related resources permanently
            if ($lesson->resources()->count() > 0) {
                foreach ($lesson->resources as $resource) {
                    // Delete resource files if they exist
                    if ($resource->file_path) {
                        Storage::disk('public')->delete($resource->file_path);
                    }
                    $resource->forceDelete();
                }
            }

            // Delete related comments permanently
            $lesson->comments()->forceDelete();

            // Delete related progress records permanently
            $lesson->progress()->forceDelete();
        }
        // For soft delete, we keep the content and relationships intact
    }

    /**
     * Handle the Lesson "deleted" event.
     */
    public function deleted(Lesson $lesson): void
    {
        \Log::info('Lesson deleted successfully', [
            'lesson_id' => $lesson->id,
            'title' => $lesson->title,
        ]);
    }

    /**
     * Handle the Lesson "forceDeleting" event.
     * This fires when permanently deleting (force delete)
     */
    public function forceDeleting(Lesson $lesson): void
    {
        // Same cleanup as deleting
        $this->deleting($lesson);
    }
}
