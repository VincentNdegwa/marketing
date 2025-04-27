<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BusinessPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission, ?int $businessId = null): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // If businessId is not provided, try to get it from the session
        if (! $businessId) {
            $businessId = getCurrentBusinessId();
        }

        // If still no businessId, check if user has the permission in any business
        if (! $businessId) {
            if ($user->isSuperAdmin() || $user->hasPermission($permission)) {
                return $next($request);
            }
        } else {
            // Check if user has the permission in the specific business
            if ($user->isSuperAdmin() || $user->hasPermissionInBusiness($permission, $businessId)) {
                return $next($request);
            }
        }

        return abort(403, 'Unauthorized action.');
    }
}
