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
            'cart' => $this->loadCart($cart),
        ];
    }

    public function getCart(?User $user, ?string $token): Cart
    {
        return $this->loadCart($this->resolveCart($user, $token));
    }

    public function addItem(?User $user, ?string $token, int $productId, int $quantity): Cart
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

            return $this->loadCart($cart->fresh());
        });
    }

    public function updateItem(?User $user, ?string $token, int $itemId, int $quantity): Cart
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

            return $this->loadCart($cart->fresh());
        });
    }

    public function removeItem(?User $user, ?string $token, int $itemId): Cart
    {
        $cart = $this->resolveCart($user, $token);
        $cart->items()->findOrFail($itemId)->delete();

        return $this->loadCart($cart->fresh());
    }

    public function clear(?User $user, ?string $token): Cart
    {
        $cart = $this->resolveCart($user, $token);
        $cart->items()->delete();

        return $this->loadCart($cart->fresh());
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

    private function loadCart(Cart $cart): Cart
    {
        return $cart->load('items.product');
    }
}
