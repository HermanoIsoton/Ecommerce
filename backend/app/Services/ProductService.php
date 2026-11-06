<?php

namespace App\Services;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function listAll(): Collection
    {
        return Product::with('category')->get();
    }

    public function create(array $data): product
    {
        $data['slug'] = Str::slug($data['name']);
        $product = product::create($data);

        return $product;
    }

    public function findById(int $id): product
    {
        $product = product::findOrFail($id);

        return product::with('category')->get();
    }

    public function update(int $id, array $data): product
    {
        if (isset($data['name'])){
        $data['slug'] = Str::slug($data['name']);
        }

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