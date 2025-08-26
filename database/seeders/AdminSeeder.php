<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@ktu.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'department' => 'IT Management'
            ]
        );
    }
}
