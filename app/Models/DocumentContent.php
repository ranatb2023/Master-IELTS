<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DocumentContent extends Model
{
    protected $fillable = [
        'file_path',
        'file_name',
        'file_type',
        'pages',
    ];

    protected function casts(): array
    {
        return [
            'pages' => 'integer',
        ];
    }

    // Relationships
    public function lessons(): MorphMany
    {
        return $this->morphMany(Lesson::class, 'contentable');
    }
}
