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
        $author = $recipe->author;
        $authorName = $author ? $author->name : 'Unknown';
        $authorIdForMessage = $author ? $author->id : 'N/A';
        
        $logMessage = "Recipe {$action}: ID={$recipe->id}, Title=\"{$recipe->title}\", Author={$authorName} (ID: {$authorIdForMessage})";
        
        Log::channel('daily')->info($logMessage, [
            'recipe_id' => $recipe->id,
            'recipe_title' => $recipe->title,
            'author_id' => $author ? $author->id : null,
            'author_name' => $author ? $author->name : null,
            'action' => $action,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
