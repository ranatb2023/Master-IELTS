<?php

namespace App\Notifications;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentGradedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $assignment;
    protected $submission;

    public function __construct(Assignment $assignment, AssignmentSubmission $submission)
    {
        $this->assignment = $assignment;
        $this->submission = $submission;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Assignment Graded',
            'message' => "Your assignment '{$this->assignment->title}' has been graded. Score: {$this->submission->score}/{$this->submission->max_score}",
            'action_url' => route('student.assignments.submission', $this->submission),
            'icon' => 'check-circle',
            'color' => 'green',
            'metadata' => [
                'assignment_id' => $this->assignment->id,
                'submission_id' => $this->submission->id,
                'score' => $this->submission->score,
                'max_score' => $this->submission->max_score,
            ],
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Assignment Graded: ' . $this->assignment->title)
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your assignment '{$this->assignment->title}' has been graded.")
            ->line("Score: {$this->submission->score}/{$this->submission->max_score}")
            ->when($this->submission->feedback, function ($mail) {
                return $mail->line("Feedback: {$this->submission->feedback}");
            })
            ->action('View Results', route('student.assignments.submission', $this->submission->id))
            ->line('Keep up the great work!');
    }
}
