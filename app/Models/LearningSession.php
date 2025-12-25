<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningSession extends Model
{
    /**
     * Indicates if the model should use updated_at timestamps.
     *
     * @var string|null
     */
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'course_id',
        'lesson_id',
        'started_at',
        'ended_at',
        'duration',
        'activity_data',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'duration' => 'int',
            'activity_data' => 'array',
        ];
    }

    /**
     * Get the user that owns the learning session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course associated with this learning session.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the lesson associated with this learning session.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Calculate the duration of the session in seconds.
     */
    public function calculateDuration(): int
    {
        if (!$this->started_at || !$this->ended_at) {
            return 0;
        }

        $duration = $this->ended_at->diffInSeconds($this->started_at);

        $this->duration = $duration;
        $this->save();

        return $duration;
    }

    /**
     * Check if the session is currently active.
     */
    public function isActive(): bool
    {
        return $this->started_at !== null && $this->ended_at === null;
    }
}
