<?php

namespace App\Notifications;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RefundRequestedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $enrollment;
    protected $reason;

    public function __construct(Enrollment $enrollment, string $reason = null)
    {
        $this->enrollment = $enrollment;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Refund Requested',
            'message' => "Refund requested for {$this->enrollment->user->name} - {$this->enrollment->course->title}",
            'action_url' => route('admin.enrollments.show', $this->enrollment),
            'icon' => 'currency-dollar',
            'color' => 'orange',
            'metadata' => [
                'enrollment_id' => $this->enrollment->id,
                'user_id' => $this->enrollment->user_id,
                'course_id' => $this->enrollment->course_id,
                'reason' => $this->reason,
            ],
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Refund Request')
            ->line("A refund has been requested for:")
            ->line("Student: {$this->enrollment->user->name}")
            ->line("Course: {$this->enrollment->course->title}")
            ->when($this->reason, function ($mail) {
                return $mail->line("Reason: {$this->reason}");
            })
            ->action('Review Request', route('admin.enrollments.show', $this->enrollment->id));
    }
}
