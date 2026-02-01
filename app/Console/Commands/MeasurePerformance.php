<?php

namespace App\Console\Commands;

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class MeasurePerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:measure-performance {--iterations=10 : Количество итераций для замера}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Замер производительности с кэшем и без него';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $iterations = (int) $this->option('iterations');
        
        $this->info("Замер производительности (итераций: {$iterations})...");
        $this->newLine();

        // Очищаем кэш перед началом замеров
        Cache::flush();
        
        $results = [
            'admin_stats' => $this->measureAdminStats($iterations),
            'recipes_all' => $this->measureRecipesAll($iterations),
            'ingredients_all' => $this->measureIngredientsAll($iterations),
            'top_authors' => $this->measureTopAuthors($iterations),
            'recipe_by_id' => $this->measureRecipeById($iterations),
        ];

        $this->displayResults($results);
        
        return Command::SUCCESS;
    }

    /**
     * Замер производительности статистики админ-панели
     */
    protected function measureAdminStats(int $iterations): array
    {
        // Без кэша
        $start = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $stats = [
                'users_count' => User::count(),
                'recipes_count' => Recipe::count(),
                'ingredients_count' => Ingredient::count(),
            ];
        }
        $timeWithoutCache = (microtime(true) - $start) * 1000;

        // С кэшем
        $start = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $stats = Cache::remember('admin.stats', 300, function () {
                return [
                    'users_count' => User::count(),
                    'recipes_count' => Recipe::count(),
                    'ingredients_count' => Ingredient::count(),
                ];
            });
        }
        $timeWithCache = (microtime(true) - $start) * 1000;

        return [
            'name' => 'Статистика админ-панели',
            'without_cache' => $timeWithoutCache,
            'with_cache' => $timeWithCache,
            'improvement' => (($timeWithoutCache - $timeWithCache) / $timeWithoutCache) * 100,
        ];
    }

    /**
     * Замер производительности списка всех рецептов
     */
    protected function measureRecipesAll(int $iterations): array
    {
        // Без кэша
        $start = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $recipes = Recipe::with(['author', 'ingredients'])->get();
        }
        $timeWithoutCache = (microtime(true) - $start) * 1000;

        // С кэшем
        $start = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $recipes = Cache::remember('recipes.all', 600, function () {
                return Recipe::with(['author', 'ingredients'])->get();
            });
        }
        $timeWithCache = (microtime(true) - $start) * 1000;

        return [
            'name' => 'Список всех рецептов',
            'without_cache' => $timeWithoutCache,
            'with_cache' => $timeWithCache,
            'improvement' => (($timeWithoutCache - $timeWithCache) / $timeWithoutCache) * 100,
        ];
    }

    /**
     * Замер производительности списка всех ингредиентов
     */
    protected function measureIngredientsAll(int $iterations): array
    {
        // Без кэша
        $start = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $ingredients = Ingredient::all();
        }
        $timeWithoutCache = (microtime(true) - $start) * 1000;

        // С кэшем
        $start = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $ingredients = Cache::remember('ingredients.all', 900, function () {
                return Ingredient::all();
            });
        }
        $timeWithCache = (microtime(true) - $start) * 1000;

        return [
            'name' => 'Список всех ингредиентов',
            'without_cache' => $timeWithoutCache,
            'with_cache' => $timeWithCache,
            'improvement' => (($timeWithoutCache - $timeWithCache) / $timeWithoutCache) * 100,
        ];
    }

    /**
     * Замер производительности ТОП авторов
     */
    protected function measureTopAuthors(int $iterations): array
    {
        // Без кэша
        $start = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $topAuthors = User::withCount('recipes')
                ->orderBy('recipes_count', 'desc')
                ->take(10)
                ->get();
        }
        $timeWithoutCache = (microtime(true) - $start) * 1000;

        // С кэшем
        $start = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $topAuthors = Cache::remember('admin.top_authors', 900, function () {
                return User::withCount('recipes')
                    ->orderBy('recipes_count', 'desc')
                    ->take(10)
                    ->get();
            });
        }
        $timeWithCache = (microtime(true) - $start) * 1000;

        return [
            'name' => 'ТОП-10 авторов',
            'without_cache' => $timeWithoutCache,
            'with_cache' => $timeWithCache,
            'improvement' => (($timeWithoutCache - $timeWithCache) / $timeWithoutCache) * 100,
        ];
    }

    /**
     * Замер производительности получения рецепта по ID
     */
    protected function measureRecipeById(int $iterations): array
    {
        $recipeId = Recipe::first()?->id ?? 1;

        // Без кэша
        $start = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $recipe = Recipe::with(['author', 'ingredients'])->find($recipeId);
        }
        $timeWithoutCache = (microtime(true) - $start) * 1000;

        // С кэшем
        $start = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $recipe = Cache::remember("recipe.{$recipeId}", 1800, function () use ($recipeId) {
                return Recipe::with(['author', 'ingredients'])->find($recipeId);
            });
        }
        $timeWithCache = (microtime(true) - $start) * 1000;

        return [
            'name' => 'Рецепт по ID',
            'without_cache' => $timeWithoutCache,
            'with_cache' => $timeWithCache,
            'improvement' => (($timeWithoutCache - $timeWithCache) / $timeWithoutCache) * 100,
        ];
    }

    /**
     * Вывод результатов
     */
    protected function displayResults(array $results): void
    {
        $this->info('Результаты замеров производительности:');
        $this->newLine();

        $headers = ['Операция', 'Без кэша (мс)', 'С кэшем (мс)', 'Улучшение (%)'];
        $rows = [];

        foreach ($results as $result) {
            $rows[] = [
                $result['name'],
                number_format($result['without_cache'], 2),
                number_format($result['with_cache'], 2),
                number_format($result['improvement'], 2) . '%',
            ];
        }

        $this->table($headers, $rows);

        $avgImprovement = array_sum(array_column($results, 'improvement')) / count($results);
        $this->newLine();
        $this->info("Среднее улучшение производительности: " . number_format($avgImprovement, 2) . "%");
    }
}

