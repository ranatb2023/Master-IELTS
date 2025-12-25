<?php

namespace App\Mail;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RefundProcessed extends Mailable
{
    use Queueable, SerializesModels;

    public $enrollment;
    public $refundAmount;
    public $refundReason;
    public $user;
    public $course;

    /**
     * Create a new message instance.
     */
    public function __construct(Enrollment $enrollment, float $refundAmount, string $refundReason)
    {
        $this->enrollment = $enrollment;
        $this->refundAmount = $refundAmount;
        $this->refundReason = $refundReason;
        $this->user = $enrollment->user;
        $this->course = $enrollment->course;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Refund Processed - ' . $this->course->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.refund-processed',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
