<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Cart_item;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function createGuestCart(): array
    {
        $token = bin2hex(random_bytes(32));
        $cart = Cart::query()->create([
            'token_hash' => hash('sha256', $token),
        ]);

        return [
            'token' => $token,
            'cart' => $this->serialize($cart),
        ];
    }

    public function getCart(?User $user, ?string $token): array
    {
        return $this->serialize($this->resolveCart($user, $token));
    }

    public function addItem(?User $user, ?string $token, int $productId, int $quantity): array
    {
        return DB::transaction(function () use ($user, $token, $productId, $quantity): array {
            $cart = $this->lockCart($this->resolveCart($user, $token));
            $product = Product::query()->lockForUpdate()->findOrFail($productId);

            if (! $product->is_active || (int) $product->stock < 1) {
                throw ValidationException::withMessages([
                    'product_id' => ['Este produto está indisponível.'],
                ]);
            }

            $item = $cart->items()->where('product_id', $product->id)->lockForUpdate()->first();
            $newQuantity = (int) ($item?->quantity ?? 0) + $quantity;

            if ($newQuantity > (int) $product->stock) {
                throw ValidationException::withMessages([
                    'quantity' => ['A quantidade solicitada excede o estoque disponível.'],
                ]);
            }

            if ($item) {
                $item->update(['quantity' => $newQuantity]);
            } else {
                $cart->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ]);
            }

            return $this->serialize($cart->fresh());
        });
    }

    public function updateItem(?User $user, ?string $token, int $itemId, int $quantity): array
    {
        return DB::transaction(function () use ($user, $token, $itemId, $quantity): array {
            $cart = $this->lockCart($this->resolveCart($user, $token));
            $item = $cart->items()->with('product')->lockForUpdate()->findOrFail($itemId);
            $product = $item->product;

            if (
                $quantity > (int) $item->quantity
                && (! $product->is_active || $quantity > (int) $product->stock)
            ) {
                throw ValidationException::withMessages([
                    'quantity' => ['A quantidade solicitada não está disponível.'],
                ]);
            }

            $item->update(['quantity' => $quantity]);

            return $this->serialize($cart->fresh());
        });
    }

    public function removeItem(?User $user, ?string $token, int $itemId): array
    {
        $cart = $this->resolveCart($user, $token);
        $cart->items()->findOrFail($itemId)->delete();

        return $this->serialize($cart->fresh());
    }

    public function clear(?User $user, ?string $token): array
    {
        $cart = $this->resolveCart($user, $token);
        $cart->items()->delete();

        return $this->serialize($cart->fresh());
    }

    public function mergeGuestCartIntoUser(?string $token, User $user): void
    {
        if (! is_string($token) || ! preg_match('/^[a-f0-9]{64}$/', $token)) {
            return;
        }

        DB::transaction(function () use ($token, $user): void {
            $guestCart = Cart::query()
                ->whereNull('user_id')
                ->where('token_hash', hash('sha256', $token))
                ->lockForUpdate()
                ->first();

            if (! $guestCart) {
                return;
            }

            $userCart = Cart::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (! $userCart) {
                $userCart = Cart::query()->create(['user_id' => $user->id]);
            }

            if ($guestCart->is($userCart)) {
                return;
            }

            foreach ($guestCart->items()->with('product')->get() as $guestItem) {
                $userItem = $userCart->items()
                    ->where('product_id', $guestItem->product_id)
                    ->lockForUpdate()
                    ->first();

                if ($userItem) {
                    $quantity = (int) $userItem->quantity + (int) $guestItem->quantity;
                    $product = $guestItem->product;

                    if ($product->is_active && (int) $product->stock > 0) {
                        $quantity = min($quantity, (int) $product->stock);
                    }

                    $userItem->update(['quantity' => $quantity]);

                    continue;
                }

                $userCart->items()->create([
                    'product_id' => $guestItem->product_id,
                    'quantity' => $guestItem->quantity,
                ]);
            }

            $guestCart->delete();
        });
    }

    private function resolveCart(?User $user, ?string $token): Cart
    {
        if ($user) {
            return Cart::query()->firstOrCreate(['user_id' => $user->id]);
        }

        if (! is_string($token) || ! preg_match('/^[a-f0-9]{64}$/', $token)) {
            abort(401, 'Sessão do carrinho inválida ou ausente.');
        }

        return Cart::query()
            ->whereNull('user_id')
            ->where('token_hash', hash('sha256', $token))
            ->firstOrFail();
    }

    private function lockCart(Cart $cart): Cart
    {
        return Cart::query()->whereKey($cart->id)->lockForUpdate()->firstOrFail();
    }

    private function serialize(Cart $cart): array
    {
        $cart->load('items.product');
        $totalCents = 0;
        $items = $cart->items->map(function (Cart_item $item) use (&$totalCents): array {
            $product = $item->product;
            $quantity = (int) $item->quantity;
            $unitPriceCents = (int) round((float) $product->price * 100);
            $subtotalCents = $unitPriceCents * $quantity;
            $totalCents += $subtotalCents;
            $isAvailable = (bool) $product->is_active && (int) $product->stock >= $quantity;

            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'quantity' => $quantity,
                'unit_price' => $unitPriceCents / 100,
                'subtotal' => $subtotalCents / 100,
                'is_available' => $isAvailable,
                'availability_message' => ! $product->is_active
                    ? 'Produto indisponível'
                    : ((int) $product->stock < $quantity ? 'Estoque insuficiente' : null),
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image_url' => $product->image_url,
                    'price' => $unitPriceCents / 100,
                    'stock' => (int) $product->stock,
                    'is_active' => (bool) $product->is_active,
                    'type' => $product->type,
                    'range_km' => $product->range_km,
                    'top_speed_kmh' => $product->top_speed_kmh,
                ],
            ];
        });

        return [
            'id' => $cart->id,
            'items' => $items->values(),
            'total_units' => $items->sum('quantity'),
            'subtotal' => $totalCents / 100,
            'total' => $totalCents / 100,
        ];
    }
}
