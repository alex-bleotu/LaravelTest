<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function store($recipeId)
    {
        $user = auth()->user();
        $user->favorites()->attach($recipeId);

        return response()->json(['message' => 'Recipe added to favorites'], 201);
    }

    public function destroy($recipeId)
    {
        $user = auth()->user();
        $user->favorites()->detach($recipeId);

        return response()->json(['message' => 'Recipe removed from favorites'], 200);
    }

    public function index()
    {
        $user = auth()->user();
        return $user->favorites()->paginate(10);
    }
}
