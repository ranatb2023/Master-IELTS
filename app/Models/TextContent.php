<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TextContent extends Model
{
    protected $fillable = [
        'body',
        'reading_time',
    ];

    protected function casts(): array
    {
        return [
            'reading_time' => 'integer',
        ];
    }

    // Relationships
    public function lessons(): MorphMany
    {
        return $this->morphMany(Lesson::class, 'contentable');
    }
}
