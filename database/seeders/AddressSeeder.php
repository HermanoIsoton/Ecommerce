<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('addresses')->insert([
            [
                'user_id' => 2,
                'recipient' => 'João Silva',
                'cep' => '01001-000',
                'street' => 'Praça da Sé',
                'number' => '100',
                'complement' => 'Apartamento 12',
                'district' => 'Sé',
                'city' => 'São Paulo',
                'state' => 'SP',
            ],
            [
                'user_id' => 3,
                'recipient' => 'Maria Santos',
                'cep' => '20040-002',
                'street' => 'Avenida Rio Branco',
                'number' => '450',
                'complement' => null,
                'district' => 'Centro',
                'city' => 'Rio de Janeiro',
                'state' => 'RJ',
            ],
            [
                'user_id' => 4,
                'recipient' => 'Pedro Oliveira',
                'cep' => '30130-110',
                'street' => 'Rua da Bahia',
                'number' => '789',
                'complement' => 'Bloco B',
                'district' => 'Centro',
                'city' => 'Belo Horizonte',
                'state' => 'MG',
            ],
        ]);
    }
}
