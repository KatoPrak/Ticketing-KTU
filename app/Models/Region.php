<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'rmail'];

    // Relasi ke Locations
    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    // Relasi ke IT Staff (User)
    public function staff()
    {
        return $this->hasMany(User::class);
    }
}
