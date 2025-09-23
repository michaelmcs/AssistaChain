<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar si la sesión está autenticada
        if (!session('authenticated')) {
            return redirect('/login')->with('error', 'Debes iniciar sesión');
        }

        return $next($request);
    }
}
