<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketTransferredNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $ticket;
    public $transferredBy;
    public $fromRegion;
    public $toRegion;
    public $note;

    /**
     * Create a new message instance.
     */
    public function __construct($ticket, $transferredBy, $fromRegion, $toRegion, $note = null)
    {
        $this->ticket = $ticket;
        $this->transferredBy = $transferredBy;
        $this->fromRegion = $fromRegion;
        $this->toRegion = $toRegion;
        $this->note = $note;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ticket #' . ($this->ticket->ticket_id ?? $this->ticket->id) . ' Masuk ke Regional Anda',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-transferred',
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
