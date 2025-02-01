<?php

use App\Http\Controllers\RecipeController;

Route::prefix('recipes')->group(function () {
    Route::get('/', [RecipeController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/mine', [RecipeController::class, 'myRecipes']);
        Route::post('/', [RecipeController::class, 'store']);
        Route::put('/{id}', [RecipeController::class, 'update']);
        Route::delete('/{id}', [RecipeController::class, 'destroy']);
    });

    Route::get('/{id}', [RecipeController::class, 'show']);
});