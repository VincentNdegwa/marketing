<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If user is not authenticated, redirect to login
        if (! $user) {
            return redirect()->route('login');
        }

        // If user is authenticated but not a superadmin, show 403
        if (! $user->hasRole('superadmin')) {
            abort(403, 'Unauthorized. Super Admin access required.');
        }

        return $next($request);
    }
}
