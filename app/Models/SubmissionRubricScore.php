<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionRubricScore extends Model
{
    protected $fillable = [
        'submission_id',
        'rubric_id',
        'points',
        'feedback',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'decimal:2',
        ];
    }

    // Relationships
    public function submission(): BelongsTo
    {
        return $this->belongsTo(AssignmentSubmission::class, 'submission_id');
    }

    public function rubric(): BelongsTo
    {
        return $this->belongsTo(AssignmentRubric::class, 'rubric_id');
    }
}
