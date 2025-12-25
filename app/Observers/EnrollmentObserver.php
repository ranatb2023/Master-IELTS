<?php

namespace App\Observers;

use App\Models\Enrollment;
use App\Models\Certificate;
use App\Models\CourseProgress;

class EnrollmentObserver
{
    /**
     * Handle the Enrollment "created" event.
     */
    public function created(Enrollment $enrollment): void
    {
        // Update course enrolled count
        $enrollment->course->increment('enrolled_count');

        // Create or update course progress record
        CourseProgress::updateOrCreate(
            [
                'user_id' => $enrollment->user_id,
                'course_id' => $enrollment->course_id,
            ],
            [
                'total_lessons' => $enrollment->course->topics()
                    ->withCount('lessons')
                    ->get()
                    ->sum('lessons_count'),
                'completed_lessons' => 0,
                'total_quizzes' => $enrollment->course->topics()
                    ->withCount('quizzes')
                    ->get()
                    ->sum('quizzes_count'),
                'completed_quizzes' => 0,
                'total_assignments' => $enrollment->course->topics()
                    ->withCount('assignments')
                    ->get()
                    ->sum('assignments_count'),
                'completed_assignments' => 0,
                'progress_percentage' => 0,
                'started_at' => now(),
            ]
        );

        // Log activity
        activity()
            ->performedOn($enrollment)
            ->causedBy($enrollment->user)
            ->log('Enrolled in course: ' . $enrollment->course->title);

        // TODO: Send enrollment confirmation email
    }

    /**
     * Handle the Enrollment "updated" event.
     */
    public function updated(Enrollment $enrollment): void
    {
        // If status changed to completed, generate certificate
        if ($enrollment->isDirty('status') && $enrollment->status === 'completed') {
            $this->generateCertificate($enrollment);
        }

        // Update course progress
        if ($enrollment->isDirty('progress_percentage')) {
            CourseProgress::where('user_id', $enrollment->user_id)
                ->where('course_id', $enrollment->course_id)
                ->update([
                    'progress_percentage' => $enrollment->progress_percentage,
                    'last_accessed_at' => now(),
                ]);
        }
    }

    /**
     * Handle the Enrollment "deleted" event.
     */
    public function deleted(Enrollment $enrollment): void
    {
        // Decrement course enrolled count
        $enrollment->course->decrement('enrolled_count');

        // Log activity
        activity()
            ->performedOn($enrollment)
            ->causedBy(auth()->user())
            ->log('Enrollment deleted');
    }

    /**
     * Generate certificate for completed enrollment
     */
    private function generateCertificate(Enrollment $enrollment): void
    {
        // Check if certificate already exists
        if ($enrollment->certificate) {
            return;
        }

        // Check if course has certificate enabled
        if (!$enrollment->course->certificate_available) {
            return;
        }

        // Generate unique certificate code
        $certificateCode = 'CERT-' . strtoupper(uniqid());

        // Create certificate
        Certificate::create([
            'user_id' => $enrollment->user_id,
            'course_id' => $enrollment->course_id,
            'enrollment_id' => $enrollment->id,
            'template_id' => $enrollment->course->certificate_template_id,
            'certificate_code' => $certificateCode,
            'issued_at' => now(),
            'issued_by' => $enrollment->course->instructor_id,
        ]);

        // Log activity
        activity()
            ->performedOn($enrollment)
            ->causedBy($enrollment->user)
            ->log('Certificate generated');

        // TODO: Send certificate notification email
    }
}
