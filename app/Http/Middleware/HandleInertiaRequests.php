<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');
        
        $menuItems = [];
        if ($request->user()) {
            // Create a new Menu instance with the current user
            $menu = new \App\Classes\Menu($request->user());
            $user = $request->user();
            
            // Dispatch the appropriate event based on user type
            if ($user->user_type === 'Super Admin') {
                // Log the event for debugging
                \Illuminate\Support\Facades\Log::info('Dispatching SuperAdminMenuEvent');
                event(new \App\Events\SuperAdminMenuEvent($menu));
            } else {
                // Log the event for debugging
                \Illuminate\Support\Facades\Log::info('Dispatching ClientMenuEvent');
                event(new \App\Events\ClientMenuEvent($menu));
            }
            
            // Get the menu items after the events have been processed
            $menuItems = $menu->getItems();
            \Illuminate\Support\Facades\Log::info('Menu items', ['items' => $menuItems]);
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'menu' => $menuItems,
        ];
    }
}
