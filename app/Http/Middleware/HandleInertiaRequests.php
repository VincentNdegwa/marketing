<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
            'permissions' => $user ? $this->getUserPermissions($user) : [],
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
                'warning' => session('warning'),
                'info' => session('info'),
            ],
        ];
    }
    
    /**
     * Get user permissions for the current business context
     *
     * @param \App\Models\User $user
     * @return array
     */
    protected function getUserPermissions($user): array
    {
        if (!$user) {
            return [];
        }
        
        $businessId = session()->get('current_business_id');        
        if (!$businessId && $user->defaultBusiness()) {
            $businessId = $user->defaultBusiness()->id;
        }
        $isSuperAdmin = $user->isSuperAdmin();
        if ($isSuperAdmin) {
            $allPermissions = Permission::pluck('name')->toArray();
            return [
                'list' => $allPermissions,
                'is_business_admin' => true,
                'is_superadmin' => true,
            ];
        }
        $directPermissions = $user->permissions;
        $rolePermissions = collect();
        foreach ($user->roles as $role) {
            if ($role->name === 'admin' || $role->name === 'superadmin') {
                $rolePermissions = $rolePermissions->merge($role->permissions);
            }
            else if ($role->business_id === null || ($businessId && $role->business_id == $businessId)) {
                $rolePermissions = $rolePermissions->merge($role->permissions);
            }
        }
        $allUserPermissions = $directPermissions->merge($rolePermissions)
            ->unique('id')
            ->pluck('name')
            ->toArray();
            
        $isBusinessAdmin = $user->roles->contains(function ($role) {
            return $role->name === 'admin';
        });
        
        return [
            'list' => $allUserPermissions,
            'is_business_admin' => $isBusinessAdmin,
            'is_superadmin' => $isSuperAdmin,
        ];
    }
}
