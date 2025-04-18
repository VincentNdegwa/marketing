<?php

namespace App\Providers;

use Inertia\Inertia;
use App\Classes\Menu;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('app.menu', function ($app) {
            return new Menu($app['auth']->user());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register event listeners for menu events
        Event::listen(\App\Events\MenuEvent::class, \App\Listeners\BaseMenuListener::class);
        Event::listen(\App\Events\SuperAdminMenuEvent::class, \App\Listeners\BaseMenuListener::class);
        Event::listen(\App\Events\ClientMenuEvent::class, \App\Listeners\BaseMenuListener::class);
        
        // Register the Blog module's menu listener
        Event::listen(\App\Events\MenuEvent::class, \Modules\Blog\app\Listeners\BlogMenuListener::class);
        Event::listen(\App\Events\ClientMenuEvent::class, \Modules\Blog\app\Listeners\BlogMenuListener::class);
    }
}
