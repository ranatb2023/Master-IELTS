<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateEarnedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $certificate;

    public function __construct(Certificate $certificate)
    {
        $this->certificate = $certificate;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Certificate Earned!',
            'message' => "Congratulations! You've earned a certificate for {$this->certificate->course->title}",
            'action_url' => route('student.certificates.show', $this->certificate),
            'icon' => 'badge-check',
            'color' => 'green',
            'metadata' => [
                'certificate_id' => $this->certificate->id,
                'course_id' => $this->certificate->course_id,
            ],
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Congratulations! Certificate Earned')
            ->greeting("Hello {$notifiable->name}!")
            ->line("Congratulations on completing {$this->certificate->course->title}!")
            ->line("You've earned a certificate of completion. Download and share it with pride!")
            ->action('View Certificate', route('student.certificates.show', $this->certificate))
            ->line('Keep up the excellent work!');
    }
}
