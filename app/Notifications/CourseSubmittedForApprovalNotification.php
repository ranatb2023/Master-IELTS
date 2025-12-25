<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CourseSubmittedForApprovalNotification extends Notification
{
    use Queueable;

    protected $course;

    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $tutorName = $this->course->instructor->name ?? 'Unknown';

        return [
            'title' => 'Course Pending Approval',
            'message' => "{$tutorName} submitted '{$this->course->title}' for approval",
            'action_url' => route('admin.courses.show', $this->course),
            'icon' => 'clipboard-check',
            'color' => 'yellow',
            'metadata' => [
                'course_id' => $this->course->id,
                'instructor_id' => $this->course->instructor_id,
            ],
        ];
    }
}
