<?php

namespace App\Services;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AddressService
{
    public function listAll(User $user): Collection
    {
        $query = Address::query();

        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        return $query->get();
    }

    public function create(array $data): address
    {
        $address = address::create($data);

        return $address;
    }

    public function findById(User $user, int $id): Address
    {
        $query = Address::query();

        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        return $query->findOrFail($id);
    }

    public function update(int $id, array $data): address
    {
        $address = address::findOrFail($id);
        $address->update($data);

        return $address;
    }

    public function delete(int $id): void
    {
        $address = address::findOrFail($id);
        $address->delete();
    }
}