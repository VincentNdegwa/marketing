<?php

namespace Modules\Blog\app\Listeners;

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
        $module = 'Blog';
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
            'title' => 'Blog',
            'icon' => 'FileText',
            'name' => 'blog',
            'parent' => null,
            'order' => 40,
            'ignore_if' => [],
            'depend_on' => [],
            'href' => 'blog.index',
            'module' => $module,
            'permission' => 'blog.manage',
        ]);
    }

    /**
     * Register menu items for admin users
     */
    private function registerAdminMenu($menu, $module): void {}

    /**
     * Register default menu items
     */
    private function registerDefaultMenu($menu, $module): void {}
}
