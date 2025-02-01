<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\Ingredient;
use App\Models\RecipeIngredient;

class RecipeIngredientSeeder extends Seeder
{
    public function run()
    {
        $recipes = Recipe::all();
        $ingredients = Ingredient::all();

        if ($recipes->isEmpty() || $ingredients->isEmpty()) {
            $this->command->warn("No recipes or ingredients found. Seeding aborted.");
            return;
        }

        foreach ($recipes as $recipe) {
            $randomIngredients = $ingredients->random(rand(2, 5));

            foreach ($randomIngredients as $ingredient) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe->id,
                    'ingredient_id' => $ingredient->id,
                    'quantity' => rand(1, 500),
                    'unit' => ['grams', 'ml', 'cups', 'pieces'][array_rand(['grams', 'ml', 'cups', 'pieces'])],
                ]);
            }
        }

        $this->command->info("Recipe ingredients seeded successfully!");
    }
}
