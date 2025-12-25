<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $amount;
    protected $reason;

    public function __construct(float $amount, string $reason = null)
    {
        $this->amount = $amount;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Payment Failed',
            'message' => "Payment of $" . number_format($this->amount, 2) . " failed. Please update your payment method.",
            'action_url' => route('student.subscription.payment-method'),
            'icon' => 'exclamation-circle',
            'color' => 'red',
            'metadata' => [
                'amount' => $this->amount,
                'reason' => $this->reason,
            ],
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Payment Failed')
            ->greeting("Hello {$notifiable->name}!")
            ->line("We were unable to process your payment of $" . number_format($this->amount, 2) . ".")
            ->when($this->reason, function ($mail) {
                return $mail->line("Reason: {$this->reason}");
            })
            ->line('Please update your payment method to continue enjoying our services.')
            ->action('Update Payment Method', route('student.subscription.payment-method'))
            ->line('If you have any questions, please contact our support team.');
    }
}
