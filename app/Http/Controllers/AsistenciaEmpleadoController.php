<?php
namespace App\Http\Controllers;
use App\Models\AsistenciaEmpleado;
use App\Models\Empleado;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
class AsistenciaEmpleadoController extends Controller
{
    public function create()
    {
        return view('asistencias.registrar');
    }

    public function registrarAsistencia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rfid' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        try {
            $empleado = Empleado::where('id_rfid', $request->rfid)->first();

            if (!$empleado) {
                Log::warning('Intento de registro con RFID no encontrado: ' . $request->rfid);
                return redirect()->back()
                    ->with('error', 'Empleado no encontrado. RFID: ' . $request->rfid)
                    ->withInput();
            }

            $asistenciaHoy = AsistenciaEmpleado::where('id_empleado', $empleado->id)
                ->whereDate('fecha', now()->toDateString())
                ->first();

            if ($asistenciaHoy) {
                if ($asistenciaHoy->estado === 'ausente') {
                    $asistenciaHoy->update([
                        'estado' => 'presente',
                        'tipo_registro' => 'manual',
                        'fecha' => now()
                    ]);
                    
                    Log::info('Asistencia cambiada de ausente a presente: ' . $empleado->nombre);
                    return redirect()->back()
                        ->with('success', 'Asistencia actualizada: ' . $empleado->nombre . ' ahora está presente')
                        ->withInput();
                }
                
                Log::info('Asistencia ya registrada hoy para: ' . $empleado->nombre);
                return redirect()->back()
                    ->with('info', $empleado->nombre . ' ya registró asistencia hoy a las ' . 
                        $asistenciaHoy->fecha->format('H:i:s'))
                    ->withInput();
            }
            $prevHash = Configuracion::where('parametro', 'last_block_hash')->value('valor') ?? 'GENESIS';
            $timestamp = now()->timestamp;
            
            $hashBlockchain = hash('sha256', 
                $empleado->id . 
                $empleado->nombre . 
                'presente' . 
                $timestamp . 
                $prevHash .
                uniqid()
            );
            $asistencia = AsistenciaEmpleado::create([
                'id_empleado' => $empleado->id,
                'estado' => 'presente',
                'hash_blockchain' => $hashBlockchain,
                'prev_hash' => $prevHash,
                'fecha' => now(),
                'tipo_registro' => 'manual'
            ]);
            Configuracion::updateOrCreate(
                ['parametro' => 'last_block_hash'],
                ['valor' => $hashBlockchain]
            );
            Log::info('Asistencia registrada con blockchain', [
                'empleado' => $empleado->nombre,
                'rfid' => $empleado->id_rfid,
                'asistencia_id' => $asistencia->id,
                'hash' => $hashBlockchain,
                'prev_hash' => $prevHash
            ]);

            return redirect()->back()
                ->with('success', 'Asistencia registrada correctamente para: ' . $empleado->nombre);

        } catch (\Exception $e) {
            Log::error('Error al registrar asistencia: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al registrar asistencia: ' . $e->getMessage())
                ->withInput();
        }
    }
    public function index()
    {
        try {
            $asistencias = AsistenciaEmpleado::with('empleado')
                ->orderBy('fecha', 'desc')
                ->paginate(20);

            return view('asistencias.index', compact('asistencias'));
        } catch (\Exception $e) {
            Log::error('Error al cargar asistencias: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar las asistencias');
        }
    }
    public function show($id)
    {
        try {
            $asistencias = AsistenciaEmpleado::where('id_empleado', $id)
                ->with('empleado')
                ->orderBy('fecha', 'desc')
                ->get();

            if ($asistencias->isEmpty()) {
                return redirect()->route('asistencias.index')
                    ->with('info', 'No hay registros de asistencia para este empleado');
            }

            return view('asistencias.show', compact('asistencias'));
        } catch (\Exception $e) {
            Log::error('Error al mostrar asistencias: ' . $e->getMessage());
            return redirect()->route('asistencias.index')
                ->with('error', 'Error al cargar las asistencias del empleado');
        }
    }
    public function marcarAusentesAutomaticamente()
    {
        try {
            $hoy = now()->toDateString();
            $horaLimite = '10:00:00';
            
            $empleadosAusentes = Empleado::whereDoesntHave('asistencia', function($query) use ($hoy) {
                $query->whereDate('fecha', $hoy);
            })->get();

            $contador = 0;
            $prevHash = Configuracion::where('parametro', 'last_block_hash')->value('valor') ?? 'GENESIS';
            
            foreach ($empleadosAusentes as $empleado) {
                if (now()->format('H:i:s') > $horaLimite) {
                    $timestamp = now()->timestamp;
                    $hashBlockchain = hash('sha256', 
                        $empleado->id . 
                        $empleado->nombre . 
                        'ausente' . 
                        $timestamp . 
                        $prevHash .
                        uniqid()
                    );
                    AsistenciaEmpleado::create([
                        'id_empleado' => $empleado->id,
                        'estado' => 'ausente',
                        'hash_blockchain' => $hashBlockchain,
                        'prev_hash' => $prevHash,
                        'fecha' => now()->setTime(23, 59, 59),
                        'tipo_registro' => 'automatico'
                    ]);

                    $prevHash = $hashBlockchain;
                    $contador++;
                    Log::info('Empleado marcado como ausente: ' . $empleado->nombre);
                }
            }
            if ($contador > 0) {
                Configuracion::updateOrCreate(
                    ['parametro' => 'last_block_hash'],
                    ['valor' => $prevHash]
                );
            }

            return response()->json([
                'message' => 'Ausentes marcados: ' . $contador,
                'ausentes' => $contador
            ]);
        } catch (\Exception $e) {
            Log::error('Error al marcar ausentes: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function marcarAusenteManualmente($empleadoId)
    {
        try {
            $empleado = Empleado::findOrFail($empleadoId);
            $hoy = now()->toDateString();
            $asistenciaHoy = AsistenciaEmpleado::where('id_empleado', $empleado->id)
                ->whereDate('fecha', $hoy)
                ->first();
            if ($asistenciaHoy) {
                if ($asistenciaHoy->estado === 'ausente') {
                    return redirect()->back()
                        ->with('info', $empleado->nombre . ' ya está marcado como ausente hoy');
                }
                
                $asistenciaHoy->update([
                    'estado' => 'ausente',
                    'tipo_registro' => 'manual'
                ]);
                return redirect()->back()
                    ->with('success', $empleado->nombre . ' cambiado a ausente');
            }
            $prevHash = Configuracion::where('parametro', 'last_block_hash')->value('valor') ?? 'GENESIS';
            $timestamp = now()->timestamp;
            
            $hashBlockchain = hash('sha256', 
                $empleado->id . 
                $empleado->nombre . 
                'ausente' . 
                $timestamp . 
                $prevHash .
                uniqid()
            );
            AsistenciaEmpleado::create([
                'id_empleado' => $empleado->id,
                'estado' => 'ausente',
                'hash_blockchain' => $hashBlockchain,
                'prev_hash' => $prevHash,
                'fecha' => now(),
                'tipo_registro' => 'manual'
            ]);
            Configuracion::updateOrCreate(
                ['parametro' => 'last_block_hash'],
                ['valor' => $hashBlockchain]
            );
            return redirect()->back()
                ->with('success', $empleado->nombre . ' marcado como ausente');

        } catch (\Exception $e) {
            Log::error('Error al marcar ausente manual: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al marcar como ausente');
        }
    }
}