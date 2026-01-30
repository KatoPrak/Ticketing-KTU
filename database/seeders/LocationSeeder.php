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
                'name' => 'Sagulung',
                'description' => 'Sungai Binti, Sagulung, Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tanjung Riau',
                'description' => 'Kawasan Industri Sekupang, Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sekupang',
                'description' => 'Jl. RE. Martadinata KM 2, Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Marunda',
                'description' => 'Cilincing, Jakarta Utara',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
