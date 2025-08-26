<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Kolom yang bisa diisi mass-assignment.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_staff',
        'name',
        'email',
        'password',
        'role',
        'department',
    ];

    /**
     * Kolom yang disembunyikan ketika serialisasi.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting atribut.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Gunakan kolom id_staff sebagai username untuk autentikasi.
     */
    public function getAuthIdentifierName()
    {
        return 'id_staff';
    }
}
