<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DebugAuth
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('DebugAuth - URL: ' . $request->url());
        Log::info('DebugAuth - Authenticated: ' . (Auth::check() ? 'Yes' : 'No'));
        
        if (Auth::check()) {
            Log::info('DebugAuth - User: ' . Auth::user()->nombre_usuario);
        }
        
        return $next($request);
    }
}