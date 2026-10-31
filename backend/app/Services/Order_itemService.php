<?php

namespace App\Services;
use App\Models\Order_item;
use Illuminate\Database\Eloquent\Collection;

class Order_itemService
{
    public function listAll(): Collection
    {
        $order_items = order_item::all();

        return $order_items;
    }

    public function create(array $data): order_item
    {
        $order_item = order_item::create($data);

        return $order_item;
    }

    public function findById(int $id): order_item
    {
        $order_item = order_item::findOrFail($id);

        return $order_item;
    }

    public function update(int $id, array $data): order_item
    {
        $order_item = order_item::findOrFail($id);
        $order_item->update($data);

        return $order_item;
    }

    public function delete(int $id): void
    {
        $order_item = order_item::findOrFail($id);
        $order_item->delete();
    }
}