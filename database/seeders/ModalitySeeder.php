<?php

namespace Database\Seeders;

use App\Models\Modality;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch modalities from JSON file
        $path = 'C:\\temp\\baleart\\modalitats.json';
        $json = file_get_contents($path);
        $modalities = json_decode($json, true);

        // Create modalities
        foreach ($modalities['modalitats']['modalitat'] as $modality) {
            Modality::create([
                'name' => $modality['cat'],
                'description_CA' => $modality['cat'],
                'description_ES' => $modality['esp'],
                'description_EN' => $modality['eng'],
            ]);
        }
    }
}
