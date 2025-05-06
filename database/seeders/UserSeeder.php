<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        User::updateOrCreate(
            ['email' => 'direction@a6landes.fr'], // critère d'unicité
            [
                'nom' => 'admin',
                'prenom' => 'admin',
                'telephone' => '05 58 45 40 40',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password_par_defaut')),
                'photo' => 'https://www.a6landes.fr/wp-content/uploads/2023/01/logo-a6-landes.png',
                'service_id' => 3,
                'role_id' => 2,
                'updated_at' => $now,
                'created_at' => $now, // utile seulement si la ligne est créée
            ]
        );
    }
}
