<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = [
        'course_id',
        'topic_id',
        'title',
        'description',
        'instructions',
        'time_limit',
        'passing_score',
        'max_attempts',
        'shuffle_questions',
        'shuffle_answers',
        'show_answers',
        'show_correct_answers',
        'require_passing',
        'certificate_eligible',
        'order',
        'is_published',
    ];

    protected $casts = [
        'time_limit' => 'integer',
        'passing_score' => 'decimal:2',
        'max_attempts' => 'integer',
        'shuffle_questions' => 'boolean',
        'shuffle_answers' => 'boolean',
        'show_answers' => 'boolean',
        'show_correct_answers' => 'boolean',
        'require_passing' => 'boolean',
        'certificate_eligible' => 'boolean',
        'order' => 'integer',
        'is_published' => 'boolean',
    ];

    // Relationships
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeCertificateEligible($query)
    {
        return $query->where('certificate_eligible', true);
    }

    // Methods
    public function canAttempt($user): bool
    {
        if (!$this->is_published) {
            return false;
        }

        if ($this->max_attempts === null || $this->max_attempts === 0) {
            return true;
        }

        $attemptCount = $this->getUserAttempts($user)->count();

        return $attemptCount < $this->max_attempts;
    }

    public function getUserAttempts($user)
    {
        return $this->attempts()->where('user_id', $user->id);
    }
}
