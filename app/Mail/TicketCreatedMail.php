<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;

    /**
     * Buat instance baru dari mail ini.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Bangun pesan email.
     */
    public function build()
    {
        $subject = sprintf('🎫 Tiket Baru Diterima: %s', $this->ticket->ticket_id);

        return $this->subject($subject)
                    ->view('emails.new-ticket-notification')
                    ->with([
                        'ticket' => $this->ticket,
                    ]);
    }
}
