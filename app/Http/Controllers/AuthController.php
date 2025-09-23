<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (session('authenticated')) {
            return redirect('/dashboard');
        }

        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = Usuario::where('nombre_usuario', $request->username)->first();

        if ($user && Hash::check($request->password, $user->contrasena)) {
            session([
                'authenticated' => true,
                'user_id' => $user->id,
                'user_name' => $user->nombre_usuario,
                'tipo_usuario' => $user->tipo_usuario,
            ]);

            Log::info('Login exitoso', [
                'usuario' => $user->nombre_usuario,
                'tipo_usuario' => $user->tipo_usuario,
                'user_id' => $user->id
            ]);

            return redirect('/dashboard')->with('success', '¡Bienvenido ' . $user->nombre_usuario . '!');
        }

        Log::warning('Intento de login fallido', ['username' => $request->username]);
        return back()->with('error', 'Credenciales incorrectas');
    }

    public function logout()
    {
        $userName = session('user_name');
        
        session()->forget([
            'authenticated', 
            'user_id', 
            'user_name', 
            'tipo_usuario'
        ]);
        session()->flush();

        Log::info('Logout exitoso', ['usuario' => $userName]);
        return redirect('/login')->with('success', 'Sesión cerrada correctamente');
    }
}