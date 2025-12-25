<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * This middleware allows access to users with any admin role
     * (any role except 'tutor' and 'student')
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If not logged in, redirect to login
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Check if user has any admin role (not tutor or student)
        if ($request->user()->hasAnyAdminRole()) {
            return $next($request);
        }

        abort(403, 'Unauthorized access. Admin role required.');
    }
}
