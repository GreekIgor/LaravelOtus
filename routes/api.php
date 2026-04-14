<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\IngredientApiController;
use App\Http\Controllers\Api\V1\RecipeApiController;
use App\Http\Controllers\Api\V1\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public auth endpoint (only login doesn't require token)
    Route::post('auth/login', [AuthController::class, 'login']);

    // All other endpoints require API token authentication
    Route::middleware('auth.api_token')->group(function () {
        // Auth
        Route::post('auth/logout', [AuthController::class, 'logout']);

        // User profile endpoints
        Route::get('me', [UserProfileController::class, 'show']);

        // Ingredients endpoints
        Route::get('ingredients', [IngredientApiController::class, 'index']);

        // Recipes endpoints
        Route::get('recipes', [RecipeApiController::class, 'index']);
        Route::get('recipes/{recipe}', [RecipeApiController::class, 'show'])
            ->whereNumber('recipe');
        Route::post('recipes', [RecipeApiController::class, 'store']);
        Route::match(['put', 'patch'], 'recipes/{recipe}', [RecipeApiController::class, 'update'])
            ->whereNumber('recipe');
        Route::delete('recipes/{recipe}', [RecipeApiController::class, 'destroy'])
            ->whereNumber('recipe');
    });
});

