<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RecipeTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $otherUser;
    private $recipe;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->recipe = Recipe::factory()->create([
            'user_id' => $this->user->id,
            'public' => true,
        ]);
    }

    #[Test]
    public function lists_public_recipes_and_user_own_recipes()
    {
        Recipe::factory()->create(['public' => true]);
        Recipe::factory()->create(['user_id' => $this->user->id, 'public' => false]);

        $response = $this->actingAs($this->user)->getJson('/api/recipes');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function lists_only_user_recipes_in_my_recipes()
    {
        Recipe::factory()->create(['user_id' => $this->otherUser->id]);

        $response = $this->actingAs($this->user)->getJson('/api/recipes/mine');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    #[Test]
    public function allows_user_to_create_a_recipe()
    {
        $ingredient = \App\Models\Ingredient::factory()->create();
        
        $recipeData = [
            'title' => 'New Recipe',
            'description' => 'Delicious recipe',
            'public' => true,
            'ingredients' => [
                ['ingredient_id' => $ingredient->id, 'quantity' => 200, 'unit' => 'g'],
            ],
            'steps' => [
                ['description' => 'Step 1', 'order' => 1],
            ],
        ];

        $response = $this->actingAs($this->user)->postJson('/api/recipes', $recipeData);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'New Recipe']);
    }

    #[Test]
    public function validates_required_fields_on_recipe_creation()
    {
        $response = $this->actingAs($this->user)->postJson('/api/recipes', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'description', 'ingredients', 'steps']);
    }

    #[Test]
    public function allows_users_to_view_their_own_or_public_recipes()
    {
        $publicRecipe = Recipe::factory()->create(['public' => true]);
        $privateRecipe = Recipe::factory()->create(['user_id' => $this->user->id, 'public' => false]);

        $this->actingAs($this->user)
            ->getJson("/api/recipes/{$publicRecipe->id}")
            ->assertStatus(200);

        $this->actingAs($this->user)
            ->getJson("/api/recipes/{$privateRecipe->id}")
            ->assertStatus(200);
    }

    #[Test]
    public function prevents_users_from_viewing_private_recipes_of_others()
    {
        $privateRecipe = Recipe::factory()->create(['user_id' => $this->otherUser->id, 'public' => false]);

        $this->actingAs($this->user)
            ->getJson("/api/recipes/{$privateRecipe->id}")
            ->assertStatus(404);
    }

    #[Test]
    public function allows_user_to_update_their_own_recipe()
    {
        $updateData = ['title' => 'Updated Recipe'];

        $this->actingAs($this->user)
            ->putJson("/api/recipes/{$this->recipe->id}", $updateData)
            ->assertStatus(200)
            ->assertJsonFragment(['title' => 'Updated Recipe']);
    }

    #[Test]
    public function prevents_user_from_updating_someone_elses_recipe()
    {
        $recipe = Recipe::factory()->create(['user_id' => $this->otherUser->id]);

        $this->actingAs($this->user)
            ->putJson("/api/recipes/{$recipe->id}", ['title' => 'Hacked'])
            ->assertStatus(403);
    }

    #[Test]
    public function allows_user_to_delete_their_own_recipe()
    {
        $recipe = Recipe::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->deleteJson("/api/recipes/{$recipe->id}")
            ->assertStatus(200);
    }

    #[Test]
    public function prevents_user_from_deleting_someone_elses_recipe()
    {
        $recipe = Recipe::factory()->create(['user_id' => $this->otherUser->id]);

        $this->actingAs($this->user)
            ->deleteJson("/api/recipes/{$recipe->id}")
            ->assertStatus(403);
    }
}
