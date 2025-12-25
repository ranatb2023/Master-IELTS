<?php

namespace App\Observers;

use App\Models\QuizAttempt;

class QuizAttemptObserver
{
    /**
     * Handle the QuizAttempt "created" event.
     */
    public function created(QuizAttempt $attempt): void
    {
        // Log activity
        activity()
            ->performedOn($attempt)
            ->causedBy($attempt->user)
            ->log('Started quiz: ' . $attempt->quiz->title);
    }

    /**
     * Handle the QuizAttempt "updated" event.
     */
    public function updated(QuizAttempt $attempt): void
    {
        // If status changed to completed
        if ($attempt->isDirty('status') && $attempt->status === 'completed') {
            // Update enrollment progress
            $this->updateEnrollmentProgress($attempt);

            // Log activity
            activity()
                ->performedOn($attempt)
                ->causedBy($attempt->user)
                ->withProperties([
                    'score' => $attempt->score,
                    'passed' => $attempt->passed,
                ])
                ->log('Completed quiz: ' . $attempt->quiz->title);

            // TODO: Send quiz completion notification if configured
        }
    }

    /**
     * Update enrollment progress when quiz is completed
     */
    private function updateEnrollmentProgress(QuizAttempt $attempt): void
    {
        $enrollment = $attempt->enrollment;

        // Count unique quizzes completed
        $completedQuizzes = $enrollment->quizAttempts()
            ->where('status', 'completed')
            ->distinct('quiz_id')
            ->count();

        // Update course progress
        $courseProgress = $enrollment->courseProgress;
        if ($courseProgress) {
            $courseProgress->update([
                'completed_quizzes' => $completedQuizzes,
                'last_activity_at' => now(),
            ]);
        }
    }
}
