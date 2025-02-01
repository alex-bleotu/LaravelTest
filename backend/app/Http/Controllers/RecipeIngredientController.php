<?php

namespace App\Http\Controllers;

use App\Models\RecipeIngredient;
use Illuminate\Http\Request;

class RecipeIngredientController extends Controller
{
    public function index($recipeId)
    {
        return RecipeIngredient::where('recipe_id', $recipeId)
            ->with('ingredient')
            ->get();
    }

    public function store(Request $request, $recipeId)
    {
        $validated = $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|numeric|min:0.1',
            'unit' => 'required|string|max:50',
        ]);

        $validated['recipe_id'] = $recipeId;

        $recipeIngredient = RecipeIngredient::create($validated);

        return response()->json($recipeIngredient->load('ingredient'), 201);
    }

    public function update(Request $request, $id)
    {
        $recipeIngredient = RecipeIngredient::findOrFail($id);

        $validated = $request->validate([
            'quantity' => 'sometimes|numeric|min:0.1',
            'unit' => 'sometimes|string|max:50',
        ]);

        $recipeIngredient->update($validated);

        return response()->json($recipeIngredient->load('ingredient'));
    }

    public function destroy($id)
    {
        $recipeIngredient = RecipeIngredient::findOrFail($id);
        $recipeIngredient->delete();

        return response()->json(['message' => 'Recipe ingredient deleted'], 200);
    }
}
