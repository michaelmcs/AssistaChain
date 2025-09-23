<?php

namespace App\Http\Controllers;

use App\Models\RegistroActividad;
use Illuminate\Http\Request;

class RegistroActividadController extends Controller
{
    public function store(Request $request)
    {
        $registro = RegistroActividad::create([
            'id_usuario' => $request->id_usuario,
            'accion' => $request->accion,
        ]);

        return response()->json($registro);
    }

    public function index()
    {
        return RegistroActividad::all();
    }
}
