<?php
// app/Models/Ticket.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'department_id',
        'category_id',
        'description',
        'attachments',
        'status',
        'ticket_id',
        'priority',
        'resolution_notes',
        'resolved_at',
        'assigned_to',
        'region_id' // ✅ Added region_id
    ];

    protected $casts = [
        'attachments' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    protected $appends = ['created_at_formatted', 'updated_at_formatted', 'resolved_at_formatted'];

    // ✅ ACCESSOR untuk format date dengan NULL-safe
    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at 
            ? $this->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') 
            : '-';
    }

    public function getUpdatedAtFormattedAttribute()
    {
        return $this->updated_at 
            ? $this->updated_at->timezone('Asia/Jakarta')->format('d M Y, H:i') 
            : 'Not yet responded';
    }

    public function getResolvedAtFormattedAttribute()
    {
        return $this->resolved_at 
            ? $this->resolved_at->timezone('Asia/Jakarta')->format('d M Y, H:i') 
            : 'Not yet resolved';
    }

    public function getResponseDateAttribute()
    {
        return $this->updated_at 
            ? $this->updated_at->timezone('Asia/Jakarta')->format('d M Y, H:i') 
            : 'Pending';
    }

    // Relationships
    public function feedback()
    {
        return $this->hasOne(TicketFeedback::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function transferLogs()
    {
        return $this->hasMany(TicketTransferLog::class)->with(['fromRegion', 'toRegion', 'transferredBy'])->latest();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    /**
     * ✅ LOGIC LENGKAP untuk timestamp:
     * 1. Ticket baru (status='waiting'): created_at ✅, updated_at = NULL, resolved_at = NULL
     * 2. Status → in_progress/pending: updated_at terisi PERTAMA KALI
     * 3. Status → resolved/closed: resolved_at terisi, updated_at FREEZE
     * 4. Priority berubah: updated_at TIDAK berubah jika masih waiting
     */
    protected static function booted()
    {
        // ✅ SAAT TICKET BARU DIBUAT
        static::creating(function ($ticket) {
            $ticket->created_at = now();
            $ticket->updated_at = null;
            $ticket->resolved_at = null;
            $ticket->timestamps = false;
        });

        static::created(function ($ticket) {
            $ticket->timestamps = true;
        });

        // ✅ SAAT TICKET DI-UPDATE
        static::updating(function ($ticket) {
            $ticket->timestamps = true;
            
            $isDirtyStatus = $ticket->isDirty('status');
            $currentStatus = $ticket->status;
            $oldStatus = $ticket->getOriginal('status');

            // ✅ RULE 1: Status berubah ke in_progress ATAU pending
            // → Set updated_at HANYA jika masih NULL (response pertama kali)
            if ($isDirtyStatus && in_array($currentStatus, ['in_progress', 'pending'])) {
                if (!$ticket->getOriginal('updated_at')) {
                    $ticket->updated_at = now();
                }
                // Jika sudah ada updated_at, biarkan Laravel auto-update
            }

            // ✅ RULE 2: Status berubah ke resolved/closed
            // → Set resolved_at, FREEZE updated_at
            if ($isDirtyStatus && in_array($currentStatus, ['resolved', 'closed'])) {
                $ticket->resolved_at = now();
                $ticket->timestamps = false; // Freeze updated_at
            }
            
            // ✅ RULE 3: Status kembali dari resolved/closed ke status lain
            // → Hapus resolved_at
            if ($isDirtyStatus && 
                in_array($oldStatus, ['resolved', 'closed']) && 
                !in_array($currentStatus, ['resolved', 'closed'])) {
                $ticket->resolved_at = null;
                $ticket->timestamps = true;
            }

            // ✅ RULE 4: Perubahan selain status (priority, description, dll)
            // → JANGAN update updated_at jika status masih waiting
            if (!$isDirtyStatus && $currentStatus === 'waiting') {
                $ticket->timestamps = false;
            }
        });

        static::updated(function ($ticket) {
            $ticket->timestamps = true;
        });
    }
}