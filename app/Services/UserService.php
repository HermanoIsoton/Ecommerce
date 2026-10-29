<?php

namespace App\Services;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function listAll(): Collection
    {
        $users = user::all();

        return $users;
    }

    public function create(array $data): user
    {
        $user = user::create($data);

        return $user;
    }

    public function findById(int $id): user
    {
        $user = user::findOrFail($id);

        return $user;
    }

    public function update(int $id, array $data): user
    {
        $user = user::findOrFail($id);
        $user->update($data);

        return $user;
    }

    public function delete(int $id): void
    {
        $user = user::findOrFail($id);
        $user->delete();
    }
}