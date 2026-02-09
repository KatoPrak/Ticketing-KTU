<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketTransferLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'from_region_id',
        'to_region_id',
        'transferred_by',
        'note'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function fromRegion()
    {
        return $this->belongsTo(Region::class, 'from_region_id');
    }

    public function toRegion()
    {
        return $this->belongsTo(Region::class, 'to_region_id');
    }

    public function transferredBy()
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }
}
