<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Laravel\Passport\Passport;

class UserSeeder extends Seeder
{
    public function run() {
        $user = User::create([
            'name' => 'Prueba Usuario', 
            'email' => 'prueba@example.com',
            'password' => bcrypt('prueba'), 
        ]);

        $token = $user->createToken('Personal Access Token')->accessToken;

        echo "Token de acceso: " . $token . "\n";
    }
}
