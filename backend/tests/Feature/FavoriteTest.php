<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);

        $this->recipe = Recipe::factory()->create(['user_id' => $this->user->id]);
    }

    #[Test]
    public function user_can_add_recipe_to_favorites()
    {
        $response = $this->actingAs($this->user)->postJson("/api/recipes/favorites/{$this->recipe->id}");

        $response->assertStatus(201)
                 ->assertJson(['message' => 'Recipe added to favorites']);

        $this->user->refresh();

        $this->assertTrue($this->user->favorites()->where('recipe_id', $this->recipe->id)->exists());
    }

    #[Test]
    public function user_can_remove_recipe_from_favorites()
    {
        $this->user->favorites()->attach($this->recipe->id);

        $response = $this->actingAs($this->user)->deleteJson("/api/recipes/favorites/{$this->recipe->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Recipe removed from favorites']);

        $this->user->refresh();
        $this->assertFalse($this->user->favorites()->where('recipe_id', $this->recipe->id)->exists());
    }

    #[Test]
    public function user_can_view_favorite_recipes()
    {
        $this->user->favorites()->attach($this->recipe->id);
        
        $response = $this->actingAs($this->user)->getJson("/api/recipes/favorites");

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => [['id', 'title', 'description']]]);
    }

    #[Test]
    public function unauthenticated_users_cannot_manage_favorites()
    {
        $response = $this->postJson("/api/recipes/favorites/{$this->recipe->id}");
        $response->assertStatus(401);

        $response = $this->deleteJson("/api/recipes/favorites/{$this->recipe->id}");
        $response->assertStatus(401);

        $response = $this->getJson("/api/recipes/favorites");
        $response->assertStatus(401);
    }
}
