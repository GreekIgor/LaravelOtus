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
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('isAdmin', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('isModerator', function ($user) {
            return $user->role === 'moderator';
        });

        Gate::policy(Recipe::class, RecipePolicy::class);

        // Регистрация кастомного драйвера для Telegram логирования
        Log::extend('telegram', function ($app, $config) {
            $botToken = $config['handler_with']['bot_token'] ?? env('TELEGRAM_BOT_TOKEN');
            $chatId = $config['handler_with']['chat_id'] ?? env('TELEGRAM_CHAT_ID');
            $level = Level::fromName($config['level'] ?? 'error');

            if (empty($botToken) || empty($chatId)) {
                // Если токен или chat_id не настроены, возвращаем null handler
                return Log::channel('null');
            }

            $handler = new TelegramHandler($botToken, $chatId, $level);
            
            return new \Monolog\Logger('telegram', [$handler]);
        });
    }
}
