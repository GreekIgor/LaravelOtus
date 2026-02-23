<?php

namespace App\Listeners;

use App\Events\RecipeCreated;
use App\Events\RecipeUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendTelegramNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RecipeCreated|RecipeUpdated $event): void
    {
        $recipe = $event->recipe;
        $action = $event instanceof RecipeCreated ? 'создан' : 'обновлен';
        
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if (empty($botToken) || empty($chatId)) {
            Log::warning('Telegram credentials not configured');
            return;
        }

        $message = "🍳 <b>Рецепт {$action}</b>\n\n";
        $message .= "<b>Название:</b> " . htmlspecialchars($recipe->title) . "\n";
        $message .= "<b>Автор:</b> " . htmlspecialchars($recipe->author->name ?? 'Неизвестен') . "\n";
        $message .= "<b>ID:</b> {$recipe->id}\n";
        $message .= "<b>Время:</b> " . now()->format('Y-m-d H:i:s');

        try {
            $response = Http::timeout(10)->post(
                "https://api.telegram.org/bot{$botToken}/sendMessage",
                [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ]
            );

            if (!$response->successful()) {
                Log::error('Failed to send Telegram notification', [
                    'response' => $response->body(),
                    'recipe_id' => $recipe->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception while sending Telegram notification', [
                'message' => $e->getMessage(),
                'recipe_id' => $recipe->id,
            ]);
        }
    }
}
