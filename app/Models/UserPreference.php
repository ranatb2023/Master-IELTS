<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_notifications',
        'push_notifications',
        'sms_notifications',
        'course_updates',
        'assignment_reminders',
        'message_notifications',
        'marketing_emails',
        'weekly_digest',
        'theme',
        'notifications_settings',
        'privacy_settings',
    ];

    protected function casts(): array
    {
        return [
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'course_updates' => 'boolean',
            'assignment_reminders' => 'boolean',
            'message_notifications' => 'boolean',
            'marketing_emails' => 'boolean',
            'weekly_digest' => 'boolean',
            'notifications_settings' => 'array',
            'privacy_settings' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}