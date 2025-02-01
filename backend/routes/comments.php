<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentController;

Route::prefix('/recipes')->group(function () {
    Route::get('/{recipeId}/comments', [CommentController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/{recipeId}/comments', [CommentController::class, 'store']);
        Route::delete('/{recipeId}/comments/{commentId}', [CommentController::class, 'destroy']);
    });
});