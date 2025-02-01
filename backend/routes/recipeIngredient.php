<?php

use App\Http\Controllers\RecipeIngredientController;

Route::prefix('recipes/{recipeId}/ingredients')->group(function () {
    Route::get('/', [RecipeIngredientController::class, 'index']);
    Route::post('/', [RecipeIngredientController::class, 'store']);
    Route::put('/{id}', [RecipeIngredientController::class, 'update']);
    Route::delete('/{id}', [RecipeIngredientController::class, 'destroy']);
});
