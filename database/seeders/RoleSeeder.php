<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['id' => 1, 'nom' => 'user', 'description' => 'Role permettant d\'utiliser les fonctionnalités de base du système', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nom' => 'admin', 'description' => 'Role permettant d\'administrer le système', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
