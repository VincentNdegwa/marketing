<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HasBusinessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('business.create') || $request->routeIs('business.store') || $request->routeIs('business.index')) {
            return $next($request);
        }

        $user = Auth::user();
        if ($user && $user->businesses()->count() == 0) {
            return redirect()->route('business.create')->with('warning', 'You need to create a business before accessing this page.');
        }
        
        return $next($request);
    }
}
