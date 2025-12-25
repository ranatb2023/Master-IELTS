<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class PackagePurchased extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $invoice;
    public $package;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, Invoice $invoice)
    {
        $this->order = $order;
        $this->invoice = $invoice;
        $this->package = $order->items()->first()->item;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Purchase Confirmation - ' . $this->package->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.package-purchased',
            with: [
                'order' => $this->order,
                'invoice' => $this->invoice,
                'package' => $this->package,
                'user' => $this->order->user,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        // Generate PDF and attach to email
        $pdf = Pdf::loadView('invoices.package-invoice', [
            'invoice' => $this->invoice,
            'order' => $this->order,
            'package' => $this->package,
            'user' => $this->order->user,
        ]);

        return [
            Attachment::fromData(fn() => $pdf->output(), "invoice-{$this->invoice->invoice_number}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
