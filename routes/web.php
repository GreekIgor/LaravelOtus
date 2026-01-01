<?php

use App\Http\Controllers\IngredientController;
use App\Http\Controllers\RecipeController;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('Home');
});

Route::get('/personal', function () {
    return view('UserPersonal');
});

Route::get('/recipe', function () {
    return view('Recipe');
});

Route::get('/list', function () {
    return view('RecipeList');
});

Route::get('/recipe-edit', function () {
    return view('recipe.edit');
});

Route::group(['prefix' => 'admin'], function () {
    Route::get('/', function () {
        // Статистика для карточек
    $stats = [
        'users_count' => User::count(),
        'recipes_count' => Recipe::count(),
        'ingredients_count' => Ingredient::count(),
    ];

    // Данные для графика регистрации (за последние 7 дней)
    $registrations = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
        ->where('created_at', '>=', now()->subDays(7))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    // ТОП-10 авторов (по количеству рецептов)
    $topAuthors = User::withCount('recipes')
        ->orderBy('recipes_count', 'desc')
        ->take(10)
        ->get();
        return view('admin.dashboard', compact('stats', 'registrations', 'topAuthors'));
    }); //

    Route::resource('ingredients', IngredientController::class);
    Route::resource('recipes', RecipeController::class);
});

Route::get('/dbrecipe', function () {
    dd(Tag::all());
    return '<h1>Debugbar test</h1>';
});


