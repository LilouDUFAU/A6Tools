<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
        {
            $now = Carbon::now();
    
            DB::table('users')->insert(
            [
                'nom' => 'admin',
                'prenom' => 'admin',
                'email' => 'direction@a6landes.fr',
                'telephone' => '05 58 45 40 40',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password_par_defaut')),
                'service_id' => 3,
                'role_id' => 2,
                'stock_id' => 2,
                'updated_at' => $now,
                'created_at' => $now, // utile seulement si la ligne est créée
            ]
        );
    }
}
