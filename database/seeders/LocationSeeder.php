<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['id' => 1, 'name' => 'Jakarta'],
            ['id' => 2, 'name' => 'Marunda'],
            ['id' => 3, 'name' => 'Sagulung'],
            ['id' => 4, 'name' => 'Tanjung Uncang'],
            ['id' => 7, 'name' => 'Tanjung Riau'],
            ['id' => 8, 'name' => 'Sekupang 1'],
            ['id' => 9, 'name' => 'Sekupang 2'],
        ];

        foreach ($locations as $loc) {
            DB::table('locations')->updateOrInsert(['id' => $loc['id']], [
                'name' => $loc['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
