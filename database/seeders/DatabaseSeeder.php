<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorieSeeder::class,
            AddressSeeder::class,
            ProductSeeder::class,
            CartSeeder::class,
            Cart_itemSeeder::class,
            OrderSeeder::class,
            Order_itemSeeder::class,
        ]);
    }
}
