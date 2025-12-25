<?php

namespace App\Jobs;

use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use App\Notifications\CourseAutoEnrolledNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BulkAutoEnrollStudentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes max
    public $tries = 3;

    protected $courseId;
    protected $chunkSize;

    /**
     * Create a new job instance.
     */
    public function __construct(int $courseId, int $chunkSize = 100)
    {
        $this->courseId = $courseId;
        $this->chunkSize = $chunkSize;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $course = Course::find($this->courseId);

        if (!$course) {
            Log::error("Bulk enrollment failed: Course not found", ['course_id' => $this->courseId]);
            return;
        }

        $cacheKey = "auto_enrollment_{$this->courseId}";

        // Get total student count
        $totalStudents = User::role('student')->count();

        // Initialize progress tracking
        Cache::put($cacheKey, [
            'total' => $totalStudents,
            'completed' => 0,
            'progress' => 0,
            'status' => 'processing',
            'started_at' => now()->toDateTimeString(),
            'errors' => [],
        ], 3600); // 1 hour expiry

        $completed = 0;
        $errors = [];

        try {
            // Process students in chunks to avoid memory issues
            User::role('student')->chunk($this->chunkSize, function ($students) use ($course, $cacheKey, &$completed, &$errors, $totalStudents) {
                $enrollmentData = [];

                foreach ($students as $student) {
                    try {
                        // Check if already enrolled
                        $exists = Enrollment::where('user_id', $student->id)
                            ->where('course_id', $course->id)
                            ->exists();

                        if (!$exists) {
                            $enrollmentData[] = [
                                'user_id' => $student->id,
                                'course_id' => $course->id,
                                'status' => 'active',
                                'enrolled_at' => now(),
                                'enrollment_source' => 'auto_enroll',
                                'payment_status' => 'free',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Student {$student->id}: " . $e->getMessage();
                        Log::error("Enrollment error for student {$student->id}", [
                            'course_id' => $course->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                // Batch insert enrollments
                if (!empty($enrollmentData)) {
                    DB::table('enrollments')->insert($enrollmentData);

                    // Send notifications (optional - can be disabled for performance)
                    foreach ($enrollmentData as $enrollment) {
                        try {
                            $user = User::find($enrollment['user_id']);
                            if ($user) {
                                $user->notify(new CourseAutoEnrolledNotification($course));
                            }
                        } catch (\Exception $e) {
                            Log::warning("Notification failed for user {$enrollment['user_id']}", [
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }

                $completed += $students->count();
                $progress = $totalStudents > 0 ? round(($completed / $totalStudents) * 100, 2) : 100;

                // Update progress
                Cache::put($cacheKey, [
                    'total' => $totalStudents,
                    'completed' => $completed,
                    'progress' => $progress,
                    'status' => 'processing',
                    'started_at' => Cache::get($cacheKey)['started_at'] ?? now()->toDateTimeString(),
                    'errors' => $errors,
                ], 3600);
            });

            // Mark as completed
            Cache::put($cacheKey, [
                'total' => $totalStudents,
                'completed' => $completed,
                'progress' => 100,
                'status' => 'completed',
                'started_at' => Cache::get($cacheKey)['started_at'] ?? now()->toDateTimeString(),
                'completed_at' => now()->toDateTimeString(),
                'errors' => $errors,
            ], 3600);

            Log::info("Bulk auto-enrollment completed", [
                'course_id' => $course->id,
                'enrolled' => $completed,
                'errors' => count($errors)
            ]);

        } catch (\Exception $e) {
            // Mark as failed
            Cache::put($cacheKey, [
                'total' => $totalStudents,
                'completed' => $completed,
                'progress' => 0,
                'status' => 'failed',
                'started_at' => Cache::get($cacheKey)['started_at'] ?? now()->toDateTimeString(),
                'errors' => array_merge($errors, [$e->getMessage()]),
            ], 3600);

            Log::error("Bulk auto-enrollment failed", [
                'course_id' => $course->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
