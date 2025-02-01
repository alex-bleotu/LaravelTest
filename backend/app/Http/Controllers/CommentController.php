<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;

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

    public function destroy($recipeId, $commentId)
    {
        $recipe = Recipe::findOrFail($recipeId);

        $comment = $recipe->comments()->findOrFail($commentId);

        if ($comment->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(null, 204);
    }
}
