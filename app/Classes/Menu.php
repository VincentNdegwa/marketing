<?php

namespace App\Classes;

class Menu
{
    public $items = [];

    public $user;

    public $modules;

    public function __construct($user = null)
    {
        $this->user = $user;
        $this->modules = $this->getActivatedModules();
    }

    /**
     * Add a menu item to the collection
     */
    public function add(array $item): void
    {
        $module = $item['module'] ?? 'Base';
        
        if ($module === 'Base') {
            if (in_array($module, $this->modules)) {
                $this->items[] = $item;
            }
            return;
        }        
        if (
            in_array($module, $this->modules) &&
            ((empty($item['permission'])) || $this->userHasPermission($item['permission']))
        ) {
            $this->items[] = $item;
        }
    }

    /**
     * Get all menu items sorted by order
     */
    public function getItems(): array
    {
        usort($this->items, function ($a, $b) {
            return ($a['order'] ?? 999) <=> ($b['order'] ?? 999);
        });

        return $this->items;
    }

    /**
     * Get activated modules
     */
    protected function getActivatedModules(): array
    {
        $modules = ['Base'];

        $modulesStatusesPath = base_path('modules_statuses.json');
        if (file_exists($modulesStatusesPath)) {
            $modulesStatuses = json_decode(file_get_contents($modulesStatusesPath), true);
            foreach ($modulesStatuses as $module => $status) {
                if ($status) {
                    $modules[] = $module;
                }
            }
        }

        return $modules;
    }

    protected function userHasPermission(string $permission): bool
    {
        return $this->user->hasPermission($permission);
    }
}
