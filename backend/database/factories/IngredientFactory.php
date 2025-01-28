<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class IngredientFactory extends Factory
{
    public function definition()
    {
        return [
            'fatsecret_id' => $this->faker->unique()->numberBetween(1000, 9999),
            'name' => $this->faker->word(),
            'calories' => $this->faker->randomFloat(2, 50, 500),
            'protein' => $this->faker->randomFloat(2, 0, 50),
            'fat' => $this->faker->randomFloat(2, 0, 50),
            'carbs' => $this->faker->randomFloat(2, 0, 100),
            'fiber' => $this->faker->randomFloat(2, 0, 30),
        ];
    }
}
