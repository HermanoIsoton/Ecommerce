<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categorie;

class CategorieSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Carros elétricos', 'slug' => 'carros-eletricos'],
            ['name' => 'Autopropelidos', 'slug' => 'autopropelidos'],
            ['name' => 'Ciclomotores', 'slug' => 'ciclomotores'],
        ];
        foreach($categories as $categorie){
            Categorie::create($categorie);
        }
    }
}
