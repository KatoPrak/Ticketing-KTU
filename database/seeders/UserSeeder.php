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
            'department_id' => 9,
            'role' => 'admin',
            'nik' => 'ADM001',
            'username' => 'admin',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Staff User',
            'email' => 'staff@ktushipyard.com',
            'department_id' => 7,
            'role' => 'user',
            'nik' => 'STF001',
            'username' => 'staff',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Tim IT',
            'email' => 'it@ktushipyard.com',
            'department_id' => 9,
            'role' => 'tim it',
            'nik' => 'IT001',
            'username' => 'it.team',
            'password' => Hash::make('password'),
        ]);
    }
}
