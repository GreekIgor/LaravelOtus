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
    protected $signature = 'cache:warm 
                            {entity? : Тип сущности для прогрева (all|stats|recipes|ingredients|users|pages)}
                            {--pages=1 : Количество страниц для прогрева}
                            {--recipes-count=20 : Количество рецептов для прогрева}
                            {--force : Очистить кэш перед прогревом}
                            {--detailed : Подробный вывод процесса}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Прогрев кэша для основных сущностей и страниц приложения';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $entity = $this->argument('entity') ?? 'all';
        $pages = (int) $this->option('pages');
        $recipesCount = (int) $this->option('recipes-count');
        $force = $this->option('force');
        $detailed = $this->option('detailed');

        // Валидация аргумента entity
        $validEntities = ['all', 'stats', 'recipes', 'ingredients', 'users', 'pages'];
        if (!in_array($entity, $validEntities)) {
            $this->error("Неверный тип сущности: {$entity}");
            $this->info("Доступные значения: " . implode(', ', $validEntities));
            return Command::FAILURE;
        }

        // Очистка кэша при опции --force
        if ($force) {
            $this->info('Очистка кэша...');
            Cache::flush();
            $this->info('Кэш очищен.');
            $this->newLine();
        }

        $this->info("Начинаем прогрев кэша для: {$entity}");

        if ($detailed) {
            $this->line("Опции: pages={$pages}, recipes-count={$recipesCount}, force=" . ($force ? 'да' : 'нет'));
            $this->newLine();
        }

        try {
            $stats = [
                'cached' => 0,
                'items' => []
            ];

            // Прогрев в зависимости от аргумента entity
            switch ($entity) {
                case 'all':
                    $stats = $this->warmAll($pages, $recipesCount, $detailed);
                    break;
                case 'stats':
                    $stats = $this->warmStats($detailed);
                    break;
                case 'recipes':
                    $stats = $this->warmRecipes($pages, $recipesCount, $detailed);
                    break;
                case 'ingredients':
                    $stats = $this->warmIngredients($detailed);
                    break;
                case 'users':
                    $stats = $this->warmUsers($detailed);
                    break;
                case 'pages':
                    $stats = $this->warmPages($pages, $detailed);
                    break;
            }

            $this->newLine();
            $this->info("Прогрев кэша завершен успешно!");
            
            if ($detailed) {
                $this->newLine();
                $this->info("Статистика:");
                $this->line("  Закэшировано элементов: {$stats['cached']}");
                if (!empty($stats['items'])) {
                    $this->line("  Детали:");
                    foreach ($stats['items'] as $item) {
                        $this->line("    - {$item}");
                    }
                }
            }
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Ошибка при прогреве кэша: ' . $e->getMessage());
            if ($detailed) {
                $this->error('Трассировка: ' . $e->getTraceAsString());
            }
            return Command::FAILURE;
        }
    }

    /**
     * Прогрев всех сущностей
     */
    protected function warmAll(int $pages, int $recipesCount, bool $detailed): array
    {
        $stats = ['cached' => 0, 'items' => []];
        
        $statsStats = $this->warmStats($detailed);
        $statsRecipes = $this->warmRecipes($pages, $recipesCount, $detailed);
        $statsIngredients = $this->warmIngredients($detailed);
        $statsUsers = $this->warmUsers($detailed);
        $statsPages = $this->warmPages($pages, $detailed);
        
        $stats['cached'] = $statsStats['cached'] + $statsRecipes['cached'] + 
                          $statsIngredients['cached'] + $statsUsers['cached'] + 
                          $statsPages['cached'];
        $stats['items'] = array_merge(
            $statsStats['items'],
            $statsRecipes['items'],
            $statsIngredients['items'],
            $statsUsers['items'],
            $statsPages['items']
        );
        
        return $stats;
    }

    /**
     * Прогрев статистики админ-панели
     */
    protected function warmStats(bool $detailed): array
    {
        $stats = ['cached' => 0, 'items' => []];

        if ($detailed) {
            $this->line('  Прогрев статистики админ-панели...');
        } else {
            $this->info('Прогрев статистики админ-панели...');
        }
        
        Cache::remember('admin.stats', 300, function () {
            return [
                'users_count' => User::count(),
                'recipes_count' => Recipe::count(),
                'ingredients_count' => Ingredient::count(),
            ];
        });
        $stats['cached']++;
        $stats['items'][] = 'admin.stats';
        
        if ($detailed) {
            $this->comment('     Статистика админ-панели закэширована');
        } else {
            $this->info('   Статистика админ-панели закэширована');
        }

        return $stats;
    }

    /**
     * Прогрев рецептов
     */
    protected function warmRecipes(int $pages, int $recipesCount, bool $detailed): array
    {
        $stats = ['cached' => 0, 'items' => []];

        // Прогрев списка всех рецептов
        if ($detailed) {
            $this->line('  Прогрев списка всех рецептов...');
        } else {
            $this->info('Прогрев списка всех рецептов...');
        }
        
        Cache::remember('recipes.all', 600, function () {
            return Recipe::with(['author', 'ingredients'])->get();
        });
        $stats['cached']++;
        $stats['items'][] = 'recipes.all';
        
        if ($detailed) {
            $this->comment('     Список всех рецептов закэширован');
        } else {
            $this->info('   Список всех рецептов закэширован');
        }

        // Прогрев отдельных рецептов
        if ($recipesCount > 0) {
            if ($detailed) {
                $this->line("  Прогрев отдельных рецептов (первые {$recipesCount})...");
            } else {
                $this->info("Прогрев отдельных рецептов (первые {$recipesCount})...");
            }
            
            $recipes = Recipe::take($recipesCount)->get();
            $bar = $detailed ? null : $this->output->createProgressBar($recipes->count());
            if ($bar) {
                $bar->start();
            }
            
            foreach ($recipes as $recipe) {
                Cache::remember("recipe.{$recipe->id}", 1800, function () use ($recipe) {
                    return Recipe::with(['author', 'ingredients'])->find($recipe->id);
                });
                $stats['cached']++;
                $stats['items'][] = "recipe.{$recipe->id}";
                
                if ($bar) {
                    $bar->advance();
                }
            }
            
            if ($bar) {
                $bar->finish();
                $this->newLine();
            }
            
            if ($detailed) {
                $this->comment("     Закэшировано рецептов: {$recipes->count()}");
            } else {
                $this->info("   Закэшировано рецептов: {$recipes->count()}");
            }
        }

        return $stats;
    }

    /**
     * Прогрев ингредиентов
     */
    protected function warmIngredients(bool $detailed): array
    {
        $stats = ['cached' => 0, 'items' => []];

        if ($detailed) {
            $this->line('  Прогрев списка всех ингредиентов...');
        } else {
            $this->info('Прогрев списка всех ингредиентов...');
        }
        
        Cache::remember('ingredients.all', 900, function () {
            return Ingredient::all();
        });
        $stats['cached']++;
        $stats['items'][] = 'ingredients.all';
        
        if ($detailed) {
            $this->comment('     Список всех ингредиентов закэширован');
        } else {
            $this->info('   Список всех ингредиентов закэширован');
        }

        return $stats;
    }

    /**
     * Прогрев данных пользователей
     */
    protected function warmUsers(bool $detailed): array
    {
        $stats = ['cached' => 0, 'items' => []];

        // Прогрев графика регистраций
        if ($detailed) {
            $this->line('  Прогрев графика регистраций...');
        } else {
            $this->info('Прогрев графика регистраций...');
        }
        
        Cache::remember('admin.registrations', 600, function () {
            return User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        });
        $stats['cached']++;
        $stats['items'][] = 'admin.registrations';
        
        if ($detailed) {
            $this->comment('     График регистраций закэширован');
        } else {
            $this->info('   График регистраций закэширован');
        }

        // Прогрев ТОП-10 авторов
        if ($detailed) {
            $this->line('  Прогрев ТОП-10 авторов...');
        } else {
            $this->info('Прогрев ТОП-10 авторов...');
        }
        
        Cache::remember('admin.top_authors', 900, function () {
            return User::withCount('recipes')
                ->orderBy('recipes_count', 'desc')
                ->take(10)
                ->get();
        });
        $stats['cached']++;
        $stats['items'][] = 'admin.top_authors';
        
        if ($detailed) {
            $this->comment('     ТОП-10 авторов закэширован');
        } else {
            $this->info('   ТОП-10 авторов закэширован');
        }

        return $stats;
    }

    /**
     * Прогрев страниц с пагинацией
     */
    protected function warmPages(int $pages, bool $detailed): array
    {
        $stats = ['cached' => 0, 'items' => []];

        if ($pages > 0) {
            if ($detailed) {
                $this->line("  Прогрев страниц списка рецептов (страниц: {$pages})...");
            } else {
                $this->info("Прогрев страниц списка рецептов (страниц: {$pages})...");
            }
            
            $bar = $detailed ? null : $this->output->createProgressBar($pages);
            if ($bar) {
                $bar->start();
            }
            
            for ($page = 1; $page <= $pages; $page++) {
                Cache::remember("recipes.list.page.{$page}", 300, function () use ($page) {
                    return Recipe::with(['author', 'ingredients'])->paginate(10, ['*'], 'page', $page);
                });
                $stats['cached']++;
                $stats['items'][] = "recipes.list.page.{$page}";
                
                if ($bar) {
                    $bar->advance();
                }
            }
            
            if ($bar) {
                $bar->finish();
                $this->newLine();
            }
            
            if ($detailed) {
                $this->comment("     Закэшировано страниц: {$pages}");
            } else {
                $this->info("   Закэшировано страниц: {$pages}");
            }
        }

        return $stats;
    }
}

