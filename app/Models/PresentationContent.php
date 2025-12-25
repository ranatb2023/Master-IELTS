<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PresentationContent extends Model
{
    protected $fillable = [
        'file_path',
        'slides',
    ];

    protected function casts(): array
    {
        return [
            'slides' => 'integer',
        ];
    }

    // Relationships
    public function lessons(): MorphMany
    {
        return $this->morphMany(Lesson::class, 'contentable');
    }
}
