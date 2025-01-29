<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserRecipeController extends Controller
{
    public function index()
    {
        return Recipe::where('public', true)
            ->orWhere('user_id', auth()->id())
            ->with(['ingredients', 'steps'])
            ->paginate(10);
    }

    public function myRecipes()
    {
        return Recipe::where('user_id', auth()->id())
            ->with(['ingredients', 'steps'])
            ->paginate(10);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'nullable|image|max:2048',
            'ingredients' => 'required|array',
            'ingredients.*.id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.1',
            'ingredients.*.unit' => 'required|string|max:50',
            'steps' => 'required|array',
            'steps.*.description' => 'required|string',
            'steps.*.order' => 'required|integer|min:1',
            'public' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            if ($request->hasFile('thumbnail')) {
                $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
            }

            $recipe = Recipe::create([
                'user_id' => auth()->id(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'thumbnail' => $validated['thumbnail'] ?? null,
                'public' => $validated['public'] ?? false,
            ]);

            foreach ($validated['ingredients'] as $ingredient) {
                $recipe->ingredients()->attach($ingredient['id'], [
                    'quantity' => $ingredient['quantity'],
                    'unit' => $ingredient['unit'],
                ]);
            }

            foreach ($validated['steps'] as $step) {
                $recipe->steps()->create($step);
            }

            DB::commit();
            return response()->json($recipe->load('ingredients', 'steps'), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function show($id)
    {
        $recipe = Recipe::where('id', $id)
            ->where(function ($query) {
                $query->where('public', true)
                      ->orWhere('user_id', auth()->id());
            })
            ->with(['ingredients', 'steps'])
            ->firstOrFail();

        return response()->json($recipe);
    }

    public function update(Request $request, $id)
    {
        $recipe = Recipe::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'thumbnail' => 'nullable|image|max:2048',
            'ingredients' => 'sometimes|array',
            'ingredients.*.id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.1',
            'ingredients.*.unit' => 'required|string|max:50',
            'steps' => 'sometimes|array',
            'steps.*.description' => 'required|string',
            'steps.*.order' => 'required|integer|min:1',
            'public' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            if ($request->hasFile('thumbnail')) {
                if ($recipe->thumbnail) {
                    Storage::disk('public')->delete($recipe->thumbnail);
                }
                $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
            }

            $recipe->update($validated);

            if ($request->has('ingredients')) {
                $recipe->ingredients()->detach();
                foreach ($validated['ingredients'] as $ingredient) {
                    $recipe->ingredients()->attach($ingredient['id'], [
                        'quantity' => $ingredient['quantity'],
                        'unit' => $ingredient['unit'],
                    ]);
                }
            }

            if ($request->has('steps')) {
                $recipe->steps()->delete();
                foreach ($validated['steps'] as $step) {
                    $recipe->steps()->create($step);
                }
            }

            DB::commit();
            return response()->json($recipe->load('ingredients', 'steps'), 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function destroy($id)
    {
        $recipe = Recipe::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($recipe->thumbnail) {
            Storage::disk('public')->delete($recipe->thumbnail);
        }

        $recipe->delete();

        return response()->json(['message' => 'Recipe deleted'], 200);
    }
}
