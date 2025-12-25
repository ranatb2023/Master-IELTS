<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = [
        'quiz_id',
        'question_type_id',
        'question',
        'description',
        'points',
        'order',
        'media_type',
        'media_source',
        'media_url',
        'explanation',
        'difficulty',
        'settings',
    ];

    protected $casts = [
        'points' => 'decimal:2',
        'order' => 'integer',
        'settings' => 'array',
    ];

    // Relationships
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function questionType(): BelongsTo
    {
        return $this->belongsTo(QuestionType::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class);
    }

    // Scopes
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function scopeByQuestionType($query, $questionTypeId)
    {
        return $query->where('question_type_id', $questionTypeId);
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    // Methods
    public function requiresManualGrading(): bool
    {
        return $this->questionType?->requiresManualGrading() ?? false;
    }

    public function isAutoGradable(): bool
    {
        return $this->questionType?->isAutoGradable() ?? false;
    }
}
