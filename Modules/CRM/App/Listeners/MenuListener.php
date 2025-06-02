<?php

namespace Modules\CRM\App\Listeners;

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
        $module = 'CRM';
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
        // Main CRM menu
        $menu->add([
            'title' => 'CRM',
            'icon' => 'FileText',
            'name' => 'crm',
            'parent' => null,
            'order' => 45,
            'ignore_if' => [],
            'depend_on' => [],
            'href' => null,
            'module' => $module,
            'permission' => 'crm.view'
        ]);
        
        // Contacts submenu
        $menu->add([
            'title' => 'Contacts',
            'icon' => 'FileText',
            'name' => 'crm.contacts',
            'parent' => 'crm',
            'order' => 1,
            'ignore_if' => [],
            'depend_on' => [],
            'href' => 'crm.contacts.index',
            'module' => $module,
            'permission' => 'crm.contacts.view'
        ]);
        
        // Companies submenu
        $menu->add([
            'title' => 'Companies',
            'icon' => null,
            'name' => 'crm.companies',
            'parent' => 'crm',
            'order' => 2,
            'ignore_if' => [],
            'depend_on' => [],
            'href' => 'crm.companies.index',
            'module' => $module,
            'permission' => 'crm.companies.view'
        ]);
        
        // Deals submenu
        $menu->add([
            'title'=> 'Deals',
            'icon' => null,
            'name' => 'crm.deals',
            'parent' => 'crm',
            'order' => 3,
            'ignore_if' => [],
            'depend_on' => [],
            'href' => 'crm.deals.index',
            'module' => $module,
            'permission' => 'crm.deals.view'
        ]);
        
        // Activities submenu
        $menu->add([
            'title'=> 'Activities',
            'icon' => null,
            'name' => 'crm.activities',
            'parent' => 'crm',
            'order' => 4,
            'ignore_if' => [],
            'depend_on' => [],
            'href' => 'crm.activities.index',
            'module' => $module,
            'permission' => 'crm.activities.view'
        ]);
        
        // Tasks submenu
        $menu->add([
            'title'=> 'Tasks',
            'icon' => null,
            'name' => 'crm.tasks',
            'parent' => 'crm',
            'order' => 5,
            'ignore_if' => [],
            'depend_on' => [],
            'href' => 'crm.tasks.index',
            'module' => $module,
            'permission' => 'crm.tasks.view'
        ]);
        
        // Notes submenu
        $menu->add([
            'title'=> 'Notes',
            'icon' => null,
            'name' => 'crm.notes',
            'parent' => 'crm',
            'order' => 6,
            'ignore_if' => [],
            'depend_on' => [],
            'href' => 'crm.notes.index',
            'module' => $module,
            'permission' => 'crm.notes.view'
        ]);
        
        // Reports submenu
        // $menu->add([
        //     'title'=> 'Reports',
        //     'icon' => null,
        //     'name' => 'crm.reports',
        //     'parent' => 'crm',
        //     'order' => 7,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'href' => 'crm.reports.index',
        //     'module' => $module,
        //     'permission' => 'crm.reports.view'
        // ]);
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
