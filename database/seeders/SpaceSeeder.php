<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Municipality;
use App\Models\Space;
use App\Models\SpaceType;
use App\Models\Modality;
use App\Models\Service;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class SpaceSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch spaces from JSON file
        $path = 'C:\\temp\\baleart\\espais.json';
        $json = file_get_contents($path);
        $spaces = json_decode($json, true);

        foreach ($spaces as $space) {
            $addresses = Address::create([
                'name' => $space['adreca'],
                'municipality_id' => Municipality::where('name', $space['municipi'])->first()->id,
                'zone_id' => Zone::where('name', $space['zona'])->first()->id,
            ]);
            $spaceId = $space['registre'];
            $totalScore = $scores[$spaceId]['totalScore'] ?? 0;
            $countScore = $scores[$spaceId]['countScore'] ?? 0;

            $spaceRecord = Space::create([
                'name' => $space['nom'],
                'reg_number' => $spaceId,
                'observation_CA' => $space['descripcions/cat'],
                'observation_ES' => $space['descripcions/esp'],
                'observation_EN' => $space['descripcions/eng'],
                'email' => $space['email'],
                'phone' => $space['telefon'],
                'website' => $space['web'],
                'accessType' => $space['accessibilitat'] === 'Sí' ? 'A' : ($space['accessibilitat'] === 'No' ? 'B' : 'M'),
                'totalScore' => 0,
                'countScore' => 0,
                'address_id' => $addresses->id,
                'space_type_id' => SpaceType::where('name', $space['tipus'])->first()?->id,
                'user_id' => User::where('email', $space['gestor'])->firstOr(function () {
                    return User::where('email', 'admin@baleart.com')->first();
                })->id,
            ]);

            // Attach services
            if (!empty($space['serveis'])) {
                $services = array_map('trim', explode(',', $space['serveis']));
                foreach ($services as $serviceName) {
                    $service = Service::where('name', $serviceName)->first();
                    if ($service) {
                        $spaceRecord->services()->attach($service->id);
                    } else {
                        throw new \Exception("Service not found: " . $serviceName);
                    }
                }
            }

            // Attach modalities
            if (!empty($space['modalitats'])) {
                $modalities = array_map('trim', explode(',', $space['modalitats']));
                foreach ($modalities as $modalityName) {
                    $modality = Modality::where('name', $modalityName)->first();
                    if ($modality) {
                        $spaceRecord->modalities()->attach($modality->id);
                    } else {
                        echo "Modality not found: " . $modalityName . "\n";
                    }
                }
            }
        }
    }
}
