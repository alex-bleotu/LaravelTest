<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = ['Breakfast', 'Lunch', 'Dinner', 'Snacks', 'Dessert', 'Vegan', 'Gluten-Free'];

        foreach ($categories as $category) {
            Category::factory()->create(['name' => $category]);
        }
    }
}
