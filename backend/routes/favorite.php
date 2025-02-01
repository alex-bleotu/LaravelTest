<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FavoriteController;

Route::middleware('auth:sanctum')->prefix('/recipes/favorites')->group(function () {
    Route::get('/list', [FavoriteController::class, 'index']);
    Route::post('/{recipeId}', [FavoriteController::class, 'store']);
    Route::delete('/{recipeId}', [FavoriteController::class, 'destroy']);
});