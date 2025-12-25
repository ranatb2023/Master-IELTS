<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'topic_id',
        'title',
        'description',
        'content_type',
        'contentable_type',
        'contentable_id',
        'duration_minutes',
        'order',
        'is_preview',
        'is_published',
        'requires_previous_completion',
    ];

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'order' => 'integer',
            'is_preview' => 'boolean',
            'is_published' => 'boolean',
            'requires_previous_completion' => 'boolean',
        ];
    }

    // Relationships
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function contentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function resources(): HasMany
    {
        return $this->hasMany(LessonResource::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(LessonComment::class);
    }

    public function progress(): MorphMany
    {
        return $this->morphMany(Progress::class, 'progressable');
    }

    // Scopes
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopePreview(Builder $query): Builder
    {
        return $query->where('is_preview', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order', 'asc');
    }
}
