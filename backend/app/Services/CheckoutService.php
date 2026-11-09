<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Order_item;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    private const SHIPPING_CENTS = 2990;

    private const PAYMENT_WINDOW_MINUTES = 30;

    public function createOrder(User $user, int $addressId): Order
    {
        return DB::transaction(function () use ($user, $addressId): Order {
            $address = Address::query()
                ->where('user_id', $user->id)
                ->findOrFail($addressId);
            $cart = Cart::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (! $cart) {
                throw ValidationException::withMessages([
                    'cart' => ['Seu carrinho está vazio.'],
                ]);
            }

            $cartItems = $cart->items()->orderBy('id')->get();
            if ($cartItems->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => ['Seu carrinho está vazio.'],
                ]);
            }

            $products = Product::query()
                ->whereIn('id', $cartItems->pluck('product_id'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotalCents = 0;
            $lines = [];

            foreach ($cartItems as $cartItem) {
                $product = $products->get($cartItem->product_id);
                $quantity = (int) $cartItem->quantity;

                if (! $product || ! $product->is_active || $quantity < 1) {
                    throw ValidationException::withMessages([
                        'cart' => ['Um ou mais produtos do carrinho estão indisponíveis. Atualize o carrinho e tente novamente.'],
                    ]);
                }

                if ((int) $product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'cart' => ["Estoque insuficiente para {$product->name}. Atualize a quantidade no carrinho."],
                    ]);
                }

                $unitPriceCents = (int) round((float) $product->price * 100);
                $lineSubtotalCents = $unitPriceCents * $quantity;
                $subtotalCents += $lineSubtotalCents;
                $lines[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price_cents' => $unitPriceCents,
                    'subtotal_cents' => $lineSubtotalCents,
                ];
            }

            $order = Order::query()->create([
                'user_id' => $user->id,
                'status' => 'pendente',
                'subtotal_cents' => $subtotalCents / 100,
                'shipping_cents' => self::SHIPPING_CENTS / 100,
                'total_cents' => ($subtotalCents + self::SHIPPING_CENTS) / 100,
                'ship_recipient' => $address->recipient,
                'ship_cep' => $address->cep,
                'ship_street' => $address->street,
                'ship_number' => $address->number,
                'ship_complement' => $address->complement ?? '',
                'ship_district' => $address->district,
                'ship_city' => $address->city,
                'ship_state' => $address->state,
                'payment_expires_at' => now()->addMinutes(self::PAYMENT_WINDOW_MINUTES),
            ]);

            foreach ($lines as $line) {
                /** @var Product $product */
                $product = $line['product'];
                $quantity = $line['quantity'];

                $product->decrement('stock', $quantity);
                Order_item::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $line['unit_price_cents'] / 100,
                    'quantity' => $quantity,
                    'subtotal_cents' => $line['subtotal_cents'] / 100,
                ]);
            }

            $cart->items()->delete();

            return $order->load('orderItems');
        });
    }

    public function simulatePayment(User $user, int $orderId): array
    {
        [$order, $expired] = DB::transaction(function () use ($user, $orderId): array {
            $order = Order::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->findOrFail($orderId);

            if ($order->status !== 'pendente') {
                throw ValidationException::withMessages([
                    'order' => ['Este pedido não está aguardando pagamento.'],
                ]);
            }

            if ($order->payment_expires_at && $order->payment_expires_at->isPast()) {
                $this->cancelAndRestoreStock($order);

                return [$order->fresh()->load('orderItems'), true];
            }

            $order->update([
                'status' => 'pago',
                'paid_at' => now(),
            ]);

            return [$order->fresh()->load('orderItems'), false];
        });

        return [$order, $expired];
    }

    public function expirePendingOrders(): int
    {
        $expiredCount = 0;
        $orderIds = Order::query()
            ->where('status', 'pendente')
            ->whereNotNull('payment_expires_at')
            ->where('payment_expires_at', '<=', now())
            ->pluck('id');

        foreach ($orderIds as $orderId) {
            $expired = DB::transaction(function () use ($orderId): bool {
                $order = Order::query()->lockForUpdate()->find($orderId);

                if (
                    ! $order
                    || $order->status !== 'pendente'
                    || ! $order->payment_expires_at
                    || $order->payment_expires_at->isFuture()
                ) {
                    return false;
                }

                $this->cancelAndRestoreStock($order);

                return true;
            });

            if ($expired) {
                $expiredCount++;
            }
        }

        return $expiredCount;
    }

    public function expireIfDue(Order $order): Order
    {
        if ($order->status !== 'pendente' || ! $order->payment_expires_at?->isPast()) {
            return $order->loadMissing('orderItems');
        }

        return DB::transaction(function () use ($order): Order {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if (
                $lockedOrder->status === 'pendente'
                && $lockedOrder->payment_expires_at?->isPast()
            ) {
                $this->cancelAndRestoreStock($lockedOrder);
            }

            return $lockedOrder->fresh()->load('orderItems');
        });
    }

    private function cancelAndRestoreStock(Order $order): void
    {
        foreach ($order->orderItems()->whereNotNull('product_id')->get() as $item) {
            $product = Product::query()->lockForUpdate()->find($item->product_id);
            $product?->increment('stock', (int) $item->quantity);
        }

        $order->update([
            'status' => 'cancelado',
            'cancelled_at' => now(),
        ]);
    }
}
