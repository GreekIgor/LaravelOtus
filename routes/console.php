<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Планировщик задач (Laravel Scheduler)
Schedule::call(function () {
    // Полный сброс кэша раз в сутки
    Cache::flush();
})->description('cache-flush-daily')
  ->dailyAt('02:55')
  ->onOneServer();

Schedule::command('cache:warm all --recipes-count=50 --pages=3')
    ->name('cache-warm-daily')
    ->dailyAt('03:00')
    ->onOneServer();

