<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categorie;

class CategorieSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Patinete Eletrico', 'slug' => 'patinetes-eletricos'],
            ['name' => 'Bicicleta Eletrica', 'slug' => 'bicicletas-eletricas'],
            ['name' => 'Moto Eletrica', 'slug' => 'motos-eletricas'],
        ];
        foreach($categories as $categorie){
            Categorie::create($categorie);
        }
    }
}
