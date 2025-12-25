<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class Assignment extends Model
{
    protected $fillable = [
        'topic_id',
        'title',
        'description',
        'instructions',
        'max_points',
        'passing_points',
        'due_date',
        'allow_late_submission',
        'late_penalty',
        'max_file_size',
        'allowed_file_types',
        'max_files',
        'auto_grade',
        'require_passing',
        'order',
        'is_published',
        'allow_text_submission',
        'allow_file_upload',
    ];

    protected function casts(): array
    {
        return [
            'max_points' => 'decimal:2',
            'passing_points' => 'decimal:2',
            'late_penalty' => 'decimal:2',
            'allowed_file_types' => 'array',
            'due_date' => 'datetime',
            'allow_late_submission' => 'boolean',
            'allow_text_submission' => 'boolean',
            'allow_file_upload' => 'boolean',
            'auto_grade' => 'boolean',
            'require_passing' => 'boolean',
            'is_published' => 'boolean',
            'max_file_size' => 'integer',
            'max_files' => 'integer',
            'order' => 'integer',
        ];
    }

    // Relationships
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function rubrics(): HasMany
    {
        return $this->hasMany(AssignmentRubric::class);
    }

    // Scopes
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('due_date', '<', now())
            ->where('is_published', true);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('due_date', '>', now())
            ->where('is_published', true);
    }

    // Methods
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast();
    }

    public function canSubmit(User $user): bool
    {
        if (!$this->is_published) {
            return false;
        }

        if ($this->isOverdue() && !$this->allow_late_submission) {
            return false;
        }

        return true;
    }

    public function getUserSubmission(User $user): ?AssignmentSubmission
    {
        return $this->submissions()
            ->where('user_id', $user->id)
            ->latest('submitted_at')
            ->first();
    }
}
