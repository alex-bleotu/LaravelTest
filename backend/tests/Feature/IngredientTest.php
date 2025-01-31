<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Ingredient;

class IngredientTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_paginated_ingredients()
    {
        Ingredient::factory()->count(15)->create();

        $response = $this->getJson('/api/ingredients');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data',
                     'links',
                     'meta'
                 ])
                 ->assertJsonCount(10, 'data');
    }

    public function test_it_returns_a_single_ingredient()
    {
        $ingredient = Ingredient::factory()->create();

        $response = $this->getJson("/api/ingredients/{$ingredient->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $ingredient->id,
                     'name' => $ingredient->name,
                 ]);
    }

    public function test_it_returns_a_404_if_ingredient_not_found()
    {
        $response = $this->getJson('/api/ingredients/9999'); 

        $response->assertStatus(404);
    }
}
