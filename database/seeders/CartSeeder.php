<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('carts')->insert([
            ['user_id' => 2, 'token_hash' => null, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => 3, 'token_hash' => null, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => 4, 'token_hash' => hash('sha256', 'visitante-1'), 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
