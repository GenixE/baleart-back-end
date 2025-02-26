<?php

namespace Database\Seeders;

use App\Models\Island;
use App\Models\Municipality;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MunicipalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch municipalities from JSON file
        $path = 'C:\\temp\\baleart\\municipis.json';
        $json = file_get_contents($path);
        $municipalities = json_decode($json, true);

        // Create municipalities
        foreach ($municipalities['municipis']['municipi'] as $municipality) {
            Municipality::create([
                'name' => $municipality['Nom'],
                'island_id' => Island::where('name', $municipality['Illa'])->first()->id,
            ]);
        }
    }
}
