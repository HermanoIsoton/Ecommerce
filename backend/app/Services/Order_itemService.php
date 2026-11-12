<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Order_item;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class Order_itemService
{
    public function listAll(User $user): Collection
    {
        $query = Order_item::query();

        if ($user->role !== 'admin') {
            $query->whereIn('order_id', Order::query()
                ->where('user_id', $user->id)
                ->select('id'));
        }

        return $query->get();
    }

    public function create(array $data): order_item
    {
        $order_item = order_item::create($data);

        return $order_item;
    }

    public function findById(User $user, int $id): Order_item
    {
        $query = Order_item::query();

        if ($user->role !== 'admin') {
            $query->whereIn('order_id', Order::query()
                ->where('user_id', $user->id)
                ->select('id'));
        }

        return $query->findOrFail($id);
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