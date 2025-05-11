<?php

namespace Modules\GoogleSocialite\App\Providers;

use App\Events\ClientMenuEvent;
use App\Events\MenuEvent;
use App\Events\SuperAdminMenuEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [];
    
    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }
    
    
    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}
