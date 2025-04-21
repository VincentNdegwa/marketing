<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Authentication should be handled by the auth middleware, not here
        // This middleware is just for sharing data with Inertia
        $menuItems = [];
        $user = $request->user();

        if ($user) {
            $menu = new \App\Classes\Menu($user);

            if ($user->isSuperAdmin()) {
                \Illuminate\Support\Facades\Log::info('Dispatching SuperAdminMenuEvent');
                event(new \App\Events\SuperAdminMenuEvent($menu));
            } else {
                \Illuminate\Support\Facades\Log::info('Dispatching ClientMenuEvent');
                event(new \App\Events\ClientMenuEvent($menu));
            }

            // Get the menu items after the events have been processed
            $menuItems = $menu->getItems();
            // \Illuminate\Support\Facades\Log::info('Menu items', ['items' => $menuItems]);
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
                'is_superadmin' => $request->user() ? $request->user()->isSuperAdmin() : false,
            ],
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'menu' => $menuItems,
            'businesses' => $user ? $user->businesses()->get() : [],
            'current_business' => session()->get('current_business_id'),
        ];
    }
}
