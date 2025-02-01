<?php

namespace App\Http\Controllers;

use App\Models\RecipeStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecipeStepController extends Controller
{
    public function index($recipeId)
    {
        return RecipeStep::where('recipe_id', $recipeId)
            ->orderBy('step_number')
            ->get();
    }

    public function store(Request $request, $recipeId)
    {
        $validated = $request->validate([
            'step_number' => 'required|integer|min:1',
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('steps', 'public');
        }

        $validated['recipe_id'] = $recipeId;

        $step = RecipeStep::create($validated);

        return response()->json($step, 201);
    }

    public function show($id)
    {
        return response()->json(RecipeStep::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $step = RecipeStep::findOrFail($id);

        $validated = $request->validate([
            'step_number' => 'sometimes|integer|min:1',
            'description' => 'sometimes|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($step->image_path) {
                Storage::disk('public')->delete($step->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('steps', 'public');
        }

        $step->update($validated);

        return response()->json($step);
    }

    public function destroy($id)
    {
        $step = RecipeStep::findOrFail($id);

        if ($step->image_path) {
            Storage::disk('public')->delete($step->image_path);
        }

        $step->delete();

        return response()->json(['message' => 'Recipe step deleted'], 200);
    }
}
