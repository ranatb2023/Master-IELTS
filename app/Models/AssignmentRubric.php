<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class AssignmentRubric extends Model
{
    protected $fillable = [
        'assignment_id',
        'criteria',
        'description',
        'max_points',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'max_points' => 'decimal:2',
            'order' => 'integer',
        ];
    }

    // Relationships
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(SubmissionRubricScore::class, 'rubric_id');
    }

    // Scopes
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }
}
