<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Regions
        $regions = [
            'Regional Jakarta' => ['Jakarta', 'Marunda'],
            'Regional Sagulung' => ['Sagulung', 'TJU 1 Kumala', 'TJU 2 Amedco', 'TJU 3 CIS'], // TJU locations now part of Sagulung
            'Regional Sekupang' => ['Sekupang 1', 'Sekupang 2', 'Tanjung Riau'],
        ];

        foreach ($regions as $regionName => $locationNames) {
            $region = \App\Models\Region::firstOrCreate(
                ['name' => $regionName],
                ['description' => 'Coverage area for ' . $regionName]
            );

            // 2. Assign Locations to Region
            \App\Models\Location::whereIn('name', $locationNames)->update(['region_id' => $region->id]);
        }
    }
}
