<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_fetch_all_categories()
    {
        Category::factory()->count(5)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)->assertJsonCount(5);
    }

    #[Test]
    public function can_create_category()
    {
        $data = ['name' => 'Desserts'];

        $response = $this->postJson('/api/categories', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Desserts']);

        $this->assertDatabaseHas('categories', ['name' => 'Desserts']);
    }

    #[Test]
    public function can_fetch_single_category()
    {
        $category = Category::factory()->create();

        $response = $this->getJson('/api/categories/' . $category->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => $category->name]);
    }

    #[Test]
    public function can_update_category()
    {
        $category = Category::factory()->create(['name' => 'Old Name']);

        $response = $this->putJson('/api/categories/' . $category->id, ['name' => 'New Name']);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'New Name']);

        $this->assertDatabaseHas('categories', ['name' => 'New Name']);
    }

    #[Test]
    public function can_delete_category()
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson('/api/categories/' . $category->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
