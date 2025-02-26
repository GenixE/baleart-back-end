<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = 'C:\\temp\\baleart\\rols.json';
        $json = file_get_contents($path);
        $roles = json_decode($json, true);

        foreach ($roles['roles']['role'] as $role) {
            Role::create([
                'name' => $role['Nom'],
         ]);
}
    }
}
