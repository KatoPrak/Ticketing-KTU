<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
       // Admin
User::updateOrCreate(
    ['email' => 'admin@example.com'],
    [
        'name' => 'Admin Utama',
        'id_staff' => 1001, // integer, bukan string
        'role' => 'admin',
        'password' => Hash::make('password123'),
    ]
);

// IT
User::updateOrCreate(
    ['email' => 'it@example.com'],
    [
        'name' => 'IT Support',
        'id_staff' => 2001,
        'role' => 'it',
        'password' => Hash::make('password123'),
    ]
);

// User
User::updateOrCreate(
    ['email' => 'user@example.com'],
    [
        'name' => 'Pengguna Biasa',
        'id_staff' => 3001,
        'role' => 'user',
        'password' => Hash::make('password123'),
    ]
);
    }
}
