<?php

namespace App\Providers;

use App\Logging\TelegramHandler;
use App\Models\Recipe;
use App\Policies\RecipePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Monolog\Level;

class AppServiceProvider extends ServiceProvider
{
    private const ROLE_PERMISSIONS = [
        'admin' => [
            'view-admin-dashboard',
            'manage-recipes',
            'edit-own-recipes',
            'create-recipes',
            'delete-recipes',
            'manage-ingredients',
            'manage-users',
            'view-statistics',
        ],
        'moderator' => [
            'create-recipes',
            'edit-own-recipes',
            'view-statistics',
        ],
        'viewer' => [
            'create-recipes',
            'edit-own-recipes',
        ],
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Проверка, имеет ли пользователь разрешение
     */
    private function userHasPermission($user, string $permission): bool
    {
        $userRole = $user->role ?? 'viewer';

        return in_array($permission, self::ROLE_PERMISSIONS[$userRole] ?? [], true);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Определяем разрешения через Gate
        $this->defineAbilities();

        Gate::define('isAdmin', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('isModerator', function ($user) {
            return $user->role === 'moderator';
        });

        Gate::policy(Recipe::class, RecipePolicy::class);

        // Регистрация кастомного драйвера для Telegram логирования
        Log::extend('telegram', function ($app, $config) {
            $botToken = $config['handler_with']['bot_token'] ?? null;
            $chatId = $config['handler_with']['chat_id'] ?? null;
            $level = Level::fromName($config['level'] ?? 'error');

            if (empty($botToken) || empty($chatId)) {
                // Если токен или chat_id не настроены, возвращаем null handler
                return Log::channel('null');
            }

            $handler = new TelegramHandler($botToken, $chatId, $level);
            
            return new \Monolog\Logger('telegram', [$handler]);
        });
    }

    /**
     * Определение всех разрешений (abilities) в системе
     */
    private function defineAbilities(): void
    {
        // Просмотр админ-панели
        Gate::define('view-admin-dashboard', function ($user) {
            return $this->userHasPermission($user, 'view-admin-dashboard');
        });

        // Полное управление рецептами (создание, редактирование, удаление любых)
        Gate::define('manage-recipes', function ($user) {
            return $this->userHasPermission($user, 'manage-recipes');
        });

        // Редактирование только своих рецептов
        Gate::define('edit-own-recipes', function ($user, $recipe = null) {
            if ($this->userHasPermission($user, 'manage-recipes')) {
                return true; // Админы могут редактировать все
            }
            
            if ($this->userHasPermission($user, 'edit-own-recipes')) {
                // Модераторы могут редактировать только свои рецепты
                if (!$recipe) {
                    return false;
                }
                // Получаем user_id через атрибут или отношение
                $recipeUserId = $recipe->user_id ?? $recipe->getAttribute('user_id') ?? null;
                return $recipeUserId && $recipeUserId === $user->id;
            }
            
            return false;
        });

        // Создание рецептов
        Gate::define('create-recipes', function ($user) {
            return $this->userHasPermission($user, 'create-recipes') 
                || $this->userHasPermission($user, 'manage-recipes');
        });

        // Удаление рецептов
        Gate::define('delete-recipes', function ($user) {
            return $this->userHasPermission($user, 'delete-recipes')
                || $this->userHasPermission($user, 'manage-recipes');
        });

        // Управление ингредиентами
        Gate::define('manage-ingredients', function ($user) {
            return $this->userHasPermission($user, 'manage-ingredients');
        });

        // Управление пользователями
        Gate::define('manage-users', function ($user) {
            return $this->userHasPermission($user, 'manage-users');
        });

        // Просмотр статистики
        Gate::define('view-statistics', function ($user) {
            return $this->userHasPermission($user, 'view-statistics')
                || $this->userHasPermission($user, 'view-admin-dashboard');
        });
    }
}
