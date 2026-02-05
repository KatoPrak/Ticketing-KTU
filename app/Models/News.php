<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = ['message', 'location_id'];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}