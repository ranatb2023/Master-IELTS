<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'order',
        'is_active',
        'parent_id',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_active' => 'boolean',
            'parent_id' => 'integer',
        ];
    }

    /**
     * Get all courses in this category.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_category', 'course_category_id', 'course_id');
    }

    /**
     * Get the parent category.
     */
    public function parent()
    {
        return $this->belongsTo(CourseCategory::class, 'parent_id');
    }

    /**
     * Get child categories.
     */
    public function children()
    {
        return $this->hasMany(CourseCategory::class, 'parent_id')->orderBy('order');
    }

    /**
     * Scope to only get active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to only get parent categories.
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }
}
