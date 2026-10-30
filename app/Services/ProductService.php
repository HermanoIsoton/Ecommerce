<?php

namespace App\Services;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function listAll(): Collection
    {
        $products = product::all();

        return $products;
    }

    public function create(array $data): product
    {
        $product = product::create($data);

        return $product;
    }

    public function findById(int $id): product
    {
        $product = product::findOrFail($id);

        return $product;
    }

    public function update(int $id, array $data): product
    {
        $product = product::findOrFail($id);
        $product->update($data);

        return $product;
    }

    public function delete(int $id): void
    {
        $product = product::findOrFail($id);
        $product->delete();
    }
}