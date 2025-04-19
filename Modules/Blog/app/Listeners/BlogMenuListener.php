<?php

namespace Modules\Blog\app\Listeners;

use App\Events\MenuEvent;
use App\Events\ClientMenuEvent;
use Illuminate\Support\Facades\Log;

class BlogMenuListener
{
    /**
     * Handle menu events.
     */
    public function handle(MenuEvent $event): void
    {
        $menu = $event->menu;
        $module = 'Blog';
        
        Log::info("Blog Menu", ["event"=> $event]);
        
        if ($event instanceof ClientMenuEvent) {
            $menu->add([
                'title' => 'Blogs',
                'icon' => 'FileText',
                'href' => 'blog.index',
                'order' => 40, 
                'module' => $module,
            ]);
            $menu->add([
                'title' => 'Blog Categories',
                'icon' => 'FolderTree',
                'href' => 'blogs-category.index',
                'order' => 45,
                'module' => $module,
            ]);
            
            $menu->add([
                'title' => 'Blog Trend',
                'icon' => 'Settings',
                'href' => 'trends.index',
                'order' => 50,
                'module' => $module,
            ]);
        }
    }
}
