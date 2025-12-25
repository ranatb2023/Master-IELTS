<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewUserRegisteredNotification extends Notification
{
    use Queueable;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $roleName = $this->user->getRoleNames()->first() ?? 'user';

        return [
            'title' => 'New User Registration',
            'message' => "{$this->user->name} ({$roleName}) has registered",
            'action_url' => route('admin.users.show', $this->user),
            'icon' => 'user-plus',
            'color' => 'blue',
            'metadata' => [
                'user_id' => $this->user->id,
                'role' => $roleName,
            ],
        ];
    }
}
