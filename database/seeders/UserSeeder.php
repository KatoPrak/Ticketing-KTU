<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $itDept = \Illuminate\Support\Facades\DB::table('departments')->where('name', 'IT')->first()?->id 
                  ?? \Illuminate\Support\Facades\DB::table('departments')->insertGetId(['name' => 'IT', 'created_at' => now(), 'updated_at' => now()]);
        
        $engDept = \Illuminate\Support\Facades\DB::table('departments')->where('name', 'Engineering')->first()?->id 
                   ?? \Illuminate\Support\Facades\DB::table('departments')->insertGetId(['name' => 'Engineering', 'created_at' => now(), 'updated_at' => now()]);

        \Illuminate\Support\Facades\DB::table('users')->insert([
            [
                'name' => 'Admin Sistem',
                'email' => 'admin@ktushipyard.com',
                'department_id' => $itDept,
                'role' => 'admin',
                'nik' => 'ADM001',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Staff User',
                'email' => 'staff@ktushipyard.com',
                'department_id' => $engDept,
                'role' => 'user',
                'nik' => 'STF001',
                'username' => 'staff',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tim IT',
                'email' => 'it@ktushipyard.com',
                'department_id' => $itDept,
                'role' => 'tim it',
                'nik' => 'IT001',
                'username' => 'it.team',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
