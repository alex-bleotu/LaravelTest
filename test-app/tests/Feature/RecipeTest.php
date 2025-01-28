<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RecipeTest extends TestCase
{
    public function test_can_create_recipe()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/recipes', [
            'title' => 'Sample Recipe',
            'description' => 'This is a sample recipe.',
            'ingredients' => [
                ['id' => 1, 'quantity' => 200, 'unit' => 'grams']
            ],
            'steps' => [
                ['step_number' => 1, 'description' => 'Preheat the oven.']
            ],
        ]);

        $response->assertStatus(201);
    }
}
