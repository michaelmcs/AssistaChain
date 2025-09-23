<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function store(Request $request)
    {
        $configuracion = Configuracion::create([
            'parametro' => $request->parametro,
            'valor' => $request->valor,
        ]);

        return response()->json($configuracion);
    }

    public function index()
    {
        return Configuracion::all();
    }
}
