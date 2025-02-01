<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\RecipeStep;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;

class RecipeStepTest extends TestCase
{
    use RefreshDatabase;

    protected Recipe $recipe;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->recipe = Recipe::factory()->create([
            'user_id' => $this->user->id
        ]);
    }

    #[Test]
    public function can_fetch_steps_for_recipe()
    {
        RecipeStep::factory()->count(3)->create(['recipe_id' => $this->recipe->id]);

        $response = $this->getJson("/api/recipes/{$this->recipe->id}/steps");

        $response->assertStatus(200)->assertJsonCount(3);
    }

    #[Test]
    public function can_add_step_to_recipe()
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('step.jpg');

        $response = $this->actingAs($this->user)->postJson("/api/recipes/{$this->recipe->id}/steps", [
            'step_number' => 1,
            'description' => 'First step of the recipe',
            'image' => $image,
        ]);

        $response->assertStatus(201)->assertJsonFragment([
            'step_number' => 1,
            'description' => 'First step of the recipe',
        ]);

        Storage::disk('public')->assertExists('steps/' . $image->hashName());
    }

    #[Test]
    public function cannot_add_step_without_authentication()
    {
        $response = $this->postJson("/api/recipes/{$this->recipe->id}/steps", [
            'step_number' => 1,
            'description' => 'Step without auth',
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function can_update_recipe_step()
    {
        $step = RecipeStep::factory()->create(['recipe_id' => $this->recipe->id]);

        $response = $this->actingAs($this->user)->putJson("/api/recipes/{$this->recipe->id}/steps/{$step->id}", [
            'description' => 'Updated step description',
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            'description' => 'Updated step description',
        ]);
    }

    #[Test]
    public function cannot_update_recipe_step_without_authentication()
    {
        $step = RecipeStep::factory()->create(['recipe_id' => $this->recipe->id]);

        $response = $this->putJson("/api/recipes/{$this->recipe->id}/steps/{$step->id}", [
            'description' => 'Updated without auth',
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function can_delete_recipe_step()
    {
        $step = RecipeStep::factory()->create(['recipe_id' => $this->recipe->id]);

        $response = $this->actingAs($this->user)->deleteJson("/api/recipes/{$this->recipe->id}/steps/{$step->id}");

        $response->assertStatus(200)->assertJsonFragment(['message' => 'Recipe step deleted']);

        $this->assertDatabaseMissing('recipe_steps', [
            'id' => $step->id,
        ]);
    }

    #[Test]
    public function cannot_delete_recipe_step_without_authentication()
    {
        $step = RecipeStep::factory()->create(['recipe_id' => $this->recipe->id]);

        $response = $this->deleteJson("/api/recipes/{$this->recipe->id}/steps/{$step->id}");

        $response->assertStatus(401);
    }
}
