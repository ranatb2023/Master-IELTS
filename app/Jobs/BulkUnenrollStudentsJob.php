<?php

namespace App\Jobs;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BulkUnenrollStudentsJob implements ShouldQueue
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
            Log::error("Bulk unenrollment failed: Course not found", ['course_id' => $this->courseId]);
            return;
        }

        $cacheKey = "auto_unenrollment_{$this->courseId}";

        // Get total auto-enrolled student count
        $totalEnrollments = Enrollment::where('course_id', $this->courseId)
            ->where('enrollment_source', 'auto_enroll')
            ->count();

        // Initialize progress tracking
        Cache::put($cacheKey, [
            'total' => $totalEnrollments,
            'completed' => 0,
            'progress' => 0,
            'status' => 'processing',
            'started_at' => now()->toDateTimeString(),
            'errors' => [],
        ], 3600);

        $completed = 0;
        $errors = [];

        try {
            // Process enrollments in chunks
            Enrollment::where('course_id', $this->courseId)
                ->where('enrollment_source', 'auto_enroll')
                ->chunk($this->chunkSize, function ($enrollments) use ($cacheKey, &$completed, &$errors, $totalEnrollments) {

                    foreach ($enrollments as $enrollment) {
                        try {
                            // Soft delete or hard delete based on preference
                            $enrollment->delete();
                        } catch (\Exception $e) {
                            $errors[] = "Enrollment {$enrollment->id}: " . $e->getMessage();
                            Log::error("Unenrollment error", [
                                'enrollment_id' => $enrollment->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }

                    $completed += $enrollments->count();
                    $progress = $totalEnrollments > 0 ? round(($completed / $totalEnrollments) * 100, 2) : 100;

                    // Update progress
                    Cache::put($cacheKey, [
                        'total' => $totalEnrollments,
                        'completed' => $completed,
                        'progress' => $progress,
                        'status' => 'processing',
                        'started_at' => Cache::get($cacheKey)['started_at'] ?? now()->toDateTimeString(),
                        'errors' => $errors,
                    ], 3600);
                });

            // Mark as completed
            Cache::put($cacheKey, [
                'total' => $totalEnrollments,
                'completed' => $completed,
                'progress' => 100,
                'status' => 'completed',
                'started_at' => Cache::get($cacheKey)['started_at'] ?? now()->toDateTimeString(),
                'completed_at' => now()->toDateTimeString(),
                'errors' => $errors,
            ], 3600);

            Log::info("Bulk auto-unenrollment completed", [
                'course_id' => $course->id,
                'unenrolled' => $completed,
                'errors' => count($errors)
            ]);

        } catch (\Exception $e) {
            // Mark as failed
            Cache::put($cacheKey, [
                'total' => $totalEnrollments,
                'completed' => $completed,
                'progress' => 0,
                'status' => 'failed',
                'started_at' => Cache::get($cacheKey)['started_at'] ?? now()->toDateTimeString(),
                'errors' => array_merge($errors, [$e->getMessage()]),
            ], 3600);

            Log::error("Bulk auto-unenrollment failed", [
                'course_id' => $course->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
