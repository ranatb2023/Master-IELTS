<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseEnrolledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $course;
    protected $enrollment;

    public function __construct(Course $course, Enrollment $enrollment)
    {
        $this->course = $course;
        $this->enrollment = $enrollment;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Course Enrollment Confirmed',
            'message' => "You have been enrolled in {$this->course->title}",
            'action_url' => route('student.courses.show', $this->course),
            'icon' => 'academic-cap',
            'color' => 'blue',
            'metadata' => [
                'course_id' => $this->course->id,
                'enrollment_id' => $this->enrollment->id,
            ],
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Course Enrollment Confirmed')
            ->greeting("Hello {$notifiable->name}!")
            ->line("You have been successfully enrolled in {$this->course->title}.")
            ->line('Start learning now and make progress towards your goals.')
            ->action('View Course', route('student.courses.show', $this->course->id))
            ->line('Thank you for choosing our platform!');
    }
}
