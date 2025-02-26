<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin seeder
        $admin = new User();
        $admin->name = "admin";
        $admin->lastName = "admin";
        $admin->email = "admin@baleart.com";
        $admin->phone = "123456789";
        $admin->password = "12345678";
        $admin->role_id = Role::where('name', 'administrador')->value('id');
        $admin->save();

        // Fetch gestors from JSON file
        $path = 'C:\\temp\\baleart\\usuaris.json';
        $json = file_get_contents($path);
        $users = json_decode($json, true);

        // Create gestors
        foreach ($users['usuaris']['usuari'] as $user) {
            User::create([
                'name' => $user['nom'],
                'lastName' => $user['llinatges'],
                'email' => $user['email'],
                'phone' => $user['telefon'],
                'password' => $user['password'],
                'role_id' => Role::where('name', 'gestor')->first()->id
            ]);
        }
    }
}
