<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAnswer extends Model
{
    protected $fillable = [
        'attempt_id',
        'question_id',
        'answer',
        'selected_options',
        'is_correct',
        'points_earned',
        'feedback',
    ];

    protected $casts = [
        'selected_options' => 'array',
        'is_correct' => 'boolean',
        'points_earned' => 'decimal:2',
    ];

    // Relationships
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class, 'attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    // Accessors for backward compatibility
    public function getAnswerTextAttribute()
    {
        return $this->answer;
    }

    public function getSelectedOptionIdAttribute()
    {
        // If selected_options is an array with a single value, return that value
        if (is_array($this->selected_options) && count($this->selected_options) === 1) {
            return $this->selected_options[0];
        }
        // If it's a single integer stored as JSON, return it
        if (is_array($this->selected_options) && isset($this->selected_options[0])) {
            return $this->selected_options[0];
        }
        return null;
    }
}
