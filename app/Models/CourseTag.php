<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseTag extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get all courses with this tag.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_tag', 'course_tag_id', 'course_id');
    }

    /**
     * Scope to only get active tags.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
