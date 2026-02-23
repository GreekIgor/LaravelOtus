<?php

namespace App\Listeners;

use App\Events\RecipeCreated;
use App\Events\RecipeUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class CreateActivityLog implements ShouldQueue
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
        $action = $event instanceof RecipeCreated ? 'created' : 'updated';
        
        $logMessage = "Recipe {$action}: ID={$recipe->id}, Title=\"{$recipe->title}\", Author={$recipe->author->name ?? 'Unknown'} (ID: {$recipe->author->id ?? 'N/A'})";
        
        Log::channel('daily')->info($logMessage, [
            'recipe_id' => $recipe->id,
            'recipe_title' => $recipe->title,
            'author_id' => $recipe->author->id ?? null,
            'author_name' => $recipe->author->name ?? null,
            'action' => $action,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
