<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Skip if user is not authenticated
        if (!$user) {
            return $next($request);
        }
        
        // Skip if 2FA is not enabled for this user
        if (!$user->hasTwoFactorEnabled()) {
            return $next($request);
        }
        
        // Skip if already verified in this session
        if (session('2fa_verified')) {
            return $next($request);
        }
        
        // Skip 2FA routes to avoid redirect loops
        if ($request->routeIs(['two-factor.*', 'logout'])) {
            return $next($request);
        }
        
        // Require 2FA verification
        session(['2fa_required' => true]);
        return redirect()->route('two-factor.challenge');
    }
}