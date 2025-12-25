<?php

namespace App\Notifications;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewEnrollmentNotification extends Notification
{
    use Queueable;

    protected $enrollment;

    public function __construct(Enrollment $enrollment)
    {
        $this->enrollment = $enrollment;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Course Enrollment',
            'message' => "{$this->enrollment->user->name} enrolled in {$this->enrollment->course->title}",
            'action_url' => route('admin.enrollments.show', $this->enrollment),
            'icon' => 'academic-cap',
            'color' => 'green',
            'metadata' => [
                'enrollment_id' => $this->enrollment->id,
                'user_id' => $this->enrollment->user_id,
                'course_id' => $this->enrollment->course_id,
            ],
        ];
    }
}
