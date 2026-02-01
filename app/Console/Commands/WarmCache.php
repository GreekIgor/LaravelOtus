<?php

namespace App\Console\Commands;

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class WarmCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:warm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Прогрев кэша приложения';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начинаем прогрев кэша...');

        try {
            // Прогрев статистики админ-панели
            $this->info('Прогрев статистики админ-панели...');
            Cache::remember('admin.stats', 300, function () {
                return [
                    'users_count' => User::count(),
                    'recipes_count' => Recipe::count(),
                    'ingredients_count' => Ingredient::count(),
                ];
            });
            $this->info('Статистика админ-панели закэширована');

            // Прогрев графика регистраций
            $this->info('Прогрев графика регистраций...');
            Cache::remember('admin.registrations', 600, function () {
                return User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(7))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
            });
            $this->info('График регистраций закэширован');

            // Прогрев ТОП-10 авторов
            $this->info('Прогрев ТОП-10 авторов...');
            Cache::remember('admin.top_authors', 900, function () {
                return User::withCount('recipes')
                    ->orderBy('recipes_count', 'desc')
                    ->take(10)
                    ->get();
            });
            $this->info('ТОП-10 авторов закэширован');

            // Прогрев списка всех рецептов
            $this->info('Прогрев списка всех рецептов...');
            Cache::remember('recipes.all', 600, function () {
                return Recipe::with(['author', 'ingredients'])->get();
            });
            $this->info('Список всех рецептов закэширован');

            // Прогрев списка всех ингредиентов
            $this->info('Прогрев списка всех ингредиентов...');
            Cache::remember('ingredients.all', 900, function () {
                return Ingredient::all();
            });
            $this->info('Список всех ингредиентов закэширован');

            // Прогрев первой страницы списка рецептов
            $this->info('Прогрев первой страницы списка рецептов...');
            Cache::remember('recipes.list.page.1', 300, function () {
                return Recipe::with(['author', 'ingredients'])->paginate(10);
            });
            $this->info('Первая страница списка рецептов закэширована');

            // Прогрев отдельных рецептов (первые 20)
            $this->info('Прогрев отдельных рецептов (первые 20)...');
            $recipes = Recipe::take(20)->get();
            foreach ($recipes as $recipe) {
                Cache::remember("recipe.{$recipe->id}", 1800, function () use ($recipe) {
                    return Recipe::with(['author', 'ingredients'])->find($recipe->id);
                });
            }
            $this->info('Первые 20 рецептов закэшированы');

            $this->newLine();
            $this->info('Прогрев кэша завершен успешно!');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Ошибка при прогреве кэша: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

