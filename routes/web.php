<?php

use App\Http\Controllers\IngredientController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipeController;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\RecipeRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

// Маршрут для переключения языков
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

// Главная страница
Route::get('/', function () {
    return view('Home');
})->name('home');

Route::get('/personal', function () {
    return view('UserPersonal');
})->name('personal');

Route::get('/recipe/{recipe}', [RecipeController::class, 'showRecipe'])
    ->where('recipe', '[0-9]+')
    ->name('recipe.detail');

Route::get('/recipe-list', [RecipeController::class, 'showRecipeList'])->name('recipes.list');

Route::get('/recipe-edit/{recipe}', [RecipeController::class, 'showRecipeEdit'])
    ->where('recipe', '[0-9]+')
    ->name('recipe-edit');

Route::put('/recipe-edit/{recipe}', [RecipeController::class, 'update'])
    ->where('recipe', '[0-9]+')
    ->name('recipe-edit.update');

Route::post('/recipe-store', [RecipeController::class, 'store'])->name('recipe-store');

Route::get('/recipe-create', [RecipeController::class, 'showRecipeCreate'])->name('recipe-create');

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    Route::get('/', function () {
        // Используем разрешение view-admin-dashboard вместо проверки роли
        Gate::authorize('view-admin-dashboard');
        
        // Статистика для карточек (кэш на 5 минут)
        $stats = Cache::remember('admin.stats', 300, function () {
            return [
                'users_count' => User::count(),
                'recipes_count' => Recipe::count(),
                'ingredients_count' => Ingredient::count(),
            ];
        });

        // Данные для графика регистрации (за последние 7 дней, кэш на 10 минут)
        $registrations = Cache::remember('admin.registrations', 600, function () {
            return User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        });

        // ТОП-10 авторов (по количеству рецептов, кэш на 15 минут)
        $topAuthors = Cache::remember('admin.top_authors', 900, function () {
            return User::withCount('recipes')
                ->orderBy('recipes_count', 'desc')
                ->take(10)
                ->get();
        });
        
        return view('admin.dashboard', compact('stats', 'registrations', 'topAuthors'));
    })->name('admin.dashboard');

    Route::resource('ingredients', IngredientController::class);
    Route::resource('recipes', RecipeController::class);
});

Route::get('/profile-edit', function () {
    return view('profile.edit');
})->name('profile.edit.old');

Route::get('/dbrecipe', function () {
    dd(Tag::all());
    return '<h1>Debugbar test</h1>';
})->name('dbrecipe');

// Группа маршрутов, защищенных аутентификацией
Route::middleware(['auth', 'verified'])->group(function () {
    // Маршрут Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Маршруты профиля (обычно нужны для Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



require __DIR__.'/auth.php';

