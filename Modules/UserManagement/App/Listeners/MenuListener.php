<?php

namespace Modules\UserManagement\App\Listeners;

use App\Events\ClientMenuEvent;
use App\Events\MenuEvent;
use App\Events\SuperAdminMenuEvent;

class MenuListener
{
    /**
     * Handle menu events.
     */
    public function handle(MenuEvent $event): void
    {
        $module = 'UserManagement';
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
            'title' => 'User Management',
            'icon' => 'Users',
            'name' => 'usermanagement',
            'parent' => null,
            'order' => 5,
            'ignore_if' => [],
            'depend_on' => [],
            'href' => null,
            'module' => $module,
            'permission' => 'usermanagement.view',
        ]);
        $menu->add([
            'title' => 'User',
            'icon' => 'User',
            'name' => 'users',
            'parent' => 'usermanagement',
            'order' => 1,
            'ignore_if' => [],
            'depend_on' => [],
            'href' => 'usermanagement.index',
            'module' => $module,
            'permission' => 'usermanagement.view',
        ]);
        $menu->add([
            'title' => 'Roles',
            'icon' => 'KeyRound',
            'name' => 'roles',
            'parent' => 'usermanagement',
            'order' => 2,
            'ignore_if' => [],
            'depend_on' => [],
            'href' => 'user-role.index',
            'module' => $module,
            'permission' => 'role.view',
        ]);

    }

    private function registerAdminMenu($menu, $module): void {}

    private function registerDefaultMenu($menu, $module): void {}
}
