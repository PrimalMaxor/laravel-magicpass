<?php

namespace Primalmaxor\MagicPass\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class BypassPasswordAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        Config::set('auth.guards.web.driver', 'magicpass');
        
        if (Auth::guard('web')->check()) {
            return $next($request);
        }

        return $next($request);
    }
} 