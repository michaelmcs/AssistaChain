<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\AsistenciaEmpleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $empleados = Empleado::orderBy('id', 'desc')->get();
            
            // Calcular el siguiente RFID disponible
            $siguienteRFID = $this->calcularSiguienteRFID();
            
            // Estadísticas generales - USANDO EL CAMPO 'fecha' EN LUGAR DE 'created_at'
            $totalEmpleados = $empleados->count();
            $totalAsistenciasHoy = AsistenciaEmpleado::whereDate('fecha', Carbon::today())->count();
            
            return view('empleados.index', compact('empleados', 'siguienteRFID', 'totalEmpleados', 'totalAsistenciasHoy'));
        } catch (\Exception $e) {
            Log::error('Error al obtener empleados: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar la lista de empleados: ' . $e->getMessage());
        }
    }

    /**
     * Método para calcular el siguiente RFID disponible
     */
    private function calcularSiguienteRFID()
    {
        try {
            $ultimoEmpleado = Empleado::orderBy('id', 'desc')->first();
            
            if (!$ultimoEmpleado || !$ultimoEmpleado->id_rfid) {
                return 'RFID001'; // Primer empleado
            }
            
            // Extraer el número del último RFID
            preg_match('/RFID(\d+)/', $ultimoEmpleado->id_rfid, $matches);
            
            if (count($matches) > 1) {
                $numero = intval($matches[1]) + 1;
                return 'RFID' . str_pad($numero, 3, '0', STR_PAD_LEFT);
            }
            
            return 'RFID001'; // Si el formato no coincide
            
        } catch (\Exception $e) {
            Log::error('Error al calcular siguiente RFID: ' . $e->getMessage());
            return 'RFID001'; // Valor por defecto
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $siguienteRFID = $this->calcularSiguienteRFID();
        return view('empleados.create', compact('siguienteRFID'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|min:3',
            'id_rfid' => 'required|string|max:255|unique:empleados,id_rfid|regex:/^RFID\d{3}$/',
        ], [
            'id_rfid.regex' => 'El formato del RFID debe ser RFID seguido de 3 números (ej: RFID001)',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Por favor corrige los errores del formulario');
        }

        try {
            DB::beginTransaction();

            $empleado = Empleado::create($request->only('nombre', 'id_rfid'));

            // Registrar automáticamente la primera asistencia (opcional)
            $this->registrarPrimeraAsistencia($empleado->id);

            DB::commit();

            Log::info('Empleado creado: ' . $request->nombre . ' - RFID: ' . $request->id_rfid);
            return redirect()->route('empleados.index')
                ->with('success', 'Empleado creado correctamente y asistencia inicial registrada');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear empleado: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al crear el empleado: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Registrar primera asistencia automáticamente
     */
    private function registrarPrimeraAsistencia($empleadoId)
    {
        try {
            $hashBlockchain = hash('sha256', $empleadoId . 'first_attendance' . now());
            
            AsistenciaEmpleado::create([
                'id_empleado' => $empleadoId,
                'estado' => 'presente',
                'hash_blockchain' => $hashBlockchain,
                'fecha' => now(), // USANDO EL CAMPO CORRECTO
            ]);
            
            Log::info('Asistencia inicial registrada para empleado ID: ' . $empleadoId);
        } catch (\Exception $e) {
            Log::error('Error al registrar asistencia inicial: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */

    // En el método show() del controlador
public function show($id)
{
    try {
        $empleado = Empleado::findOrFail($id);
        
        // Obtener las asistencias del empleado ordenadas por fecha
        $asistencias = AsistenciaEmpleado::where('id_empleado', $id)
            ->orderBy('fecha', 'desc')
            ->paginate(10);
            
        // Calcular estadísticas generales
        $totalAsistencias = AsistenciaEmpleado::where('id_empleado', $id)->count();
        $asistenciasPresente = AsistenciaEmpleado::where('id_empleado', $id)
            ->where('estado', 'presente')->count();
        $asistenciasAusente = AsistenciaEmpleado::where('id_empleado', $id)
            ->where('estado', 'ausente')->count();
        
        // Porcentaje de asistencia
        $porcentajeAsistencia = $totalAsistencias > 0 ? 
            round(($asistenciasPresente / $totalAsistencias) * 100, 2) : 0;

        return view('empleados.show', compact(
            'empleado', 
            'asistencias',
            'totalAsistencias',
            'asistenciasPresente',
            'asistenciasAusente',
            'porcentajeAsistencia'
        ));
        
    } catch (\Exception $e) {
        Log::error('Error al mostrar empleado: ' . $e->getMessage());
        return redirect()->route('empleados.index')
            ->with('error', 'Empleado no encontrado: ' . $e->getMessage());
    }
}

    /**
     * Obtener estadísticas mensuales del empleado - CORREGIDO PARA USAR 'fecha'
     */
    private function obtenerEstadisticasMensuales($empleadoId)
    {
        return AsistenciaEmpleado::select(
            DB::raw('YEAR(fecha) as year'), // CAMBIO AQUÍ
            DB::raw('MONTH(fecha) as month'), // CAMBIO AQUÍ
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN estado = "presente" THEN 1 ELSE 0 END) as presentes'),
            DB::raw('SUM(CASE WHEN estado = "ausente" THEN 1 ELSE 0 END) as ausentes')
        )
        ->where('id_empleado', $empleadoId)
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $empleado = Empleado::findOrFail($id);
            return view('empleados.edit', compact('empleado'));
        } catch (\Exception $e) {
            Log::error('Error al editar empleado: ' . $e->getMessage());
            return redirect()->route('empleados.index')
                ->with('error', 'Empleado no encontrado');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|min:3',
            'id_rfid' => 'required|string|max:255|unique:empleados,id_rfid,' . $id . '|regex:/^RFID\d{3}$/',
        ], [
            'id_rfid.regex' => 'El formato del RFID debe ser RFID seguido de 3 números (ej: RFID001)',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $empleado = Empleado::findOrFail($id);
            $empleado->update($request->only('nombre', 'id_rfid'));

            DB::commit();

            Log::info('Empleado actualizado: ' . $request->nombre . ' - RFID: ' . $request->id_rfid);
            return redirect()->route('empleados.index')
                ->with('success', 'Empleado actualizado correctamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar empleado: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al actualizar el empleado: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $empleado = Empleado::findOrFail($id);
            $nombre = $empleado->nombre;
            $rfid = $empleado->id_rfid;
            
            // Eliminar también las asistencias relacionadas
            AsistenciaEmpleado::where('id_empleado', $id)->delete();
            
            $empleado->delete();

            DB::commit();

            Log::info('Empleado eliminado: ' . $nombre . ' - RFID: ' . $rfid);
            return redirect()->route('empleados.index')
                ->with('success', 'Empleado y sus registros de asistencia eliminados correctamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar empleado: ' . $e->getMessage());
            return redirect()->route('empleados.index')
                ->with('error', 'Error al eliminar el empleado: ' . $e->getMessage());
        }
    }

    /**
     * Método para buscar empleado por RFID (para API)
     */
    public function buscarPorRFID(Request $request)
    {
        $request->validate([
            'rfid' => 'required|string|max:255'
        ]);

        try {
            $empleado = Empleado::where('id_rfid', $request->rfid)->first();

            if (!$empleado) {
                return response()->json([
                    'success' => false,
                    'message' => 'Empleado no encontrado'
                ], 404);
            }

            // Obtener última asistencia - USANDO EL CAMPO 'fecha'
            $ultimaAsistencia = AsistenciaEmpleado::where('id_empleado', $empleado->id)
                ->orderBy('fecha', 'desc') // CAMBIO AQUÍ
                ->first();

            return response()->json([
                'success' => true,
                'empleado' => $empleado,
                'ultima_asistencia' => $ultimaAsistencia
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al buscar empleado por RFID: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Método para obtener reporte de asistencias por rango de fechas - CORREGIDO
     */
    public function reporteAsistencias($id, Request $request)
    {
        try {
            $empleado = Empleado::findOrFail($id);
            
            $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subMonth()->format('Y-m-d'));
            $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));
            
            $asistencias = AsistenciaEmpleado::where('id_empleado', $id)
                ->whereBetween('fecha', [$fechaInicio, $fechaFin]) // CAMBIO AQUÍ
                ->orderBy('fecha', 'desc') // CAMBIO AQUÍ
                ->get();
                
            return response()->json([
                'success' => true,
                'empleado' => $empleado,
                'asistencias' => $asistencias,
                'rango_fechas' => [
                    'inicio' => $fechaInicio,
                    'fin' => $fechaFin
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al generar reporte: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el reporte'
            ], 500);
        }
    }

    /**
     * Método para dashboard/resumen del sistema - CORREGIDO
     */
    public function dashboard()
    {
        try {
            $totalEmpleados = Empleado::count();
            $totalAsistenciasHoy = AsistenciaEmpleado::whereDate('fecha', Carbon::today())->count(); // CORREGIDO
            $totalAsistenciasMes = AsistenciaEmpleado::whereMonth('fecha', Carbon::now()->month)->count(); // CORREGIDO
            
            $empleadosRecientes = Empleado::orderBy('created_at', 'desc')->take(5)->get();
            $asistenciasRecientes = AsistenciaEmpleado::with('empleado')
                ->orderBy('fecha', 'desc') // CORREGIDO
                ->take(10)
                ->get();
                
            return response()->json([
                'success' => true,
                'estadisticas' => [
                    'total_empleados' => $totalEmpleados,
                    'asistencias_hoy' => $totalAsistenciasHoy,
                    'asistencias_mes' => $totalAsistenciasMes
                ],
                'empleados_recientes' => $empleadosRecientes,
                'asistencias_recientes' => $asistenciasRecientes
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en dashboard: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar el dashboard'
            ], 500);
        }
    }
}