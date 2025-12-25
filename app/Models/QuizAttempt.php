<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    protected $fillable = [
        'quiz_id',
        'user_id',
        'enrollment_id',
        'score',
        'total_points',
        'percentage',
        'passed',
        'time_taken',
        'started_at',
        'submitted_at',
        'completed_at',
        'status',
        'graded_at',
        'graded_by',
        'requires_manual_grading',
        'attempt_number',
        'answers',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'total_points' => 'decimal:2',
        'percentage' => 'decimal:2',
        'passed' => 'boolean',
        'requires_manual_grading' => 'boolean',
        'time_taken' => 'integer',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
        'graded_at' => 'datetime',
        'attempt_number' => 'integer',
        'answers' => 'array',
    ];

    // Relationships
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function quizAnswers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class, 'attempt_id');
    }

    // Scopes
    public function scopePassed($query)
    {
        return $query->where('passed', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('passed', false);
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('submitted_at');
    }

    public function scopeRequiresGrading($query)
    {
        return $query->where('requires_manual_grading', true)
                     ->where('status', '!=', 'graded');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Methods
    public function isPassed(): bool
    {
        return $this->passed === true;
    }

    public function isCompleted(): bool
    {
        return $this->submitted_at !== null;
    }

    public function isGraded(): bool
    {
        return $this->status === 'graded';
    }

    public function needsManualGrading(): bool
    {
        return $this->requires_manual_grading && !$this->isGraded();
    }
}
