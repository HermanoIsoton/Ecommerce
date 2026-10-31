<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function listAll(): Collection
    {
        $users = User::all();

        return $users;
    }

    public function create(array $data): User
    {
        $data['password_hash'] = Hash::make($data['password_hash']);
        $user = User::create($data);

        return $user;
    }

    public function findById(int $id): User
    {
        $user = User::findOrFail($id);

        return $user;
    }

    public function update(int $id, array $data): User
    {
        $user = User::findOrFail($id);

        if (array_key_exists('password_hash', $data)) {
            $data['password_hash'] = Hash::make($data['password_hash']);
        }

        $user->update($data);

        return $user;
    }

    public function delete(int $id): void
    {
        $user = User::findOrFail($id);
        $user->delete();
    }
}
