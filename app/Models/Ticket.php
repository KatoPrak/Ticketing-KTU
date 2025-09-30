<?php
// app/Models/Ticket.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','category_id','description','attachments','status'];


    protected $casts = [ 
    'attachments' => 'array',
];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
public function attachments()
{
    return $this->hasMany(TicketAttachment::class); // atau nama model attachment kamu
}

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }
}
