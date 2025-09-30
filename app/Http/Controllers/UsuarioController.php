<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');
        if (Auth::attempt(['nombre_usuario' => $credentials['username'], 'password' => $credentials['password']])) {
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'message' => 'Las credenciales no son correctas.',
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
