<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BlogTag extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blog_tags';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'usage_count',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'usage_count' => 'integer',
        ];
    }

    /**
     * Get the posts associated with this tag.
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tag', 'blog_tag_id', 'post_id')
            ->withTimestamps();
    }

    /**
     * Scope a query to order tags by usage count (most used first).
     */
    public function scopePopular(Builder $query): void
    {
        $query->orderBy('usage_count', 'desc');
    }

    /**
     * Scope a query to only include tags with usage count greater than zero.
     */
    public function scopeUsed(Builder $query): void
    {
        $query->where('usage_count', '>', 0);
    }

    /**
     * Scope a query to only include tags with no usage.
     */
    public function scopeUnused(Builder $query): void
    {
        $query->where('usage_count', 0);
    }

    /**
     * Scope a query to order tags alphabetically.
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('name', 'asc');
    }

    /**
     * Increment the usage count of this tag.
     */
    public function incrementUsage(): bool
    {
        return $this->increment('usage_count');
    }

    /**
     * Decrement the usage count of this tag.
     */
    public function decrementUsage(): bool
    {
        if ($this->usage_count > 0) {
            return $this->decrement('usage_count');
        }

        return false;
    }

    /**
     * Recalculate and update the usage count based on actual relationships.
     */
    public function recalculateUsage(): bool
    {
        $count = $this->posts()->count();

        return $this->update(['usage_count' => $count]);
    }

    /**
     * Check if the tag is being used.
     */
    public function isUsed(): bool
    {
        return $this->usage_count > 0;
    }

    /**
     * Get the route key name for Laravel.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
