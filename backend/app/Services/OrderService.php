<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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

    public function transitionStatus(int $id, string $status): Order
    {
        return DB::transaction(function () use ($id, $status): Order {
            $order = Order::query()->lockForUpdate()->findOrFail($id);

            $timestamp = match ([$order->status, $status]) {
                ['pago', 'enviado'] => 'shipped_at',
                ['enviado', 'entregue'] => 'delivered_at',
                default => null,
            };

            if ($timestamp === null) {
                throw ValidationException::withMessages([
                    'status' => ['Transição de status inválida para este pedido.'],
                ]);
            }

            $order->update([
                'status' => $status,
                $timestamp => now(),
            ]);

            return $order->fresh()->load('orderItems');
        });
    }
}