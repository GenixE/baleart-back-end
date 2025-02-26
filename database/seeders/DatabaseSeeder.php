<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Modality;
use App\Models\Service;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Order matters here for correct seeding
        $this->call([
            RoleSeeder::class,           // Seed roles first as users depend on roles
            UserSeeder::class,           // Seed users after roles
            IslandSeeder::class,         // Islands should be seeded before municipalities
            MunicipalitySeeder::class,   // Municipalities come after islands
            ZoneSeeder::class,           // Zones follow municipalities
            SpaceTypeSeeder::class,      // Space types should be seeded before spaces
            ModalitySeeder::class,       // Modalities should be seeded before spaces
            ServiceSeeder::class,        // Services should be seeded before spaces
            SpaceSeeder::class,          // Spaces should be seeded before comments
            CommentSeeder::class,        // Comments come after spaces and users
        ]);

        // Seed users first as they have the visitor role
        User::factory(10)->create();

        // Seed images linked to comments
        Image::factory(10)->create();
    }
}
