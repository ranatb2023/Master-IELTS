<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class AssignmentSubmission extends Model
{
    protected $fillable = [
        'assignment_id',
        'user_id',
        'enrollment_id',
        'content',
        'submission_text',
        'files',
        'score',
        'feedback',
        'status',
        'passed',
        'submitted_at',
        'graded_at',
        'graded_by',
        'is_late',
        'attempt_number',
    ];

    protected function casts(): array
    {
        return [
            'files' => 'array',
            'score' => 'decimal:2',
            'passed' => 'boolean',
            'is_late' => 'boolean',
            'attempt_number' => 'integer',
            'submitted_at' => 'datetime',
            'graded_at' => 'datetime',
        ];
    }

    // Relationships
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function assignmentFiles(): HasMany
    {
        return $this->hasMany(AssignmentFile::class, 'submission_id');
    }

    public function rubricScores(): HasMany
    {
        return $this->hasMany(SubmissionRubricScore::class, 'submission_id');
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    // Scopes
    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->whereNotNull('submitted_at')
            ->where('status', 'submitted');
    }

    public function scopeGraded(Builder $query): Builder
    {
        return $query->whereNotNull('graded_at')
            ->where('status', 'graded');
    }

    public function scopePassed(Builder $query): Builder
    {
        return $query->where('passed', true);
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('passed', false)
            ->whereNotNull('graded_at');
    }

    public function scopeLate(Builder $query): Builder
    {
        return $query->where('is_late', true);
    }

    // Methods
    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null && $this->status === 'submitted';
    }

    public function isGraded(): bool
    {
        return $this->graded_at !== null && $this->status === 'graded';
    }

    public function isPassed(): bool
    {
        return $this->passed === true;
    }

    public function isLate(): bool
    {
        return $this->is_late === true;
    }
}
