<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CoursePublishedNotification extends Notification
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
        return [
            'title' => 'Course Published',
            'message' => "Your course '{$this->course->title}' has been published and is now live!",
            'action_url' => route('tutor.courses.show', $this->course),
            'icon' => 'sparkles',
            'color' => 'green',
            'metadata' => [
                'course_id' => $this->course->id,
            ],
        ];
    }
}
