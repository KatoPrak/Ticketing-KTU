<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // Tambahkan kategori default (id = 1) agar aman untuk relasi di news
        Category::firstOrCreate(
            ['id' => 1],
            ['name' => 'General']
        );

        // Daftar kategori lain
        $categories = [
            'Hardware Issue',
            'Software Problem',
            'Network/Internet',
            'Email Issue',
            'Printer/Scanner',
            'Access/Login',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(
                ['name' => $name],
                ['name' => $name]
            );
        }
    }
}
