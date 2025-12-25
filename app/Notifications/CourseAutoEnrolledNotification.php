<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseAutoEnrolledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $course;

    /**
     * Create a new notification instance.
     */
    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Course Available: ' . $this->course->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Great news! A new course is now available for you.')
            ->line('**' . $this->course->title . '**')
            ->line($this->course->short_description ?? 'Start learning today!')
            ->action('Start Learning', route('student.courses.learn', ['course' => $this->course->slug]))
            ->line('This course has been automatically added to your dashboard.')
            ->line('Thank you for being part of our learning community!');
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'New Course Available',
            'message' => "'{$this->course->title}' is now available in your dashboard",
            'action_url' => route('student.courses.learn', ['course' => $this->course->slug]),
            'icon' => 'academic-cap',
            'color' => 'indigo',
            'metadata' => [
                'course_id' => $this->course->id,
                'course_slug' => $this->course->slug,
                'course_title' => $this->course->title,
            ],
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
        ];
    }
}
