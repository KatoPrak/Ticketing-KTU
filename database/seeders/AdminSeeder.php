<?php

// database/seeders/AdminSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Create or update admin user
        User::updateOrCreate(
            ['email' => 'admin@company.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('admin123'),
                'department' => 'it',
                'role' => 'admin',
                'employee_id' => 'ADMIN001',
                'status' => 'active',
                'phone' => '+1234567890',
            ]
        );
    }
}
