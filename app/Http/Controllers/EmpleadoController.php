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
    public function index()
    {
        try {
            $empleados = Empleado::orderBy('id', 'desc')->get();
            $siguienteRFID = $this->calcularSiguienteRFID();
            $totalEmpleados = $empleados->count();
            $totalAsistenciasHoy = AsistenciaEmpleado::whereDate('fecha', Carbon::today())->count();
            
            return view('empleados.index', compact('empleados', 'siguienteRFID', 'totalEmpleados', 'totalAsistenciasHoy'));
        } catch (\Exception $e) {
            Log::error('Error al obtener empleados: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar la lista de empleados: ' . $e->getMessage());
        }
    }
    private function calcularSiguienteRFID()
    {
        try {
            $ultimoEmpleado = Empleado::orderBy('id', 'desc')->first();
            
            if (!$ultimoEmpleado || !$ultimoEmpleado->id_rfid) {
                return 'RFID001'; 
            }
            preg_match('/RFID(\d+)/', $ultimoEmpleado->id_rfid, $matches);
            
            if (count($matches) > 1) {
                $numero = intval($matches[1]) + 1;
                return 'RFID' . str_pad($numero, 3, '0', STR_PAD_LEFT);
            }
            return 'RFID001'; 
        } catch (\Exception $e) {
            Log::error('Error al calcular siguiente RFID: ' . $e->getMessage());
            return 'RFID001'; 
        }
    }
    public function create()
    {
        $siguienteRFID = $this->calcularSiguienteRFID();
        return view('empleados.create', compact('siguienteRFID'));
    }
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
    private function registrarPrimeraAsistencia($empleadoId)
    {
        try {
            $hashBlockchain = hash('sha256', $empleadoId . 'first_attendance' . now());
            
            AsistenciaEmpleado::create([
                'id_empleado' => $empleadoId,
                'estado' => 'presente',
                'hash_blockchain' => $hashBlockchain,
                'fecha' => now(),
            ]);
            
            Log::info('Asistencia inicial registrada para empleado ID: ' . $empleadoId);
        } catch (\Exception $e) {
            Log::error('Error al registrar asistencia inicial: ' . $e->getMessage());
            throw $e;
        }
    }

public function show($id)
{
    try {
        $empleado = Empleado::findOrFail($id);
        $asistencias = AsistenciaEmpleado::where('id_empleado', $id)
            ->orderBy('fecha', 'desc')
            ->paginate(10);
        $totalAsistencias = AsistenciaEmpleado::where('id_empleado', $id)->count();
        $asistenciasPresente = AsistenciaEmpleado::where('id_empleado', $id)
            ->where('estado', 'presente')->count();
        $asistenciasAusente = AsistenciaEmpleado::where('id_empleado', $id)
            ->where('estado', 'ausente')->count();
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
    private function obtenerEstadisticasMensuales($empleadoId)
    {
        return AsistenciaEmpleado::select(
            DB::raw('YEAR(fecha) as year'), 
            DB::raw('MONTH(fecha) as month'),
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
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $empleado = Empleado::findOrFail($id);
            $nombre = $empleado->nombre;
            $rfid = $empleado->id_rfid;
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
            $ultimaAsistencia = AsistenciaEmpleado::where('id_empleado', $empleado->id)
                ->orderBy('fecha', 'desc')
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
    public function reporteAsistencias($id, Request $request)
    {
        try {
            $empleado = Empleado::findOrFail($id);
            
            $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subMonth()->format('Y-m-d'));
            $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));
            
            $asistencias = AsistenciaEmpleado::where('id_empleado', $id)
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->orderBy('fecha', 'desc') 
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
    public function dashboard()
    {
        try {
            $totalEmpleados = Empleado::count();
            $totalAsistenciasHoy = AsistenciaEmpleado::whereDate('fecha', Carbon::today())->count();
            $totalAsistenciasMes = AsistenciaEmpleado::whereMonth('fecha', Carbon::now()->month)->count();
            $empleadosRecientes = Empleado::orderBy('created_at', 'desc')->take(5)->get();
            $asistenciasRecientes = AsistenciaEmpleado::with('empleado')
                ->orderBy('fecha', 'desc')
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