<?php

namespace App\Notifications;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentDueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $assignment;

    public function __construct(Assignment $assignment)
    {
        $this->assignment = $assignment;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        $dueDate = $this->assignment->due_date->format('M d, Y');

        return [
            'title' => 'Assignment Due Soon',
            'message' => "{$this->assignment->title} is due on {$dueDate}",
            'action_url' => route('student.assignments.show', $this->assignment),
            'icon' => 'clock',
            'color' => 'yellow',
            'metadata' => [
                'assignment_id' => $this->assignment->id,
                'due_date' => $this->assignment->due_date,
            ],
        ];
    }

    public function toMail($notifiable)
    {
        $dueDate = $this->assignment->due_date->format('M d, Y');

        return (new MailMessage)
            ->subject('Assignment Due Soon: ' . $this->assignment->title)
            ->greeting("Hello {$notifiable->name}!")
            ->line("This is a reminder that your assignment '{$this->assignment->title}' is due on {$dueDate}.")
            ->line('Please submit your work before the deadline to avoid late penalties.')
            ->action('View Assignment', route('student.assignments.show', $this->assignment->id))
            ->line('Good luck!');
    }
}
