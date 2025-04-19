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
            $module = 'Blog';
            $menu = $event->menu;
            $menu->add([
                'title' => 'Blogs',
                'icon' => 'FileText',
                'name' => 'blogs',
                'parent' => null,
                'order' => 40,
                'ignore_if' => [],
                'depend_on' => [],
                'href' => null,
                'module' => $module,
                'permission' => 'manage-blogs'
            ]);
            $menu->add([
                'title' => 'Blogs',
                'icon' => 'FileText',
                'name' => 'blogs-manage',
                'parent' => 'blogs',
                'order' => 40,
                'ignore_if' => [],
                'depend_on' => [],
                'href' => 'blog.index',
                'module' => $module,
                'permission' => 'manage-blogs'
            ]);
            $menu->add([
                'title' => 'Blog Categories',
                'icon' => 'FolderTree',
                'name' => 'blog-categories',
                'parent' => 'blogs',
                'order' => 45,
                'ignore_if' => [],
                'depend_on' => [],
                'href' => 'blogs-category.index',
                'module' => $module,
                'permission' => 'manage-blog-categories'
            ]);
            
            $menu->add([
                'title' => 'Blog Trend',
                'icon' => 'Settings',
                'name' => 'blog-trends',
                'parent' => 'blogs',
                'order' => 50,
                'ignore_if' => [],
                'depend_on' => [],
                'href' => 'trends.index',
                'module' => $module,
                'permission' => 'manage-blog-trends'
            ]);
        }
    }
}
