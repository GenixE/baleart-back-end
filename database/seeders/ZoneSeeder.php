<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = 'C:\\temp\\baleart\\zones.json';
        $json = file_get_contents($path);
        $zones = json_decode($json, true);

        foreach ($zones['zones']['zona'] as $zone) {
            Zone::create([
                'name' => $zone['Nom'],
            ]);
        }
    }
}
