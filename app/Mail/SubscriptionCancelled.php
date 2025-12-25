<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Laravel\Cashier\Subscription;

class SubscriptionCancelled extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $subscription;
    public $endsAt;

    /**
     * Create a new message instance.
     */
    public function __construct($user, Subscription $subscription)
    {
        $this->user = $user;
        $this->subscription = $subscription;
        $this->endsAt = $subscription->ends_at;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Subscription Cancelled - Access Until ' . $this->endsAt->format('M d, Y'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-cancelled',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
