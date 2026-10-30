<?php

namespace App\Services;
use App\Models\Cart;
use Illuminate\Database\Eloquent\Collection;

class CartService
{
    public function listAll(): Collection
    {
        $carts = cart::all();

        return $carts;
    }

    public function create(array $data): cart
    {
        $cart = cart::create($data);

        return $cart;
    }

    public function findById(int $id): cart
    {
        $cart = cart::findOrFail($id);

        return $cart;
    }

    public function update(int $id, array $data): cart
    {
        $cart = cart::findOrFail($id);
        $cart->update($data);

        return $cart;
    }

    public function delete(int $id): void
    {
        $cart = cart::findOrFail($id);
        $cart->delete();
    }
}