<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition()
    {
        $departments = ['hr', 'finance', 'marketing', 'sales', 'operations', 'it', 'legal'];
        $roles = ['user', 'supervisor'];
        
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'department' => $this->faker->randomElement($departments),
            'role' => $this->faker->randomElement($roles),
            'employee_id' => 'EMP' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'phone' => $this->faker->phoneNumber(),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'admin',
                'department' => 'it',
                'status' => 'active',
            ];
        });
    }
}