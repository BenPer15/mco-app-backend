<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

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
        Event::listen(
            \App\Domains\Tracking\Events\ActivityRecorded::class,
            \App\Domains\Gamification\Listeners\HandleActivityRecorded::class,
        );

        Event::listen(
            \App\Domains\Tracking\Events\WeightRecorded::class,
            \App\Domains\Gamification\Listeners\HandleWeightRecorded::class,
        );
    }
}
