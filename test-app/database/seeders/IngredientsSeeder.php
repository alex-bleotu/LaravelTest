<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;

class IngredientsSeeder extends Seeder
{
    public function run()
    {
        Ingredient::factory(50)->create();
    }
}
