<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RecipeIngredientFactory extends Factory
{
    public function definition()
    {
        return [
            'recipe_id' => \App\Models\Recipe::factory(),
            'ingredient_id' => \App\Models\Ingredient::factory(),
            'quantity' => $this->faker->randomFloat(2, 1, 500),
            'unit' => $this->faker->randomElement(['g', 'ml', 'pcs']),
        ];
    }
}
