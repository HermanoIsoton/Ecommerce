<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Cart_item;
use App\Models\Categorie;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StorefrontCartApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cart_is_persisted_and_totals_are_calculated_from_product_prices(): void
    {
        $product = $this->createProduct(price: 25, stock: 10);
        $session = $this->postJson('/api/cart/session')->assertCreated();
        $token = $session->json('token');
        $headers = ['X-Cart-Token' => $token];

        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $token);
        $this->assertDatabaseHas('carts', [
            'token_hash' => hash('sha256', $token),
            'user_id' => null,
        ]);
        $this->assertDatabaseMissing('carts', ['token_hash' => $token]);

        $this->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 0.01,
        ], $headers)->assertOk()
            ->assertJsonPath('data.total', 50)
            ->assertJsonPath('data.items.0.subtotal', 50);

        $this->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'quantity' => 1,
        ], $headers)->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.total_units', 3)
            ->assertJsonPath('data.items.0.subtotal', 75);

        $cartId = $session->json('data.id');
        $this->getJson('/api/cart', $headers)
            ->assertOk()
            ->assertJsonPath('data.id', $cartId)
            ->assertJsonPath('data.total', 75);

        $itemId = $this->getJson('/api/cart', $headers)->json('data.items.0.id');
        $this->putJson("/api/cart/items/{$itemId}", ['quantity' => 2], $headers)
            ->assertOk()
            ->assertJsonPath('data.total', 50);

        $this->deleteJson("/api/cart/items/{$itemId}", [], $headers)
            ->assertOk()
            ->assertJsonPath('data.total_units', 0);

        $this->deleteJson('/api/cart/items', [], $headers)
            ->assertOk()
            ->assertJsonPath('data.items', []);
    }

    public function test_guest_cart_requires_a_valid_token_and_does_not_expose_other_carts(): void
    {
        $product = $this->createProduct();
        $tokenA = $this->postJson('/api/cart/session')->json('token');
        $tokenB = $this->postJson('/api/cart/session')->json('token');

        $this->getJson('/api/cart')->assertUnauthorized();
        $this->getJson('/api/cart', ['X-Cart-Token' => 'invalid'])->assertUnauthorized();

        $this->postJson('/api/cart/items', [
            'product_id' => $product->id,
        ], ['X-Cart-Token' => $tokenA])->assertOk();

        $itemId = $this->getJson('/api/cart', ['X-Cart-Token' => $tokenA])
            ->json('data.items.0.id');
        $this->deleteJson("/api/cart/items/{$itemId}", [], ['X-Cart-Token' => $tokenB])
            ->assertNotFound();
    }

    public function test_unavailable_products_are_flagged_and_cannot_be_added_or_increased(): void
    {
        $product = $this->createProduct(stock: 2);
        $token = $this->postJson('/api/cart/session')->json('token');
        $headers = ['X-Cart-Token' => $token];

        $this->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ], $headers)->assertOk();

        $this->postJson('/api/cart/items', [
            'product_id' => $product->id,
        ], $headers)->assertUnprocessable();

        $product->update(['is_active' => false]);
        $itemId = $this->getJson('/api/cart', $headers)->json('data.items.0.id');

        $this->getJson('/api/cart', $headers)
            ->assertOk()
            ->assertJsonPath('data.items.0.is_available', false)
            ->assertJsonPath('data.items.0.availability_message', 'Produto indisponível');

        $this->putJson("/api/cart/items/{$itemId}", ['quantity' => 3], $headers)
            ->assertUnprocessable();

        $this->putJson("/api/cart/items/{$itemId}", ['quantity' => 1], $headers)
            ->assertOk()
            ->assertJsonPath('data.items.0.quantity', 1)
            ->assertJsonPath('data.items.0.is_available', false);
    }

    public function test_login_merges_guest_cart_into_the_account_cart_and_caps_available_stock(): void
    {
        config(['sanctum.stateful' => ['localhost']]);

        $sharedProduct = $this->createProduct(price: 25, stock: 5);
        $guestOnlyProduct = $this->createProduct(price: 10, stock: 8);
        $user = User::query()->create([
            'name' => 'Cliente',
            'email' => 'cliente@example.com',
            'password' => Hash::make('senha-segura'),
            'role' => 'user',
        ]);
        $accountCart = Cart::query()->create(['user_id' => $user->id]);
        Cart_item::query()->create([
            'cart_id' => $accountCart->id,
            'product_id' => $sharedProduct->id,
            'quantity' => 4,
        ]);

        $token = $this->postJson('/api/cart/session')->json('token');
        $guestHeaders = ['X-Cart-Token' => $token];
        $this->postJson('/api/cart/items', [
            'product_id' => $sharedProduct->id,
            'quantity' => 4,
        ], $guestHeaders)->assertOk();
        $this->postJson('/api/cart/items', [
            'product_id' => $guestOnlyProduct->id,
            'quantity' => 2,
        ], $guestHeaders)->assertOk();

        $this->withHeader('Origin', 'http://localhost')
            ->postJson('/api/login', [
                'email' => 'cliente@example.com',
                'password' => 'senha-segura',
            ], $guestHeaders)
            ->assertOk()
            ->assertJsonPath('data.id', $user->id);

        $this->assertDatabaseCount('carts', 1);
        $this->assertDatabaseHas('carts', [
            'id' => $accountCart->id,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseMissing('carts', [
            'token_hash' => hash('sha256', $token),
        ]);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $accountCart->id,
            'product_id' => $sharedProduct->id,
            'quantity' => 5,
        ]);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $accountCart->id,
            'product_id' => $guestOnlyProduct->id,
            'quantity' => 2,
        ]);

        $this->withHeader('Origin', 'http://localhost')
            ->getJson('/api/cart')
            ->assertOk()
            ->assertJsonPath('data.total_units', 7)
            ->assertJsonPath('data.total', 145);
    }

    private function createProduct(int $price = 25, int $stock = 10): Product
    {
        $category = Categorie::query()->create([
            'name' => 'Veículos',
            'slug' => 'veiculos-'.fake()->unique()->numerify('###'),
        ]);

        return Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Patinete elétrico',
            'slug' => 'patinete-'.fake()->unique()->numerify('###'),
            'description' => 'Produto de teste.',
            'image_url' => '',
            'price' => $price,
            'stock' => $stock,
            'is_active' => true,
            'type' => 'autopropelido',
            'power' => 350,
            'range_km' => 25,
            'top_speed_kmh' => 25,
            'weight_kg' => 14.5,
            'charge_time_h' => 4,
            'battery_capaciy' => 280,
            'battery_voltage_v' => 36,
        ]);
    }
}
