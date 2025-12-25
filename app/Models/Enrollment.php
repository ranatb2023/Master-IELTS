<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Enrollment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'course_id',
        'package_id',
        'package_access_id',
        'subscription_id',
        'order_id',
        'enrolled_at',
        'expires_at',
        'status',
        'payment_status',
        'amount_paid',
        'refund_reason',
        'refund_amount',
        'refunded_at',
        'enrollment_source',
        'notes',
        'progress_percentage',
        'last_accessed_at',
        'completed_at',
        'certificate_issued',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
            'expires_at' => 'datetime',
            'refunded_at' => 'datetime',
            'progress_percentage' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'refund_amount' => 'decimal:2',
            'last_accessed_at' => 'datetime',
            'completed_at' => 'datetime',
            'certificate_issued' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the enrollment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course that this enrollment is for.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the package access associated with this enrollment.
     */
    public function packageAccess(): BelongsTo
    {
        return $this->belongsTo(UserPackageAccess::class, 'package_access_id');
    }

    /**
     * Get the subscription associated with this enrollment (Cashier).
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(\Laravel\Cashier\Subscription::class, 'subscription_id');
    }

    /**
     * Get the package associated with this enrollment.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    /**
     * Get the progress records for this enrollment's user in this course.
     * Note: Progress is not directly related to enrollments, so we need to query through user and course.
     */
    public function getCourseProgressAttribute()
    {
        return Progress::where('user_id', $this->user_id)
            ->whereHasMorph('progressable', ['App\Models\Lesson'], function ($query) {
                $query->whereHas('topic', function ($q) {
                    $q->where('course_id', $this->course_id);
                });
            })
            ->get();
    }

    /**
     * Get the quiz attempts for this enrollment's user in this course.
     */
    public function getCourseQuizAttemptsAttribute()
    {
        return QuizAttempt::where('user_id', $this->user_id)
            ->whereHas('quiz.topic', function ($q) {
                $q->where('course_id', $this->course_id);
            })
            ->get();
    }

    /**
     * Get the assignment submissions for this enrollment's user in this course.
     */
    public function getCourseAssignmentSubmissionsAttribute()
    {
        return AssignmentSubmission::where('user_id', $this->user_id)
            ->whereHas('assignment.topic', function ($q) {
                $q->where('course_id', $this->course_id);
            })
            ->get();
    }

    /**
     * Scope a query to only include active enrollments.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope a query to only include expired enrollments.
     */
    public function scopeExpired(Builder $query): void
    {
        $query->where('expires_at', '<=', now())
            ->whereNotNull('expires_at');
    }

    /**
     * Scope a query to only include completed enrollments.
     */
    public function scopeCompleted(Builder $query): void
    {
        $query->where('status', 'completed')
            ->whereNotNull('completed_at');
    }

    /**
     * Check if the enrollment is active.
     */
    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if the enrollment is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if the enrollment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed' && $this->completed_at !== null;
    }

    /**
     * Mark the enrollment as completed.
     */
    public function markAsCompleted(): bool
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->progress_percentage = 100.00;

        return $this->save();
    }

    /**
     * Boot function to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // When an enrollment is deleted, clean up all related student data
        static::deleted(function ($enrollment) {
            \Log::info("Cascade deleting related data for enrollment ID: {$enrollment->id}", [
                'user_id' => $enrollment->user_id,
                'course_id' => $enrollment->course_id,
            ]);

            // Delete quiz attempts for this enrollment
            $quizAttemptsDeleted = \App\Models\QuizAttempt::where('user_id', $enrollment->user_id)
                ->where('enrollment_id', $enrollment->id)
                ->delete();

            \Log::info("Deleted {$quizAttemptsDeleted} quiz attempts for enrollment {$enrollment->id}");

            // Delete assignment submissions for this enrollment
            $assignmentSubmissionsDeleted = \App\Models\AssignmentSubmission::where('user_id', $enrollment->user_id)
                ->where('enrollment_id', $enrollment->id)
                ->delete();

            \Log::info("Deleted {$assignmentSubmissionsDeleted} assignment submissions for enrollment {$enrollment->id}");

            // Delete progress records for this user in this course
            $progressDeleted = \App\Models\Progress::where('user_id', $enrollment->user_id)
                ->whereHasMorph('progressable', ['App\Models\Lesson'], function ($query) use ($enrollment) {
                    $query->whereHas('topic', function ($q) use ($enrollment) {
                        $q->where('course_id', $enrollment->course_id);
                    });
                })
                ->delete();

            \Log::info("Deleted {$progressDeleted} progress records for enrollment {$enrollment->id}");

            // Delete course progress aggregate record
            $courseProgressDeleted = \App\Models\CourseProgress::where('user_id', $enrollment->user_id)
                ->where('course_id', $enrollment->course_id)
                ->delete();

            \Log::info("Deleted {$courseProgressDeleted} course progress records for enrollment {$enrollment->id}");

            \Log::info("Cascade delete completed for enrollment ID: {$enrollment->id}");
        });
    }
}
