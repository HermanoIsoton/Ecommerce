<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('orders')->insert([
            [
                'user_id' => 2,
                'status' => 'entregue',
                'subtotal_cents' => 119900.00,
                'shipping_cents' => 250.00,
                'total_cents' => 120150.00,
                'ship_recipient' => 'João Silva',
                'ship_cep' => '01001-000',
                'ship_street' => 'Praça da Sé',
                'ship_number' => '100',
                'ship_complement' => 'Apartamento 12',
                'ship_district' => 'Sé',
                'ship_city' => 'São Paulo',
                'ship_state' => 'SP',
                'paid_at' => $now->copy()->subDays(5),
                'shipped_at' => $now->copy()->subDays(4),
                'delivered_at' => $now->copy()->subDays(2),
                'cancelled_at' => null,
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now,
            ],
            [
                'user_id' => 3,
                'status' => 'pago',
                'subtotal_cents' => 7990.00,
                'shipping_cents' => 29.90,
                'total_cents' => 8019.90,
                'ship_recipient' => 'Maria Santos',
                'ship_cep' => '20040-002',
                'ship_street' => 'Avenida Rio Branco',
                'ship_number' => '450',
                'ship_complement' => '',
                'ship_district' => 'Centro',
                'ship_city' => 'Rio de Janeiro',
                'ship_state' => 'RJ',
                'paid_at' => $now->copy()->subDay(),
                'shipped_at' => null,
                'delivered_at' => null,
                'cancelled_at' => null,
                'created_at' => $now->copy()->subDay(),
                'updated_at' => $now,
            ],
            [
                'user_id' => 4,
                'status' => 'pendente',
                'subtotal_cents' => 15990.00,
                'shipping_cents' => 49.90,
                'total_cents' => 16039.90,
                'ship_recipient' => 'Pedro Oliveira',
                'ship_cep' => '30130-110',
                'ship_street' => 'Rua da Bahia',
                'ship_number' => '789',
                'ship_complement' => 'Bloco B',
                'ship_district' => 'Centro',
                'ship_city' => 'Belo Horizonte',
                'ship_state' => 'MG',
                'paid_at' => null,
                'shipped_at' => null,
                'delivered_at' => null,
                'cancelled_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
