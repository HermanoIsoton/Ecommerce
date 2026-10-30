<?php

namespace App\Services;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class OrderService
{
    public function listAll(): Collection
    {
        $orders = order::all();

        return $orders;
    }

    public function create(array $data): order
    {
        $order = order::create($data);

        return $order;
    }

    public function findById(int $id): order
    {
        $order = order::findOrFail($id);

        return $order;
    }

    public function update(int $id, array $data): order
    {
        $order = order::findOrFail($id);
        $order->update($data);

        return $order;
    }

    public function delete(int $id): void
    {
        $order = order::findOrFail($id);
        $order->delete();
    }
}