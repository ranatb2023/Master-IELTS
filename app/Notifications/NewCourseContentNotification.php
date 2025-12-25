<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCourseContentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $course;
    protected $lesson;

    public function __construct(Course $course, Lesson $lesson)
    {
        $this->course = $course;
        $this->lesson = $lesson;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Content Available',
            'message' => "New lesson '{$this->lesson->title}' added to {$this->course->title}",
            'action_url' => route('student.lessons.show', $this->lesson),
            'icon' => 'sparkles',
            'color' => 'purple',
            'metadata' => [
                'course_id' => $this->course->id,
                'lesson_id' => $this->lesson->id,
            ],
        ];
    }
}
