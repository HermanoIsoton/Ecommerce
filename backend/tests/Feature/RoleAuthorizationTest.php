<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Categorie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_routes_require_a_sanctum_authenticated_session(): void
    {
        $this->getJson('/api/me')->assertUnauthorized();
        $this->getJson('/api/products')->assertUnauthorized();

        $user = $this->createUser();
        $this->actingAs($user)
            ->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id);
    }

    public function test_login_and_logout_use_the_session_guard(): void
    {
        $user = $this->createUser();
        $user->update([
            'password_hash' => Hash::make('correct-password'),
        ]);

        $headers = ['Origin' => 'http://localhost'];
        $loginResponse = $this->withHeaders($headers)
            ->postJson('/api/login', [
                'email' => $user->email,
                'password' => 'correct-password',
            ])
            ->assertOk()
            ->assertJsonPath('data.id', $user->id);

        $sessionCookieName = config('session.cookie');
        $sessionCookie = collect($loginResponse->headers->getCookies())
            ->first(fn ($cookie) => $cookie->getName() === $sessionCookieName);

        $this->assertNotNull($sessionCookie);

        Auth::forgetGuards();
        $this->withHeaders($headers)
            ->withCookie($sessionCookieName, $sessionCookie->getValue())
            ->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id);

        $logoutResponse = $this->withHeaders($headers)
            ->withCookie($sessionCookieName, $sessionCookie->getValue())
            ->postJson('/api/logout')
            ->assertNoContent();

        $logoutCookie = collect($logoutResponse->headers->getCookies())
            ->first(fn ($cookie) => $cookie->getName() === $sessionCookieName);

        $this->assertNotNull($logoutCookie);

        Auth::forgetGuards();
        $this->withHeaders($headers)
            ->withCookie($sessionCookieName, $logoutCookie->getValue())
            ->getJson('/api/me')
            ->assertUnauthorized();
    }

    public function test_public_registration_cannot_assign_the_admin_role(): void
    {
        $email = fake()->unique()->safeEmail();

        $this->withHeader('Origin', 'http://localhost')
            ->postJson('/api/register', [
                'name' => fake()->name(),
                'email' => $email,
                'password' => 'correct-password',
                'password_confirmation' => 'correct-password',
                'role' => 'admin',
            ])
            ->assertCreated()
            ->assertJsonPath('data.role', 'user');

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'role' => 'user',
        ]);
    }

    public function test_regular_users_can_read_products_and_categories_but_not_write_them(): void
    {
        $user = $this->createUser();
        $category = Categorie::query()->create([
            'name' => 'Scooters',
            'slug' => 'scooters',
        ]);

        $this->actingAs($user)
            ->getJson('/api/products')
            ->assertOk();

        $this->getJson('/api/categories')
            ->assertOk()
            ->assertJsonPath('data.0.id', $category->id);

        $this->postJson('/api/categories', [
            'name' => 'Bikes',
            'slug' => 'bikes',
        ])->assertForbidden();

        $this->patchJson("/api/categories/{$category->id}", [
            'name' => 'Updated',
        ])->assertForbidden();

        $this->postJson('/api/products', [])->assertForbidden();
        $this->getJson('/api/users')->assertForbidden();
    }

    public function test_regular_users_can_only_read_their_own_addresses(): void
    {
        $owner = $this->createUser();
        $otherUser = $this->createUser();

        $ownAddress = $this->createAddress($owner);
        $otherAddress = $this->createAddress($otherUser);

        $this->actingAs($owner)
            ->getJson('/api/addresses')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $ownAddress->id);

        $this->getJson("/api/addresses/{$ownAddress->id}")->assertOk();
        $this->getJson("/api/addresses/{$otherAddress->id}")->assertNotFound();
        $this->postJson('/api/addresses', [])->assertForbidden();
    }

    public function test_admins_can_read_users_and_all_addresses(): void
    {
        $admin = $this->createUser('admin');
        $owner = $this->createUser();
        $address = $this->createAddress($owner);

        $this->actingAs($admin)
            ->getJson('/api/users')
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->getJson('/api/addresses')
            ->assertOk()
            ->assertJsonPath('data.0.id', $address->id);
    }

    private function createUser(string $role = 'user'): User
    {
        return User::query()->create([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password_hash' => Hash::make('unused-password'),
            'role' => $role,
        ]);
    }

    private function createAddress(User $user): Address
    {
        return Address::query()->create([
            'user_id' => $user->id,
            'recipient' => fake()->name(),
            'cep' => '00000-000',
            'street' => 'Main Street',
            'number' => '1',
            'district' => 'Centro',
            'city' => 'São Paulo',
            'state' => 'SP',
        ]);
    }
}
