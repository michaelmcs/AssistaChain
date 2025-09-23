<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle($request, Closure $next, $role)
    {
        // Verificar si está autenticado via sesión
        if (session('authenticated')) {
            // Obtener el usuario actual de la base de datos
            $user = \App\Models\Usuario::find(session('user_id'));
            
            if ($user && $user->tipo_usuario == $role) {
                return $next($request);
            }
        }

        return redirect('/dashboard')->with('error', 'Acceso no autorizado');
    }
}