<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart_item;

class Cart_itemSeeder extends Seeder
{
    public function run(): void
    {
        $cart_items = [
            ['cart_id' => 1, 'product_id' => 1, 'quantity' => 1],
            ['cart_id' => 1, 'product_id' => 2, 'quantity' => 2],
            ['cart_id' => 2, 'product_id' => 3, 'quantity' => 1],
            ['cart_id' => 3, 'product_id' => 3, 'quantity' => 2],
        ];
        foreach($cart_items as $cart_item){
            Cart_item::create($cart_item);
        }
    }
}
