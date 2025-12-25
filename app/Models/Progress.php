<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;

class Progress extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'progressable_type',
        'progressable_id',
        'status',
        'completed_at',
        'time_spent',
        'score',
        'last_position',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
            'time_spent' => 'int',
            'score' => 'decimal:2',
        ];
    }

    /**
     * Get the user that owns the progress.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent progressable model (Lesson, Quiz, Assignment, etc.).
     */
    public function progressable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include completed progress.
     */
    public function scopeCompleted(Builder $query): void
    {
        $query->where('status', 'completed')
              ->whereNotNull('completed_at');
    }

    /**
     * Scope a query to only include in-progress items.
     */
    public function scopeInProgress(Builder $query): void
    {
        $query->where('status', 'in_progress')
              ->whereNull('completed_at');
    }

    /**
     * Scope a query to only include not started items.
     */
    public function scopeNotStarted(Builder $query): void
    {
        $query->where('status', 'not_started')
              ->orWhereNull('status');
    }
}
