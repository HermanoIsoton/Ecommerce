<?php

namespace Tests\Feature;

use App\Models\Categorie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriesApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_manages_categories_through_the_api(): void
    {
        $category = Categorie::create([
            'name' => 'Patinetes elétricos',
            'slug' => 'patinetes-eletricos',
        ]);

        $this->getJson('/api/categories')
            ->assertOk()
            ->assertJsonPath('data.0.id', $category->id)
            ->assertJsonPath('data.0.name', 'Patinetes elétricos')
            ->assertJsonPath('data.0.slug', 'patinetes-eletricos');

        $this->getJson("/api/categories/{$category->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $category->id);

        $this->patchJson("/api/categories/{$category->id}", [
            'name' => 'Bicicletas elétricas',
            'slug' => 'bicicletas-eletricas',
        ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Bicicletas elétricas')
            ->assertJsonPath('data.slug', 'bicicletas-eletricas');

        $this->deleteJson("/api/categories/{$category->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_it_validates_category_creation_and_unique_slugs(): void
    {
        $this->postJson('/api/categories', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'slug']);

        Categorie::create([
            'name' => 'Patinetes elétricos',
            'slug' => 'patinetes-eletricos',
        ]);

        $this->postJson('/api/categories', [
            'name' => 'Outro patinete',
            'slug' => 'patinetes-eletricos',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['slug']);
    }
}
