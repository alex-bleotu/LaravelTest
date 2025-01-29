<?php

use App\Http\Controllers\RecipeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/recipes', [RecipeController::class, 'index']);
    Route::get('/recipes/mine', [RecipeController::class, 'myRecipes']);
    Route::post('/recipes', [RecipeController::class, 'store']);
    Route::get('/recipes/{id}', [RecipeController::class, 'show']);
    Route::put('/recipes/{id}', [RecipeController::class, 'update']);
    Route::delete('/recipes/{id}', [RecipeController::class, 'destroy']);
});
