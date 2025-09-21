<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition()
    {
        $colors = ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444', '#06b6d4'];
        $icons = ['laptop', 'code', 'wifi', 'envelope', 'print', 'key', 'shield-alt'];
        
        return [
            'name' => $this->faker->unique()->words(2, true),
            'description' => $this->faker->sentence(),
            'icon' => $this->faker->randomElement($icons),
            'color' => $this->faker->randomElement($colors),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
