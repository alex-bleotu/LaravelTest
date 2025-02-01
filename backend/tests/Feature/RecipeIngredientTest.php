<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RecipeIngredientTest extends TestCase
{
    use RefreshDatabase;

    protected Recipe $recipe;
    protected Ingredient $ingredient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->recipe = Recipe::factory()->create();
        $this->ingredient = Ingredient::factory()->create();
    }

    #[Test]
    public function can_fetch_ingredients_for_recipe()
    {
        RecipeIngredient::create([
            'recipe_id' => $this->recipe->id,
            'ingredient_id' => $this->ingredient->id,
            'quantity' => 100,
            'unit' => 'grams',
        ]);

        $response = $this->getJson("/api/recipes/{$this->recipe->id}/ingredients");

        $response->assertStatus(200)->assertJsonFragment([
            'ingredient_id' => $this->ingredient->id,
            'quantity' => 100,
            'unit' => 'grams',
        ]);
    }

    #[Test]
    public function can_add_ingredient_to_recipe()
    {
        $response = $this->postJson("/api/recipes/{$this->recipe->id}/ingredients", [
            'ingredient_id' => $this->ingredient->id,
            'quantity' => 200,
            'unit' => 'ml',
        ]);

        $response->assertStatus(201)->assertJsonFragment([
            'ingredient_id' => $this->ingredient->id,
            'quantity' => 200,
            'unit' => 'ml',
        ]);

        $this->assertDatabaseHas('recipe_ingredients', [
            'recipe_id' => $this->recipe->id,
            'ingredient_id' => $this->ingredient->id,
            'quantity' => 200,
            'unit' => 'ml',
        ]);
    }

    #[Test]
    public function can_update_recipe_ingredient()
    {
        $recipeIngredient = RecipeIngredient::create([
            'recipe_id' => $this->recipe->id,
            'ingredient_id' => $this->ingredient->id,
            'quantity' => 100,
            'unit' => 'grams',
        ]);

        $response = $this->putJson("/api/recipes/{$this->recipe->id}/ingredients/{$recipeIngredient->id}", [
            'quantity' => 150,
            'unit' => 'ml',
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            'quantity' => 150,
            'unit' => 'ml',
        ]);

        $this->assertDatabaseHas('recipe_ingredients', [
            'id' => $recipeIngredient->id,
            'quantity' => 150,
            'unit' => 'ml',
        ]);
    }

    #[Test]
    public function can_delete_recipe_ingredient()
    {
        $recipeIngredient = RecipeIngredient::create([
            'recipe_id' => $this->recipe->id,
            'ingredient_id' => $this->ingredient->id,
            'quantity' => 100,
            'unit' => 'grams',
        ]);

        $response = $this->deleteJson("/api/recipes/{$this->recipe->id}/ingredients/{$recipeIngredient->id}");

        $response->assertStatus(200)->assertJsonFragment(['message' => 'Recipe ingredient deleted']);

        $this->assertDatabaseMissing('recipe_ingredients', [
            'id' => $recipeIngredient->id,
        ]);
    }
}
