<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recipe;

class RecipeFactory extends Factory
{
    protected $model = Recipe::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(3),
            'public' => $this->faker->boolean(50),
            'category_id' => Category::factory(),
            'thumbnail' => $this->faker->imageUrl(640, 480, 'food', true, 'Recipe Thumbnail'),
            'prep_time' => $this->faker->numberBetween(10, 60),
            'cook_time' => $this->faker->numberBetween(10, 60),
            'servings' => $this->faker->numberBetween(1, 10),
            'total_calories' => $this->faker->randomFloat(2, 100, 1000),
            'total_protein' => $this->faker->randomFloat(2, 10, 50),
            'total_fat' => $this->faker->randomFloat(2, 5, 50),
            'total_carbs' => $this->faker->randomFloat(2, 10, 150),
            'description' => $this->faker->paragraph(),
            'ingredients' => $this->generateRandomIngredients(),
        ];
    }

    private function generateRandomIngredients()
    {
        return collect(Ingredient::inRandomOrder()->limit(rand(2, 5))->get())->map(function ($ingredient) {
            return [
                'id' => $ingredient->id,
                'name' => $ingredient->name,
                'quantity' => rand(1, 500),
                'unit' => 'g',
            ];
        })->toArray();
    }
}
