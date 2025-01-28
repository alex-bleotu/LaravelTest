<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function index()
    {
        return Recipe::with(['ingredients', 'steps'])->paginate(10);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'nullable|image',
            'ingredients' => 'required|array',
            'steps' => 'required|array',
        ]);

        $recipe = Recipe::create($validated);

        foreach ($request->ingredients as $ingredient) {
            $recipe->ingredients()->attach($ingredient['id'], [
                'quantity' => $ingredient['quantity'],
                'unit' => $ingredient['unit'],
            ]);
        }

        foreach ($request->steps as $step) {
            $recipe->steps()->create($step);
        }

        return response()->json($recipe, 201);
    }

    public function show($id)
    {
        return Recipe::with(['ingredients', 'steps'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $recipe = Recipe::findOrFail($id);
        $recipe->update($request->all());

        return response()->json($recipe, 200);
    }

    public function destroy($id)
    {
        Recipe::findOrFail($id)->delete();

        return response()->json(['message' => 'Recipe deleted'], 200);
    }
}
