<?php

use App\Http\Controllers\IngredientController;
use App\Http\Controllers\RecipeController;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\RecipeRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('Home');
});

Route::get('/personal', function () {
    return view('UserPersonal');
});

Route::get('/recipe/{recipe}', function (Recipe $recipe) {
    return view('Recipe', compact('recipe'));
})->name('recipe.detail');

Route::get('/list', [RecipeController::class, 'list'])->name('recipes.list');

Route::get('/recipe-edit/{recipe}', function (Recipe $recipe) {
    Gate::authorize('update', $recipe);
    return view('recipe.edit', compact('recipe'));
})->name('recipe-edit');

Route::put('/recipe-edit/{recipe}', [RecipeController::class, 'update'])->name('recipe-edit');
Route::post('/recipe-store', [RecipeController::class, 'store'])->name('recipe-store');

Route::get('/recipe-create', function () {
    Gate::authorize('create', Recipe::class);
    return view('recipe.edit', ['recipe' => new Recipe()]);
})->name('recipe-create');

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {

  
    Route::get('/', function () {
          if (!Gate::allows('isAdmin')){
                abort(403);
            }
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
    })->name('admin.dashboard');

    Route::resource('ingredients', IngredientController::class);
    Route::resource('recipes', RecipeController::class);
});


Route::get('/profile-edit', function () {
    return view('profile.edit');
})->name('profile.edit');

Route::get('/dbrecipe', function () {
    dd(Tag::all());
    return '<h1>Debugbar test</h1>';
});

require __DIR__.'/auth.php';

