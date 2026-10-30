<?php

namespace App\Services;
use App\Models\Address;
use Illuminate\Database\Eloquent\Collection;

class AddressService
{
    public function listAll(): Collection
    {
        $addresss = address::all();

        return $addresss;
    }

    public function create(array $data): address
    {
        $address = address::create($data);

        return $address;
    }

    public function findById(int $id): address
    {
        $address = address::findOrFail($id);

        return $address;
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