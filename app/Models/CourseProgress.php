<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseProgress extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'course_id',
        'progress_percentage',
        'completed_lessons',
        'total_lessons',
        'completed_quizzes',
        'total_quizzes',
        'completed_assignments',
        'total_assignments',
        'average_quiz_score',
        'average_assignment_score',
        'total_time_spent',
        'last_accessed_at',
        'started_at',
        'completed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'progress_percentage' => 'decimal:2',
            'completed_lessons' => 'int',
            'total_lessons' => 'int',
            'completed_quizzes' => 'int',
            'total_quizzes' => 'int',
            'completed_assignments' => 'int',
            'total_assignments' => 'int',
            'average_quiz_score' => 'decimal:2',
            'average_assignment_score' => 'decimal:2',
            'total_time_spent' => 'int',
            'last_accessed_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the course progress.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course that this progress is for.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Update the overall progress percentage based on completed items.
     */
    public function updateProgress(): void
    {
        $totalItems = $this->total_lessons + $this->total_quizzes + $this->total_assignments;

        if ($totalItems === 0) {
            $this->progress_percentage = 0.00;
            return;
        }

        $completedItems = $this->completed_lessons + $this->completed_quizzes + $this->completed_assignments;

        $this->progress_percentage = (float) round(($completedItems / $totalItems) * 100, 2);

        // Check if course is completed
        if ($this->progress_percentage >= 100 && !$this->completed_at) {
            $this->completed_at = now();
        }

        $this->save();
    }

    /**
     * Check if the course is completed.
     */
    public function isCompleted(): bool
    {
        return $this->progress_percentage >= 100 && $this->completed_at !== null;
    }

    /**
     * Get the completion percentage.
     */
    public function getCompletionPercentage(): float
    {
        return (float) $this->progress_percentage;
    }

    /**
     * Boot the model and add observers.
     */
    protected static function booted()
    {
        // Auto-issue certificate when progress reaches 100%
        static::updated(function ($courseProgress) {
            // Check if progress just reached 100%
            if (
                $courseProgress->isDirty('progress_percentage') &&
                $courseProgress->progress_percentage >= 100
            ) {

                // Trigger certificate issuance
                try {
                    $issuer = app(\App\Services\CertificateIssuerService::class);
                    $issuer->issueCertificate($courseProgress->user, $courseProgress->course);

                    \Log::info("Auto-issued certificate for user {$courseProgress->user_id} in course {$courseProgress->course_id}");
                } catch (\Exception $e) {
                    \Log::error("Failed to auto-issue certificate: " . $e->getMessage());
                }
            }
        });
    }
}
