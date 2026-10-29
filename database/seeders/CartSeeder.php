<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        

        $carts = [
            ['user_id' => 2, 'token_hash' => null],
            ['user_id' => 3, 'token_hash' => null],
            ['user_id' => 4, 'token_hash' => hash('sha256', 'visitante-1')],
        ];
        foreach($carts as $cart){
            Cart::create($cart);
        }
    }
}
