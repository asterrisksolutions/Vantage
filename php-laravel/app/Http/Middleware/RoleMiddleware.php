<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Role Middleware
 *
 * Middleware to check if the authenticated user has a specific role.
 * Usage: ->middleware('role:Admin') or ->middleware('role:Admin,Manager')
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string ...$roles The roles allowed to access the route
     * @return Response
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return redirect('/login');
        }

        // Get the user's role name
        $userRole = $request->user()->role->name ?? null;

        // Check if user has one of the required roles
        if (!in_array($userRole, $roles)) {
            // User doesn't have the required role
            // Redirect based on their actual role
            return match ($userRole) {
                'Admin' => redirect()->route('admin.dashboard'),
                'Manager' => redirect()->route('manager.dashboard'),
                default => redirect()->route('user.landing'),
            };
        }

        return $next($request);
    }
}