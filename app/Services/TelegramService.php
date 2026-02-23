<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class TelegramService
{
    protected string $botToken;
    protected string $chatId;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN', '');
        $this->chatId = env('TELEGRAM_CHAT_ID', '');
    }

    /**
     * Отправить сообщение в Telegram
     *
     * @param string $message Текст сообщения (поддерживает HTML)
     * @param string|null $chatId ID чата (если не указан, используется из конфигурации)
     * @return bool Успешность отправки
     */
    public function sendMessage(string $message, ?string $chatId = null): bool
    {
        $chatId = $chatId ?? $this->chatId;

        if (empty($this->botToken) || empty($chatId)) {
            Log::warning('Telegram credentials not configured');
            return false;
        }

        // Telegram имеет лимит 4096 символов на сообщение
        if (mb_strlen($message) > 4000) {
            $message = mb_substr($message, 0, 3900) . "\n... (message truncated)";
        }

        try {
            $response = Http::timeout(10)->post(
                "https://api.telegram.org/bot{$this->botToken}/sendMessage",
                [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ]
            );

            if (!$response->successful()) {
                Log::error('Failed to send Telegram message', [
                    'response' => $response->body(),
                    'chat_id' => $chatId,
                ]);
                return false;
            }

            return true;
        } catch (Exception $e) {
            Log::error('Exception while sending Telegram message', [
                'message' => $e->getMessage(),
                'chat_id' => $chatId,
            ]);
            return false;
        }
    }

    /**
     * Форматировать сообщение о рецепте
     *
     * @param string $action Действие (создан/обновлен)
     * @param string $title Название рецепта
     * @param string|null $authorName Имя автора
     * @param int $recipeId ID рецепта
     * @return string Отформатированное сообщение
     */
    public function formatRecipeMessage(
        string $action,
        string $title,
        ?string $authorName,
        int $recipeId
    ): string {
        $message = "🍳 <b>Рецепт {$action}</b>\n\n";
        $message .= "<b>Название:</b> " . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . "\n";
        $message .= "<b>Автор:</b> " . htmlspecialchars($authorName ?? 'Неизвестен', ENT_QUOTES, 'UTF-8') . "\n";
        $message .= "<b>ID:</b> {$recipeId}\n";
        $message .= "<b>Время:</b> " . now()->format('Y-m-d H:i:s');

        return $message;
    }
}
