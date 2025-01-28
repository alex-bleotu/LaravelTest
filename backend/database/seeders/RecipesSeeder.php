<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;

class RecipesSeeder extends Seeder
{
    public function run()
    {
        Recipe::factory(20)
            ->hasSteps(5)
            ->hasIngredients(10)
            ->create();
    }
}
