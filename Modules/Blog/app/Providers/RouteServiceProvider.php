<?php

namespace Modules\Blog\app\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'Blog';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     */
    public function boot(): void
    {
        parent::boot();

        $this->mapApiRoutes();
        $this->mapWebRoutes();
        
        // // Make sure the module has access to all routes from the main application
        // // This ensures that routes like 'dashboard' are available in the Blog module
        // $this->app->booted(function () {
        //     // Get the Ziggy config from the main application
        //     $ziggy = config('ziggy', []);
            
        //     // Ensure the Ziggy config includes all routes
        //     if (isset($ziggy['except'])) {
        //         unset($ziggy['except']);
        //     }
            
        //     // Update the Ziggy config
        //     config(['ziggy' => $ziggy]);
        // });
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')->group(module_path($this->name, '/routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes(): void
    {
        Route::middleware('api')->prefix('api')->name('api.')->group(module_path($this->name, '/routes/api.php'));
    }
}
