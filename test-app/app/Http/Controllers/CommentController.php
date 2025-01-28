<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $recipeId)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $recipe = Recipe::findOrFail($recipeId);
        $comment = $recipe->comments()->create([
            'content' => $validated['content'],
            'user_id' => auth()->id(),
        ]);

        return response()->json($comment, 201);
    }

    public function index($recipeId)
    {
        $recipe = Recipe::findOrFail($recipeId);
        return $recipe->comments()->with('user')->paginate(10);
    }
}
