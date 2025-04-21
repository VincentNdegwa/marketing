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
        // For debugging, let's add all items without restrictions
        $this->items[] = $item;

        // Original logic (commented out for debugging)
        // if (
        //     in_array($item['module'] ?? 'Base', $this->modules) &&
        //     ((empty($item['permission'])) || $this->userHasPermission($item['permission']))
        // ) {
        //     $this->items[] = $item;
        // }
    }

    /**
     * Get all menu items sorted by order
     */
    public function getItems(): array
    {
        // Sort menu items by order
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
        // For now, return all modules as activated
        // In a real implementation, you would check which modules are activated
        $modules = ['Base'];

        // Get modules from modules_statuses.json
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

    /**
     * Check if user has permission
     */
    protected function userHasPermission(string $permission): bool
    {
        // For now, return true for all permissions
        // In a real implementation, you would check if the user has the permission
        return true;
    }
}
