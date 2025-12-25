<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $status;
    protected $planName;

    public function __construct(string $status, string $planName)
    {
        $this->status = $status;
        $this->planName = $planName;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        $messages = [
            'active' => "Your subscription to {$this->planName} is now active",
            'cancelled' => "Your subscription to {$this->planName} has been cancelled",
            'resumed' => "Your subscription to {$this->planName} has been resumed",
            'ended' => "Your subscription to {$this->planName} has ended",
        ];

        return [
            'title' => 'Subscription Update',
            'message' => $messages[$this->status] ?? "Your subscription status has been updated",
            'action_url' => route('student.subscription.index'),
            'icon' => $this->status === 'active' ? 'check-circle' : 'information-circle',
            'color' => $this->status === 'active' ? 'green' : 'blue',
            'metadata' => [
                'status' => $this->status,
                'plan_name' => $this->planName,
            ],
        ];
    }

    public function toMail($notifiable)
    {
        $messages = [
            'active' => "Your subscription to {$this->planName} is now active. Enjoy all the benefits!",
            'cancelled' => "Your subscription to {$this->planName} has been cancelled. You will retain access until the end of your billing period.",
            'resumed' => "Welcome back! Your subscription to {$this->planName} has been resumed.",
            'ended' => "Your subscription to {$this->planName} has ended. Resubscribe to continue enjoying premium features.",
        ];

        return (new MailMessage)
            ->subject('Subscription Update')
            ->greeting("Hello {$notifiable->name}!")
            ->line($messages[$this->status] ?? "Your subscription status has been updated.")
            ->action('Manage Subscription', route('student.subscription.index'))
            ->line('Thank you for being a valued member!');
    }
}
