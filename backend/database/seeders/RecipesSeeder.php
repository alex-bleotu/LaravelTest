<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\Ingredient;

class RecipesSeeder extends Seeder
{
    public function run()
    {
        Recipe::factory(20)
            ->hasSteps(5)
            ->create()
            ->each(function ($recipe) {
                $ingredients = Ingredient::inRandomOrder()->limit(rand(2, 5))->get()->map(function ($ingredient) {
                    return [
                        'id' => $ingredient->id,
                        'name' => $ingredient->name,
                        'quantity' => rand(1, 500),
                        'unit' => 'g',
                    ];
                })->toArray();
                $recipe->update(['ingredients' => $ingredients]);
            });
    }
}
