<?php
// app/Models/Ticket.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    // Gunakan use HasFactory; jika ada
    use HasFactory;
    
    // Pastikan semua kolom yang diisi ada di sini
    protected $fillable = [
        'user_id',
        'department_id',
        'category_id',
        'description',
        'attachments',
        'status',
        'ticket_id', // tambahkan ticket_id dan priority jika perlu
        'priority',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    'attachments' => 'array',
];
protected $appends = ['created_at_formatted'];

public function getCreatedAtFormattedAttribute()
{
    return $this->created_at ? $this->created_at->format('d M Y H:i') : null;
}

    public function user()
    {
        return $this->belongsTo(User::class);
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

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }
}