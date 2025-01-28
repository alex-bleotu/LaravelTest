<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UsersSeeder::class,
            CategoriesSeeder::class,
            IngredientsSeeder::class,
            RecipesSeeder::class,
            CommentsSeeder::class,
            FavoritesSeeder::class,
        ]);
    }
}
