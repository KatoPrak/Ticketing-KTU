<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Hardware Issue',
                'description' => 'Problems with physical devices and equipment',
                'icon' => 'laptop',
                'color' => '#3b82f6'
            ],
            [
                'name' => 'Software Problem',
                'description' => 'Application and software related issues',
                'icon' => 'code',
                'color' => '#8b5cf6'
            ],
            [
                'name' => 'Network/Internet',
                'description' => 'Connectivity and network related problems',
                'icon' => 'wifi',
                'color' => '#10b981'
            ],
            [
                'name' => 'Email Issue',
                'description' => 'Email client and server problems',
                'icon' => 'envelope',
                'color' => '#f59e0b'
            ],
            [
                'name' => 'Printer/Scanner',
                'description' => 'Printing and scanning device issues',
                'icon' => 'print',
                'color' => '#ef4444'
            ],
            [
                'name' => 'Access/Login',
                'description' => 'Account access and authentication problems',
                'icon' => 'key',
                'color' => '#06b6d4'
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
