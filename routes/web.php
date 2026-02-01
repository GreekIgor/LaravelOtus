<?php

use App\Http\Controllers\IngredientController;
use App\Http\Controllers\ProfileController;
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


Route::get('/recipe/{recipe}', [RecipeController::class, 'showRecipe'])->name('recipe.detail');


Route::get('/recipe-list', [RecipeController::class, 'showRecipeList'])->name('recipes.list');

Route::get('/recipe-edit/{recipe}', [RecipeController::class, 'showRecipeEdit'])->name('recipe-edit');

Route::put('/recipe-edit/{recipe}', [RecipeController::class, 'update'])->name('recipe-edit');

Route::post('/recipe-store', [RecipeController::class, 'store'])->name('recipe-store');

Route::get('/recipe-create', [RecipeController::class, 'showRecipeCreate'])->name('recipe-create');

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


// Группа маршрутов, защищенных аутентификацией
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Маршрут Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard'); // Убедитесь, что файл resources/views/dashboard.blade.php существует
    })->name('dashboard');

    // Маршруты профиля (обычно нужны для Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Ваши маршруты ингредиентов (добавьте их в эту же или отдельную группу)
Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::resource('ingredients', IngredientController::class);
});


require __DIR__.'/auth.php';

