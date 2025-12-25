<?php

namespace App\Notifications;

use App\Models\Lesson;
use App\Models\LessonComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LessonCommentReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $lesson;
    protected $reply;
    protected $originalComment;

    public function __construct(Lesson $lesson, LessonComment $reply, LessonComment $originalComment)
    {
        $this->lesson = $lesson;
        $this->reply = $reply;
        $this->originalComment = $originalComment;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        $course = $this->lesson->topic->course;
        $replierRole = $this->reply->is_from_tutor ? 'Tutor' : 'Admin';

        return [
            'title' => 'Reply to Your Comment',
            'message' => "{$this->reply->user->name} ({$replierRole}) replied to your comment on \"{$this->lesson->title}\"",
            'action_url' => route('student.courses.view-lesson', [
                $course,
                $this->lesson->topic_id,
                $this->lesson->id
            ]) . '#comment-' . $this->originalComment->id,
            'icon' => 'reply',
            'color' => 'green',
            'metadata' => [
                'lesson_id' => $this->lesson->id,
                'comment_id' => $this->originalComment->id,
                'reply_id' => $this->reply->id,
                'course_id' => $course->id,
            ],
        ];
    }

    public function toMail($notifiable)
    {
        $course = $this->lesson->topic->course;
        $replierRole = $this->reply->is_from_tutor ? 'Tutor' : 'Admin';

        return (new MailMessage)
            ->subject('Reply to Your Comment')
            ->greeting("Hello {$notifiable->name}!")
            ->line("{$this->reply->user->name} ({$replierRole}) has replied to your comment on the lesson \"{$this->lesson->title}\".")
            ->line('Your comment: ' . substr($this->originalComment->comment, 0, 150) . '...')
            ->line('Reply: ' . substr($this->reply->comment, 0, 200) . (strlen($this->reply->comment) > 200 ? '...' : ''))
            ->action('View Reply', route('student.courses.view-lesson', [
                $course,
                $this->lesson->topic_id,
                $this->lesson->id
            ]) . '#comment-' . $this->originalComment->id)
            ->line('Thank you for engaging with the course content!');
    }
}
