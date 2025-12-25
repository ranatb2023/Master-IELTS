<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewStudentEnrolledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $course;
    protected $student;
    protected $enrollment;

    public function __construct(Course $course, $student, Enrollment $enrollment)
    {
        $this->course = $course;
        $this->student = $student;
        $this->enrollment = $enrollment;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Student Enrolled',
            'message' => "{$this->student->name} enrolled in your course '{$this->course->title}'",
            'action_url' => route('tutor.courses.show', $this->course),
            'icon' => 'user-add',
            'color' => 'blue',
            'metadata' => [
                'course_id' => $this->course->id,
                'student_id' => $this->student->id,
                'enrollment_id' => $this->enrollment->id,
            ],
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Student Enrolled')
            ->greeting("Hello {$notifiable->name}!")
            ->line("{$this->student->name} has enrolled in your course '{$this->course->title}'.")
            ->line('You now have one more student to inspire and guide!')
            ->action('View Course', route('tutor.courses.show', $this->course->id))
            ->line('Keep up the great teaching!');
    }
}
