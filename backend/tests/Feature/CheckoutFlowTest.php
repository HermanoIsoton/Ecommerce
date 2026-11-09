<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Cart_item;
use App\Models\Categorie;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_reserves_stock_clears_the_cart_and_simulates_payment(): void
    {
        $user = $this->createUser();
        $product = $this->createProduct(stock: 5);
        $address = $this->createAddress($user);
        $this->addToCart($user, $product, quantity: 2);
        $this->actingAs($user);

        $checkout = $this->postJson('/api/orders/checkout', [
            'address_id' => $address->id,
        ])->assertCreated()
            ->assertJsonPath('data.status', 'pendente')
            ->assertJsonPath('data.subtotal_cents', 50)
            ->assertJsonPath('data.shipping_cents', 29.9)
            ->assertJsonPath('data.total_cents', 79.9)
            ->assertJsonPath('data.ship_cep', $address->cep)
            ->assertJsonPath('data.items.0.product_name', $product->name)
            ->assertJsonPath('data.items.0.quantity', 2);

        $orderId = $checkout->json('data.id');
        $this->assertNotNull($checkout->json('data.payment_expires_at'));
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);
        $this->assertDatabaseHas('orders', ['id' => $orderId, 'user_id' => $user->id]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $orderId,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 25,
        ]);
        $this->assertDatabaseMissing('cart_items', ['cart_id' => Cart::query()->where('user_id', $user->id)->value('id')]);

        $this->postJson("/api/orders/{$orderId}/simulate-payment")
            ->assertOk()
            ->assertJsonPath('data.status', 'pago');

        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'status' => 'pago',
        ]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);
    }

    public function test_expired_unpaid_orders_are_cancelled_and_reserved_stock_is_restored_once(): void
    {
        $user = $this->createUser();
        $product = $this->createProduct(stock: 5);
        $address = $this->createAddress($user);
        $this->addToCart($user, $product, quantity: 3);
        $this->actingAs($user);

        $orderId = $this->postJson('/api/orders/checkout', [
            'address_id' => $address->id,
        ])->assertCreated()->json('data.id');

        $this->travel(31)->minutes();
        $this->artisan('orders:expire-unpaid')->assertSuccessful();

        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'status' => 'cancelado',
        ]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 5]);

        $this->artisan('orders:expire-unpaid')->assertSuccessful();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 5]);
    }

    public function test_checkout_requires_login_owns_address_and_available_cart_items(): void
    {
        $user = $this->createUser();
        $otherUser = $this->createUser('outra@example.com');
        $product = $this->createProduct(stock: 1);
        $address = $this->createAddress($user);
        $otherAddress = $this->createAddress($otherUser);
        $this->addToCart($user, $product, quantity: 2);

        $this->postJson('/api/orders/checkout', ['address_id' => $address->id])
            ->assertUnauthorized();

        $this->actingAs($user);
        $this->postJson('/api/orders/checkout', ['address_id' => $otherAddress->id])
            ->assertNotFound();

        $this->postJson('/api/orders/checkout', ['address_id' => $address->id])
            ->assertUnprocessable();

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 1]);
    }

    public function test_expired_order_cannot_be_simulated_as_paid(): void
    {
        $user = $this->createUser();
        $product = $this->createProduct(stock: 2);
        $address = $this->createAddress($user);
        $this->addToCart($user, $product, quantity: 1);
        $this->actingAs($user);

        $orderId = $this->postJson('/api/orders/checkout', [
            'address_id' => $address->id,
        ])->assertCreated()->json('data.id');

        $this->travel(31)->minutes();
        $this->postJson("/api/orders/{$orderId}/simulate-payment")
            ->assertStatus(409)
            ->assertJsonPath('data.status', 'cancelado');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 2]);
    }

    private function createUser(string $email = 'cliente@example.com'): User
    {
        return User::query()->create([
            'name' => 'Cliente',
            'email' => $email,
            'password' => 'senha-segura',
            'role' => 'user',
        ]);
    }

    private function createAddress(User $user): Address
    {
        return Address::query()->create([
            'user_id' => $user->id,
            'recipient' => 'Cliente',
            'cep' => '01000-000',
            'street' => 'Rua da Loja',
            'number' => '10',
            'complement' => '',
            'district' => 'Centro',
            'city' => 'São Paulo',
            'state' => 'SP',
            'is_primary' => true,
        ]);
    }

    private function createProduct(int $stock = 10): Product
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
            'price' => 25,
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

    private function addToCart(User $user, Product $product, int $quantity): void
    {
        $cart = Cart::query()->create(['user_id' => $user->id]);
        Cart_item::query()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
        ]);
    }
}
