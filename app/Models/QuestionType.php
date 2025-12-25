<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'slug',
        'name',
        'description',
        'input_schema',
        'output_schema',
        'scoring_strategy',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'input_schema' => 'array',
        'output_schema' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the questions of this type.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Scope a query to only include active question types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if this question type requires manual grading.
     */
    public function requiresManualGrading(): bool
    {
        return $this->scoring_strategy === 'manual';
    }

    /**
     * Check if this question type supports partial scoring.
     */
    public function supportsPartialScoring(): bool
    {
        return $this->scoring_strategy === 'auto_partial';
    }

    /**
     * Check if this question type is auto-gradable.
     */
    public function isAutoGradable(): bool
    {
        return in_array($this->scoring_strategy, ['auto_exact', 'auto_partial']);
    }
}
