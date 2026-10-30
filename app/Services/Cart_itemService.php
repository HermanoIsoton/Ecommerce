<?php

namespace App\Services;
use App\Models\Cart_item;
use Illuminate\Database\Eloquent\Collection;

class Cart_itemService
{
    public function listAll(): Collection
    {
        $cart_items = cart_item::all();

        return $cart_items;
    }

    public function create(array $data): cart_item
    {
        $cart_item = cart_item::create($data);

        return $cart_item;
    }

    public function findById(int $id): cart_item
    {
        $cart_item = cart_item::findOrFail($id);

        return $cart_item;
    }

    public function update(int $id, array $data): cart_item
    {
        $cart_item = cart_item::findOrFail($id);
        $cart_item->update($data);

        return $cart_item;
    }

    public function delete(int $id): void
    {
        $cart_item = cart_item::findOrFail($id);
        $cart_item->delete();
    }
}