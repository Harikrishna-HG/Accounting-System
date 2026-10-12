<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DashboardAuth
{
    /**
     * Ensure the request is authenticated before reaching the dashboard.
     *
     * The "auth" middleware already covers this, but this guard is kept so the
     * redirect target and flash message stay under the application's control.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to access the dashboard.');
        }

        return $next($request);
    }
}
