<?php

namespace App\Notifications;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentSubmittedNotification extends Notification implements ShouldQueue
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
            'title' => 'Assignment Submitted',
            'message' => "{$this->submission->user->name} submitted '{$this->assignment->title}'",
            'action_url' => route('tutor.assignments.viewSubmission', [$this->assignment, $this->submission]),
            'icon' => 'document-text',
            'color' => 'green',
            'metadata' => [
                'assignment_id' => $this->assignment->id,
                'submission_id' => $this->submission->id,
                'student_id' => $this->submission->user_id,
            ],
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Assignment Submission')
            ->greeting("Hello {$notifiable->name}!")
            ->line("{$this->submission->user->name} has submitted the assignment '{$this->assignment->title}'.")
            ->line('The submission is ready for your review.')
            ->action('Review Submission', route('tutor.assignments.viewSubmission', [$this->assignment->id, $this->submission->id]))
            ->line('Thank you for your feedback and guidance!');
    }
}
