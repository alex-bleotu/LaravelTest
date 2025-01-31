<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IngredientController;

Route::prefix('ingredients')->group(function () {
    Route::get('/', [IngredientController::class, 'index']);
    Route::get('/{id}', [IngredientController::class, 'show']);
});