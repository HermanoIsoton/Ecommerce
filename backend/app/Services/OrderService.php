<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function listAll(User $user, ?string $status = null): Collection
    {
        $query = Order::query()->with('orderItems')->latest();

        if ($user->role === 'admin') {
            $query->with('user:id,name,email');
        } else {
            $query->where('user_id', $user->id);
        }

        if (! empty($status)) {
            $query->where('status', $status);
        }

        return $query->get();
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