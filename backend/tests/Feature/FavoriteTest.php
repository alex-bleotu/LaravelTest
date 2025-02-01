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

        $this->recipe = Recipe::factory()->create(['user_id' => $this->user->id]);
    }

    #[Test]
    public function user_can_add_recipe_to_favorites()
    {
        Sanctum::actingAs($this->user);

        $response = $this->actingAs($this->user)->postJson("/api/recipes/favorites/{$this->recipe->id}");

        $response->assertStatus(201)
                 ->assertJson(['message' => 'Recipe added to favorites']);

        $this->user->refresh();

        $this->assertTrue($this->user->favorites()->where('recipe_id', $this->recipe->id)->exists());
    }

    #[Test]
    public function user_can_remove_recipe_from_favorites()
    {
        Sanctum::actingAs($this->user);

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
        Sanctum::actingAs($this->user);
        
        $this->user->favorites()->attach($this->recipe->id);
        
        $response = $this->actingAs($this->user)->getJson("/api/recipes/favorites/list");

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

        $response = $this->getJson("/api/recipes/favorites/list");
        $response->assertStatus(401);
    }
}
