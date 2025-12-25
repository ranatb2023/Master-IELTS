<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'channels',
        'quiet_hours',
    ];

    protected $casts = [
        'channels' => 'array',
        'quiet_hours' => 'array',
    ];

    /**
     * Get the user that owns the notification preference
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if a specific notification channel is enabled
     */
    public function isChannelEnabled(string $channel): bool
    {
        $channels = $this->channels ?? ['email' => true, 'database' => true];
        return $channels[$channel] ?? false;
    }

    /**
     * Check if current time is within quiet hours
     */
    public function isQuietTime(): bool
    {
        if (!$this->quiet_hours || !isset($this->quiet_hours['enabled']) || !$this->quiet_hours['enabled']) {
            return false;
        }

        $now = Carbon::now();
        $currentTime = $now->format('H:i');

        $start = $this->quiet_hours['start'] ?? '22:00';
        $end = $this->quiet_hours['end'] ?? '08:00';

        // Handle overnight quiet hours (e.g., 22:00 to 08:00)
        if ($start > $end) {
            return $currentTime >= $start || $currentTime <= $end;
        }

        return $currentTime >= $start && $currentTime <= $end;
    }
}
