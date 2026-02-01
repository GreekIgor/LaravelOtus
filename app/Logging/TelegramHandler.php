<?php

namespace App\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use Monolog\Level;
use Illuminate\Support\Facades\Http;
use Monolog\Handler\StreamHandler;
use Exception;

class TelegramHandler extends AbstractProcessingHandler
{
    protected string $botToken;
    protected string $chatId;
    protected string $fallbackPath;

    public function __construct(
        string $botToken,
        string $chatId,
        $level = Level::Error,
        bool $bubble = true
    ) {
        parent::__construct($level, $bubble);
        $this->botToken = $botToken;
        $this->chatId = $chatId;
        $this->fallbackPath = storage_path('logs/telegram-fallback.log');
    }

    /**
     * Отрпавить лог в Тelegram
     */
    protected function write(LogRecord $record): void
    {
        try {
            $message = $this->formatMessage($record);
            
            $response = Http::timeout(5)->post(
                "https://api.telegram.org/bot{$this->botToken}/sendMessage",
                [
                    'chat_id' => $this->chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ]
            );

            // Если запрос не успешен, записываем в fallback лог
            if (!$response->successful()) {
                $this->writeToFallback($record, 'Telegram API error: ' . $response->body());
            }
        } catch (Exception $e) {
            // При любой ошибке записываем в fallback лог
            $this->writeToFallback($record, 'Exception: ' . $e->getMessage());
        }
    }

    /**
     * Форматировать сообщение для Telegram
     */
    protected function formatMessage(LogRecord $record): string
    {
        $level = strtoupper($record->level->getName());
        $message = $record->message;
        $context = $record->context;
        $datetime = $record->datetime->format('Y-m-d H:i:s');

        $formatted = "<b>🚨 " . htmlspecialchars($level, ENT_QUOTES, 'UTF-8') . "</b>\n";
        $formatted .= "<b>Time:</b> " . htmlspecialchars($datetime, ENT_QUOTES, 'UTF-8') . "\n";
        $formatted .= "<b>Message:</b> " . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . "\n";

        // Добавляем информацию о файле и строке, если есть
        if (isset($context['file']) && isset($context['line'])) {
            $formatted .= "<b>File:</b> " . htmlspecialchars($context['file'], ENT_QUOTES, 'UTF-8') . ":" . htmlspecialchars((string)$context['line'], ENT_QUOTES, 'UTF-8') . "\n";
        } elseif (isset($context['exception']) && $context['exception'] instanceof \Throwable) {
            $exception = $context['exception'];
            if ($exception->getFile() && $exception->getLine()) {
                $formatted .= "<b>File:</b> " . htmlspecialchars($exception->getFile(), ENT_QUOTES, 'UTF-8') . ":" . htmlspecialchars((string)$exception->getLine(), ENT_QUOTES, 'UTF-8') . "\n";
            }
        }

        // Добавляем стек трейс, если есть
        if (isset($context['exception']) && $context['exception'] instanceof \Throwable) {
            $exception = $context['exception'];
            $formatted .= "<b>Exception:</b> " . htmlspecialchars(get_class($exception), ENT_QUOTES, 'UTF-8') . "\n";
            $formatted .= "<b>Message:</b> " . htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8') . "\n";
            
            // Ограничиваем стек трейс до первых 10 строк для читаемости
            $trace = $exception->getTraceAsString();
            $traceLines = explode("\n", $trace);
            $tracePreview = implode("\n", array_slice($traceLines, 0, 10));
            if (count($traceLines) > 10) {
                $tracePreview .= "\n... (truncated)";
            }
            $formatted .= "<pre>" . htmlspecialchars($tracePreview, ENT_QUOTES, 'UTF-8') . "</pre>";
        } elseif (!empty($context)) {
            // Если есть другой контекст, добавляем его
            $contextStr = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if (mb_strlen($contextStr) > 1000) {
                $contextStr = mb_substr($contextStr, 0, 1000) . '... (truncated)';
            }
            $formatted .= "<pre>" . htmlspecialchars($contextStr, ENT_QUOTES, 'UTF-8') . "</pre>";
        }

        // Telegram имеет лимит 4096 символов на сообщение
        if (mb_strlen($formatted) > 4000) {
            $formatted = mb_substr($formatted, 0, 3900) . "\n... (message truncated)";
        }

        return $formatted;
    }

    /**
     * Заисать лог в fallback файл при ошибке отправки в Telegram
     */
    protected function writeToFallback(LogRecord $record, string $error): void
    {
        try {
            $fallbackHandler = new StreamHandler($this->fallbackPath, Level::Debug);
            $fallbackHandler->handle($record);
            
            // Также логируем причину fallback
            $fallbackRecord = new LogRecord(
                $record->datetime,
                'telegram',
                Level::Warning,
                "Failed to send to Telegram. {$error}",
                $record->context
            );
            $fallbackHandler->handle($fallbackRecord);
        } catch (Exception $e) {
            // Если и fallback не работает, используем error_log
            error_log("TelegramHandler: Failed to write to fallback log: " . $e->getMessage());
        }
    }
}

