<?php

use App\Http\Controllers\RecipeStepController;

Route::prefix('recipes/{recipeId}/steps')->group(function () {
    Route::get('/', [RecipeStepController::class, 'index']);
    Route::get('/{id}', [RecipeStepController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [RecipeStepController::class, 'store']);
        Route::put('/{id}', [RecipeStepController::class, 'update']);
        Route::delete('/{id}', [RecipeStepController::class, 'destroy']);
    });
});
