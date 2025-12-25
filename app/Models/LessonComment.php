<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class LessonComment extends Model
{
    protected $fillable = [
        'lesson_id',
        'user_id',
        'parent_id',
        'comment',
        'is_from_tutor',
        'is_pinned',
    ];

    protected $with = ['user'];

    protected function casts(): array
    {
        return [
            'is_from_tutor' => 'boolean',
            'is_pinned' => 'boolean',
        ];
    }

    // Relationships
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(LessonComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(LessonComment::class, 'parent_id')->with('user');
    }

    // Scopes
    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeFromTutor(Builder $query): Builder
    {
        return $query->where('is_from_tutor', true);
    }

    public function scopePinned(Builder $query): Builder
    {
        return $query->where('is_pinned', true);
    }

    public function scopeForCourse(Builder $query, int $courseId): Builder
    {
        return $query->whereHas('lesson.topic', function ($q) use ($courseId) {
            $q->where('course_id', $courseId);
        });
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeVisibleToStudent(Builder $query, User $user): Builder
    {
        return $query->where(function ($q) use ($user) {
            // Student's own top-level comments
            $q->where('user_id', $user->id)
                // OR replies from admin/tutor to student's comments
                ->orWhere(function ($replyQuery) use ($user) {
                    $replyQuery->where('is_from_tutor', true)
                        ->whereHas('parent', function ($parentQuery) use ($user) {
                            $parentQuery->where('user_id', $user->id);
                        });
                });
        });
    }

    // Helper Methods
    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }

    public function canBeEditedBy(User $user): bool
    {
        // User can edit their own comments
        if ($this->user_id === $user->id) {
            return true;
        }

        // Admin can edit any comment
        if ($user->hasAnyAdminRole()) {
            return true;
        }

        return false;
    }

    public function canBeDeletedBy(User $user): bool
    {
        // User can delete their own comments
        if ($this->user_id === $user->id) {
            return true;
        }

        // Admin can delete any comment
        if ($user->hasAnyAdminRole()) {
            return true;
        }

        // Tutor can delete comments on their courses (only if they own the course)
        if ($user->isTutor()) {
            $course = $this->lesson->topic->course;
            return $course->instructor_id === $user->id;
        }

        return false;
    }

    public function canBePinnedBy(User $user): bool
    {
        // Admin can pin any comment
        if ($user->hasAnyAdminRole()) {
            return true;
        }

        // Tutor can pin comments on their courses
        if ($user->isTutor()) {
            $course = $this->lesson->topic->course;
            return $course->instructor_id === $user->id;
        }

        return false;
    }
}
