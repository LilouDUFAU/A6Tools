<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Exécute les seeds de la base de données.
     */
    public function run(): void
    {
        DB::table('services')->insert([
            [
                'id' => 1,
                'nom' => 'Commercial',
                'description' => 'Gère les relations clients et les ventes.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'nom' => 'Technique',
                'description' => 'Responsable du support technique et de la maintenance.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'nom' => 'Administratif',
                'description' => 'Gère les tâches et opérations administratives.',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
