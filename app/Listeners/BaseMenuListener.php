<?php

namespace App\Listeners;

use App\Events\MenuEvent;
use App\Events\ClientMenuEvent;
use App\Events\SuperAdminMenuEvent;
use Illuminate\Support\Facades\Log;

class BaseMenuListener
{
    /**
     * Handle menu events.
     */
    public function handle(MenuEvent $event): void
    {
        $menu = $event->menu;

        Log::info("Base Menu", ["event" => $event]);

        
        
        // Add client specific menu items
        if ($event instanceof ClientMenuEvent) {
            $menu->add([
                'title' => 'Dashboard',
                'icon' => 'LayoutGrid',
                'href' => 'dashboard',
                'order' => 10,
                'module' => 'Base',
            ]);
            $menu->add([
                'title' => 'My Profile',
                'icon' => 'User',
                'href' => 'profile.edit',
                'order' => 50,
                'module' => 'Base',
            ]);
        }

        // Add super admin specific menu items
        if ($event instanceof SuperAdminMenuEvent) {
            $menu->add([
                'title' => 'Admin Dashboard',
                'icon' => 'LayoutGrid',
                'href' => 'dashboard.admin',
                'order' => 1,
                'module' => 'Base',
            ]);
            $menu->add([
                'title' => 'Clients',
                'icon' => 'Users',
                'href' => 'clients.index',
                'order' => 20,
                'module' => 'Base',
            ]);

            $menu->add([
                'title' => 'Modules',
                'icon' => 'Boxes',
                'href' => 'dashboard.admin',
                'order' => 30,
                'module' => 'Base',
            ]);
        }
    }
}
