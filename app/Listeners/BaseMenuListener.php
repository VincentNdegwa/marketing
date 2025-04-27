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
        if ($event instanceof ClientMenuEvent) {
            $module = 'Base';
            $menu = $event->menu;
            $menu->add([
                'title' => 'Dashboard',
                'icon' => 'LayoutGrid',
                'name' => 'dashboard',
                'parent' => null,
                'order' => 4,
                'ignore_if' => [],
                'depend_on' => null,
                'href' => 'dashboard',
                'module' => $module,
                'permission' => null,
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
                'permission' => 'admin-dashboard.manage',
            ]);
            $menu->add([
                'title' => 'Clients',
                'icon' => 'Users',
                'name' => 'clients',
                'parent' => null,
                'order' => 2,
                'ignore_if' => [],
                'depend_on' => [],
                'href' => 'clients.index',
                'module' => $module,
                'permission' => 'clients.view',
            ]);

            $menu->add([
                'title' => 'Modules',
                'icon' => 'Boxes',
                'name' => 'modules',
                'parent' => null,
                'order' => 3,
                'ignore_if' => [],
                'depend_on' => [],
                'href' => 'admin.modules',
                'module' => $module,
                'permission' => 'modulse.view',
            ]);
        }
    }
}
