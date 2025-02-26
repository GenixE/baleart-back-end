<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch services from JSON file
        $path = 'C:\\temp\\baleart\\serveis.json';
        $json = file_get_contents($path);
        $services = json_decode($json, true);

        // Create services
        foreach ($services['serveis']['servei'] as $service) {
            Service::create([
                'name' => $service['cat'],
                'description_CA' => $service['cat'],
                'description_ES' => $service['esp'],
                'description_EN' => $service['eng'],
            ]);
        }
    }
}
