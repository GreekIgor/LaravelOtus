<?php

namespace App\Listeners;

use App\Events\RecipeCreated;
use App\Events\RecipeUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEmailNotification implements ShouldQueue
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
        $author = $recipe->author;

        if (!$author || !$author->email) {
            Log::warning('Recipe author email not found', ['recipe_id' => $recipe->id]);
            return;
        }

        $action = $event instanceof RecipeCreated ? 'создан' : 'обновлен';
        $subject = "Рецепт \"{$recipe->title}\" был {$action}";

        try {
            Mail::raw(
                "Здравствуйте, {$author->name}!\n\n" .
                "Ваш рецепт \"{$recipe->title}\" был успешно {$action}.\n\n" .
                "ID рецепта: {$recipe->id}\n" .
                "Время: " . now()->format('Y-m-d H:i:s') . "\n\n" .
                "Спасибо за использование нашего сервиса!",
                function ($message) use ($author, $subject) {
                    $message->to($author->email)
                            ->subject($subject);
                }
            );
        } catch (\Exception $e) {
            Log::error('Failed to send email notification', [
                'message' => $e->getMessage(),
                'recipe_id' => $recipe->id,
                'email' => $author->email,
            ]);
        }
    }
}
