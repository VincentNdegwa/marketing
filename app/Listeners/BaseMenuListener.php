<?php

namespace App\Listeners;

use App\Events\ClientMenuEvent;
use App\Events\MenuEvent;
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

        Log::info('Base Menu', ['event' => $event]);

        // Add client specific menu items
        if ($event instanceof ClientMenuEvent) {
            $module = 'Base';
            $menu = $event->menu;
            $menu->add([
                'title' => 'Dashboard',
                'icon' => 'LayoutGrid',
                'name' => 'dashboard',
                'parent' => null,
                'order' => 10,
                'ignore_if' => [],
                'depend_on' => null,
                'href' => 'dashboard',
                'module' => $module,
                'permission' => 'manage-dashboard',
            ]);
        }

        // Add super admin specific menu items
        if ($event instanceof SuperAdminMenuEvent) {
            $module = 'Base';
            $menu = $event->menu;
            $menu->add([
                'title' => 'Admin Dashboard',
                'icon' => 'LayoutGrid',
                'name' => 'admin-dashboard',
                'parent' => null,
                'order' => 1,
                'ignore_if' => [],
                'depend_on' => [],
                'href' => 'dashboard.admin',
                'module' => $module,
                'permission' => 'manage-admin-dashboard',
            ]);
            $menu->add([
                'title' => 'Clients',
                'icon' => 'Users',
                'name' => 'clients',
                'parent' => null,
                'order' => 20,
                'ignore_if' => [],
                'depend_on' => [],
                'href' => 'clients.index',
                'module' => $module,
                'permission' => 'manage-clients',
            ]);

            $menu->add([
                'title' => 'Modules',
                'icon' => 'Boxes',
                'name' => 'modules',
                'parent' => null,
                'order' => 30,
                'ignore_if' => [],
                'depend_on' => [],
                'href' => 'admin.modules',
                'module' => $module,
                'permission' => 'manage-modules',
            ]);
        }
    }
}
