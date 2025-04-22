<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('stocks')->insert([
            ['id' => 1, 'lieux' => 'Mont de Marsan', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'lieux' => 'Aire sur Adour', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
