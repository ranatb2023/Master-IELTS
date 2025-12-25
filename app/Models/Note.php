<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'lesson_id',
        'course_id',
        'title',
        'body',
        'is_pinned',
        'color',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'tags' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(NoteAttachment::class);
    }

    // Scopes
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function scopePinned(Builder $query): Builder
    {
        return $query->where('is_pinned', true);
    }

    public function scopeForLesson(Builder $query, $lessonId): Builder
    {
        return $query->where('lesson_id', $lessonId);
    }

    public function scopeForCourse(Builder $query, $courseId): Builder
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('body', 'like', "%{$search}%");
        });
    }

    public function scopeByColor(Builder $query, ?string $color): Builder
    {
        if ($color) {
            return $query->where('color', $color);
        }
        return $query;
    }

    public function scopeWithTag(Builder $query, ?string $tag): Builder
    {
        if ($tag) {
            return $query->whereJsonContains('tags', $tag);
        }
        return $query;
    }

    // Helper Methods
    public function canBeEditedBy(User $user): bool
    {
        // Only the note owner can edit their notes
        // Admins and tutors CANNOT edit student notes per user requirement
        return $this->user_id === $user->id;
    }

    public function canBeDeletedBy(User $user): bool
    {
        // Only the note owner can delete their notes
        // Admins and tutors CANNOT delete student notes per user requirement
        return $this->user_id === $user->id;
    }

    public function canBeViewedBy(User $user): bool
    {
        // Only the note owner can view their notes
        // Admins and tutors CANNOT view student notes per user requirement
        return $this->user_id === $user->id;
    }

    /**
     * Get a shortened excerpt of the note body
     */
    public function getExcerpt(int $length = 150): string
    {
        $text = strip_tags($this->body);
        if (strlen($text) > $length) {
            return substr($text, 0, $length) . '...';
        }
        return $text;
    }

    /**
     * Get all unique tags used across user's notes
     */
    public static function getTagsForUser(User $user): array
    {
        $notes = static::forUser($user)->whereNotNull('tags')->get();
        $allTags = [];

        foreach ($notes as $note) {
            if (is_array($note->tags)) {
                $allTags = array_merge($allTags, $note->tags);
            }
        }

        return array_values(array_unique($allTags));
    }
}
