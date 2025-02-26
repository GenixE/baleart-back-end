<?php

namespace Database\Seeders;

use App\Models\Island;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IslandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $islands = array("Mallorca", "Menorca", "Eivissa", "Formentera");

        foreach ($islands as $island) {
            $admin = new Island();
            $admin->name = $island;
            $admin->save();
        }

    }
}
