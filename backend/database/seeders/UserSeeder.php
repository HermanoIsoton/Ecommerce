<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Hermano Admin',
                'email' => 'hermanoadmin@gmail.com',
                'password' => Hash::make('adm'),
                'role' => 'admin',
                'remember_token' => null,
            ],
            [
                'name' => 'João Silva',
                'email' => 'joao@ecommerce.test',
                'password' => Hash::make('password'),
                'role' => 'user',
                'remember_token' => null,
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria@ecommerce.test',
                'password' => Hash::make('password'),
                'role' => 'user',
                'remember_token' => null,
            ],
            [
                'name' => 'Pedro Oliveira',
                'email' => 'pedro@ecommerce.test',
                'password' => Hash::make('password'),
                'role' => 'user',
                'remember_token' => null,
            ],
        ];
        foreach($users as $user){
            User::create($user);
        }
    }
}
