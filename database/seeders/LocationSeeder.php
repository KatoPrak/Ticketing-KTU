<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Illuminate\Support\Facades\DB::table('locations')->insert([
            [
                'name' => 'Jakarta - Marunda',
                'description' => 'Cilincing, Jakarta Utara (Regional 1)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sagulung - Tanjung Uncang',
                'description' => 'Sungai Binti, Sagulung, Batam (Regional 2)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tanjung Riau - Sekupang',
                'description' => 'Kawasan Industri Sekupang, Batam (Regional 3) ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
