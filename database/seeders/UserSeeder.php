<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Administrador',
                'email' => 'admin@ecommerce.test',
                'password_hash' => Hash::make('password'),
                'role' => 'admin',
                'remember_token' => null,
            ],
            [
                'name' => 'João Silva',
                'email' => 'joao@ecommerce.test',
                'password_hash' => Hash::make('password'),
                'role' => 'user',
                'remember_token' => null,
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria@ecommerce.test',
                'password_hash' => Hash::make('password'),
                'role' => 'user',
                'remember_token' => null,
            ],
            [
                'name' => 'Pedro Oliveira',
                'email' => 'pedro@ecommerce.test',
                'password_hash' => Hash::make('password'),
                'role' => 'user',
                'remember_token' => null,
            ],
        ]);
    }
}
