<?php

namespace App\Providers;

use App\Events\RecipeCreated;
use App\Events\RecipeUpdated;
use App\Listeners\CreateActivityLog;
use App\Listeners\SendEmailNotification;
use App\Listeners\SendTelegramNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        RecipeCreated::class => [
            SendTelegramNotification::class,
            SendEmailNotification::class,
            CreateActivityLog::class,
        ],
        RecipeUpdated::class => [
            SendTelegramNotification::class,
            SendEmailNotification::class,
            CreateActivityLog::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
