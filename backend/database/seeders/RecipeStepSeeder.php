<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\RecipeStep;

class RecipeStepSeeder extends Seeder
{
    public function run()
    {
        $recipes = Recipe::all();

        if ($recipes->isEmpty()) {
            $this->command->warn("No recipes found. Seeding aborted.");
            return;
        }

        foreach ($recipes as $recipe) {
            for ($i = 1; $i <= rand(3, 6); $i++) {
                RecipeStep::create([
                    'recipe_id' => $recipe->id,
                    'step_number' => $i,
                    'description' => "Step {$i} for recipe {$recipe->title}",
                    'image_path' => null,
                ]);
            }
        }

        $this->command->info("Recipe steps seeded successfully!");
    }
}
