<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Session Timeout Middleware
 *
 * Middleware to handle session timeout functionality.
 * Tracks user activity and redirects to login when session expires.
 * 
 * Configuration:
 * - SESSION_LIFETIME in config/session.php (default: 120 minutes)
 * - SESSION_TIMEOUT_WARNING in .env (minutes before expiry to show warning)
 */
class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check timeout for authenticated users
        if ($request->user()) {
            $lifetime = config('session.lifetime', 120);
            $lastActivity = $request->session()->get('last_activity_time');
            
            // Check if session has timed out
            if ($lastActivity && (time() - $lastActivity > ($lifetime * 60))) {
                // Session has expired - logout user and redirect to login
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect('/login')->with('error', 'Your session has expired. Please log in again.');
            }
            
            // Update last activity time
            $request->session()->put('last_activity_time', time());
        }
        
        return $next($request);
    }
}