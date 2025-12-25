<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PackagePurchasedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $package;

    public function __construct(Order $order, Package $package)
    {
        $this->order = $order;
        $this->package = $package;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Package Purchase Successful',
            'message' => "You have successfully purchased {$this->package->name}",
            'action_url' => route('student.packages.show', $this->package),
            'icon' => 'shopping-bag',
            'color' => 'green',
            'metadata' => [
                'order_id' => $this->order->id,
                'package_id' => $this->package->id,
                'amount' => $this->order->total,
            ],
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Package Purchase Confirmation')
            ->greeting("Hello {$notifiable->name}!")
            ->line("Thank you for purchasing {$this->package->name}!")
            ->line("Amount: $" . number_format($this->order->total, 2))
            ->line('You now have access to all courses and features included in this package.')
            ->action('View Package', route('student.packages.show', $this->package))
            ->line('Happy learning!');
    }
}
