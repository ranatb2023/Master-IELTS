<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class VideoContent extends Model
{
    protected $fillable = [
        'vimeo_id',
        'url',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'duration_seconds',
        'source',
        'captions',
        'quality',
        'transcript',
    ];

    protected function casts(): array
    {
        return [
            'captions' => 'array',
            'quality' => 'array',
        ];
    }

    // Relationships
    public function lessons(): MorphMany
    {
        return $this->morphMany(Lesson::class, 'contentable');
    }
}
