<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentReceivedNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Payment Received',
            'message' => "Payment of $" . number_format($this->order->total, 2) . " received from {$this->order->user->name} for package purchase",
            'action_url' => route('admin.enrollments.index'),
            'icon' => 'cash',
            'color' => 'green',
            'metadata' => [
                'order_id' => $this->order->id,
                'user_id' => $this->order->user_id,
                'amount' => $this->order->total,
            ],
        ];
    }
}
