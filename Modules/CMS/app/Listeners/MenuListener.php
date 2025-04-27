<?php

namespace Modules\CMS\app\Listeners;

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
        $module = 'CMS';
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
        // Main CMS menu item
        $menu->add([
            'title' => 'CMS',
            'icon' => 'FileText',
            'name' => 'cms',
            'parent' => null,
            'order' => 40,
            'ignore_if' => [],
            'depend_on' => [],
            'href' => null,
            'module' => $module,
            'permission' => 'cms.view'
        ]);
        
        // Pages submenu
        $menu->add([
            'title' => 'Pages',
            'icon' => 'File',
            'name' => 'cms.pages',
            'parent' => 'cms',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'href' => 'cms.pages.index',
            'module' => $module,
            'permission' => 'cms.pages.view'
        ]);
        
        // Templates submenu
        $menu->add([
            'title' => 'Templates',
            'icon' => 'Copy',
            'name' => 'cms.templates',
            'parent' => 'cms',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'href' => 'cms.templates.index',
            'module' => $module,
            'permission' => 'cms.templates.view'
        ]);
        
        // Media Library submenu
        $menu->add([
            'title' => 'Media Library',
            'icon' => 'Image',
            'name' => 'cms.media',
            'parent' => 'cms',
            'order' => 30,
            'ignore_if' => [],
            'depend_on' => [],
            'href' => 'cms.media.index',
            'module' => $module,
            'permission' => 'cms.media.view'
        ]);
        
        // Settings submenu
        $menu->add([
            'title' => 'CMS Settings',
            'icon' => 'Settings',
            'name' => 'cms.settings',
            'parent' => 'cms',
            'order' => 40,
            'ignore_if' => [],
            'depend_on' => [],
            'href' => 'cms.settings.index',
            'module' => $module,
            'permission' => 'cms.settings.view'
        ]);
    }
    
    /**
     * Register menu items for admin users
     */
    private function registerAdminMenu($menu, $module): void
    {
        // Use the same menu structure for admin users
    }
    
    /**
     * Register default menu items
     */
    private function registerDefaultMenu($menu, $module): void
    {
        // Use the same menu structure for default users
    }
}
