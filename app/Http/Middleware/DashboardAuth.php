<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DashboardAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if (!auth()->check()) {
        //     return redirect()->route('login')->with('error', 'You must be logged in to access the dashboard.');
        // }
        if(!auth()->check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to access the dashboard.');
        }
        // var_dump(auth()->check());

        // echo "bikram dhami ji ";
        // echo ["user"=>auth()->user()];
        return $next($request);
    }
}
