<?php

namespace App\Helpers;

use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class TicketHelper
{
    /**
     * Generate unique ticket ID with format: YYMM-XXX
     * Example: 2602-001
     */
    public static function generateTicketId(): string
    {
        return DB::transaction(function () {
            $dateCode = now()->format('ym'); // Format: YYMM
            
            // Lock the table to prevent race conditions
            $lastTicket = Ticket::where('ticket_id', 'like', $dateCode . '-%')
                ->lockForUpdate()
                ->orderBy('ticket_id', 'desc')
                ->first();
            
            $sequence = 1;
            if ($lastTicket) {
                $parts = explode('-', $lastTicket->ticket_id);
                if (isset($parts[1]) && is_numeric($parts[1])) {
                    $sequence = intval($parts[1]) + 1;
                }
            }
            
            // Format: YYMM-XXX (e.g., 2602-001)
            return $dateCode . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
        });
    }
}
