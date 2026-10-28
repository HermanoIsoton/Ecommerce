<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorieSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            ['name' => 'Carros elétricos', 'slug' => 'carros-eletricos'],
            ['name' => 'Autopropelidos', 'slug' => 'autopropelidos'],
            ['name' => 'Ciclomotores', 'slug' => 'ciclomotores'],
        ]);
    }
}
