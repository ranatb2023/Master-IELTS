<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReviewReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        $stars = str_repeat('⭐', $this->review->rating);

        return [
            'title' => 'New Review Received',
            'message' => "{$this->review->user->name} left a {$this->review->rating}-star review on '{$this->review->course->title}'",
            'action_url' => route('tutor.courses.show', $this->review->course_id),
            'icon' => 'star',
            'color' => 'yellow',
            'metadata' => [
                'review_id' => $this->review->id,
                'course_id' => $this->review->course_id,
                'rating' => $this->review->rating,
            ],
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Review Received')
            ->greeting("Hello {$notifiable->name}!")
            ->line("{$this->review->user->name} left a {$this->review->rating}-star review on your course '{$this->review->course->title}'.")
            ->when($this->review->comment, function ($mail) {
                return $mail->line("Comment: \"{$this->review->comment}\"");
            })
            ->action('View Course', route('tutor.courses.show', $this->review->course_id))
            ->line('Thank you for creating quality content!');
    }
}
