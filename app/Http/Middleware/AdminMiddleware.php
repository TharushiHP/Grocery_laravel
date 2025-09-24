<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect('/admin/login')->with('error', 'Please login as admin to access this area.');
        }
        
        // Check if authenticated user is admin
        if (!auth()->user()->isAdmin()) {
            // If regular user is trying to access admin area, logout and redirect
            auth()->logout();
            return redirect('/admin/login')->with('error', 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
