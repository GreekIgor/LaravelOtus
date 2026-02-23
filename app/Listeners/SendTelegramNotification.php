<?php

namespace App\Listeners;

use App\Events\RecipeCreated;
use App\Events\RecipeUpdated;
use App\Services\TelegramService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTelegramNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(
        protected TelegramService $telegramService
    ) {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RecipeCreated|RecipeUpdated $event): void
    {
        $recipe = $event->recipe;
        $action = $event instanceof RecipeCreated ? 'создан' : 'обновлен';
        
        $message = $this->telegramService->formatRecipeMessage(
            $action,
            $recipe->title,
            $recipe->author->name ?? null,
            $recipe->id
        );

        $this->telegramService->sendMessage($message);
    }
}
