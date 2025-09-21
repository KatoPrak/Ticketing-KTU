<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Sistem',
            'email' => 'admin@ktushipyard.com',
            'department' => 'IT',
            'role' => 'admin',
            'id_staff' => 'ADM001',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Staff User',
            'email' => 'staff@ktushipyard.com',
            'department' => 'Engineering',
            'role' => 'user',
            'id_staff' => 'STF001',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Tim IT',
            'email' => 'it@ktushipyard.com',
            'department' => 'IT',
            'role' => 'tim it',
            'id_staff' => 'IT001',
            'password' => Hash::make('password'),
        ]);
    }
}
