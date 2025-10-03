<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;

class NewsSeeder extends Seeder
{
    public function run()
    {
        News::create([
            'title' => 'System Maintenance',
            'content' => 'We will have scheduled maintenance this weekend.',
            'category_id' => 1, // pastikan category id 1 ada
            'created_by' => 1,  // misal user admin
        ]);

        News::create([
            'title' => 'New IT Policy',
            'content' => 'Please follow the updated IT usage policy effective immediately.',
            'category_id' => 2,
            'created_by' => 2, // staff
        ]);

        News::create([
            'title' => 'Network Upgrade',
            'content' => 'Our internet speed will be doubled after the upcoming upgrade.',
            'category_id' => 3,
            'created_by' => 3, // tim it
        ]);
    }
}
