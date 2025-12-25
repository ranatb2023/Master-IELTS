<?php

namespace App\Notifications;

use App\Models\Lesson;
use App\Models\LessonComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewLessonCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $lesson;
    protected $comment;

    public function __construct(Lesson $lesson, LessonComment $comment)
    {
        $this->lesson = $lesson;
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        $course = $this->lesson->topic->course;

        return [
            'title' => 'New Lesson Comment',
            'message' => "{$this->comment->user->name} commented on \"{$this->lesson->title}\"",
            'action_url' => $notifiable->hasAnyAdminRole()
                ? route('admin.lessons.show', $this->lesson) . '#comments'
                : route('tutor.lessons.show', $this->lesson) . '#comments',
            'icon' => 'chat-alt-2',
            'color' => 'blue',
            'metadata' => [
                'lesson_id' => $this->lesson->id,
                'comment_id' => $this->comment->id,
                'student_id' => $this->comment->user_id,
                'course_id' => $course->id,
            ],
        ];
    }

    public function toMail($notifiable)
    {
        $course = $this->lesson->topic->course;

        return (new MailMessage)
            ->subject('New Comment on Lesson: ' . $this->lesson->title)
            ->greeting("Hello {$notifiable->name}!")
            ->line("{$this->comment->user->name} has posted a comment on the lesson \"{$this->lesson->title}\" in the course \"{$course->title}\".")
            ->line('Comment: ' . substr($this->comment->comment, 0, 200) . (strlen($this->comment->comment) > 200 ? '...' : ''))
            ->action('View Comment', $notifiable->hasAnyAdminRole()
                ? route('admin.lessons.show', $this->lesson) . '#comments'
                : route('tutor.lessons.show', $this->lesson) . '#comments')
            ->line('Please review and respond to the student\'s comment.');
    }
}
