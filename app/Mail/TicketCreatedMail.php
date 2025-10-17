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

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function build()
{
    // Lokasi logo di folder public
    $logoPath = public_path('assets/image/logo-ktu.jpg');

    $logoCid = null;
    if (file_exists($logoPath)) {
        $logoCid = $this->embed($logoPath);
    }

    return $this->subject('🎫 Tiket Baru: ' . $this->ticket->ticket_id)
                ->view('emails.new-ticket-notification')
                ->with([
                    'logoCid' => $logoCid,
                ]);
}

}