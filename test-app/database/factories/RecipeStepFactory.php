<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RecipeStepFactory extends Factory
{
    public function definition()
    {
        return [
            'recipe_id' => \App\Models\Recipe::factory(),
            'step_number' => $this->faker->numberBetween(1, 10),
            'description' => $this->faker->sentence(),
            'image_path' => $this->faker->imageUrl(640, 480, 'food', true, 'Recipe Step'),
        ];
    }
}
