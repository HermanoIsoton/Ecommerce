<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order_item;

class Order_itemSeeder extends Seeder
{
    public function run(): void
    {
        $order_items = [
            [
                'order_id' => 1,
                'product_id' => 1,
                'product_name' => 'Patinete Urban Sprint',
                'unit_price' => 2890.00,
                'quantity' => 1,
                'subtotal_cents' => 2890.00,
            ],
            [
                'order_id' => 2,
                'product_id' => 2,
                'product_name' => 'Bike Elétrica Trilha 500',
                'unit_price' => 6490.00,
                'quantity' => 1,
                'subtotal_cents' => 6490.00,
            ],
            [
                'order_id' => 3,
                'product_id' => 3,
                'product_name' => 'Moto Elétrica E-Moto',
                'unit_price' => 15990.00,
                'quantity' => 1,
                'subtotal_cents' => 15990.00,
            ],
        ];
        foreach($order_items as $order_item){
            Order_item::create($order_item);
        }
    }
}
