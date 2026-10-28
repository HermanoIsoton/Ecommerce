<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Order_itemSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('order_items')->insert([
            [
                'order_id' => 1,
                'product_id' => 1,
                'product_name' => 'Carro Elétrico Urbano E4',
                'unit_price' => 119900.00,
                'quantity' => 1,
                'subtotal_cents' => 119900.00,
            ],
            [
                'order_id' => 2,
                'product_id' => 2,
                'product_name' => 'Autopropelido City',
                'unit_price' => 7990.00,
                'quantity' => 1,
                'subtotal_cents' => 7990.00,
            ],
            [
                'order_id' => 3,
                'product_id' => 3,
                'product_name' => 'Ciclomotor Elétrico E-Moto',
                'unit_price' => 15990.00,
                'quantity' => 1,
                'subtotal_cents' => 15990.00,
            ],
        ]);
    }
}
