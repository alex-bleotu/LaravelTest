<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Favorite;

class FavoritesSeeder extends Seeder
{
    public function run()
    {
        Favorite::factory(30)->create();
    }
}
