<?php

namespace Database\Seeders;

use App\Models\SpaceType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpaceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch services from JSON file
        $path = 'C:\\temp\\baleart\\tipus.json';
        $json = file_get_contents($path);
        $types = json_decode($json, true);

        // Create services
        foreach ($types['tipusespais']['tipus'] as $type) {
            SpaceType::create([
                'name' => $type['cat'],
                'description_CA' => $type['cat'],
                'description_ES' => $type['esp'],
                'description_EN' => $type['eng'],
            ]);
        }
    }
}
