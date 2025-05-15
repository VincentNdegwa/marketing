<?php

namespace Modules\Marketing\App\Listeners;

use App\Events\MenuEvent;
use App\Events\ClientMenuEvent;
use App\Events\SuperAdminMenuEvent;
use Illuminate\Support\Facades\Log;

class MenuListener
{
    /**
     * Handle menu events.
     */
    public function handle(MenuEvent $event): void
    {
        $module = 'Marketing';
        $menu = $event->menu;
                
        // Handle different menu event types
        if ($event instanceof ClientMenuEvent) {
            $this->registerClientMenu($menu, $module);
        } elseif ($event instanceof SuperAdminMenuEvent) {
            $this->registerAdminMenu($menu, $module);
        } else {
            // Default menu for other event types
            $this->registerDefaultMenu($menu, $module);
        }
    }
    
    /**
     * Register menu items for client users
     */
    private function registerClientMenu($menu, $module): void
    {
        $menu->add([
            'title' => 'Marketing',
            'icon' => 'AudioWaveform',
            'name' => 'marketing',
            'parent' => null,
            'order' => 40,
            'ignore_if' => [],
            'depend_on' => [],
            'href' => null,
            'module' => $module,
            'permission' => 'marketing.manage'
        ]);

        $menu->add([
            'title' => 'Facebook',
            'icon' => 'Facebook',
            'name' => 'marketing.facebook',
            'parent' => 'marketing',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'href' => 'marketing.facebook.index', 
            'module' => $module,
            'permission' => 'facebook_ads.view'
        ]);
        $menu->add([
            'title' => 'Google',
            'icon' => 'Google',
            'name' => 'marketing.google',
            'parent' => 'marketing',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'href' => 'marketing.facebook.index', //marketing.google.index
            'module' => $module,
            'permission' => 'google_ads.view'
        ]);
    }
    
    
    /**
     * Register menu items for admin users
     */
    private function registerAdminMenu($menu, $module): void
    {

    }
    
    /**
     * Register default menu items
     */
    private function registerDefaultMenu($menu, $module): void
    {

    }
}
