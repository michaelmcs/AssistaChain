@extends('layouts.app')
@section('title', 'Detalles del Empleado - ' . $empleado->nombre)
@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h3 class="mb-0"><i class="bi bi-person-circle me-2"></i>Detalles del Empleado</h3>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Información del Empleado</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>ID:</strong> {{ $empleado->id }}</p>
                        <p><strong>Nombre:</strong> {{ $empleado->nombre }}</p>
                        <p><strong>RFID:</strong> <span class="badge bg-secondary">{{ $empleado->id_rfid }}</span></p>
                        <p><strong>Fecha de Registro:</strong> 
                            {{ $empleado->created_at ? $empleado->created_at->format('d/m/Y H:i:s') : 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Estadísticas de Asistencia</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="row">
                            <div class="col-4">
                                <div class="bg-primary text-white p-3 rounded">
                                    <h4 class="mb-0">{{ $totalAsistencias }}</h4>
                                    <small>Total Registros</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-success text-white p-3 rounded">
                                    <h4 class="mb-0">{{ $asistenciasPresente }}</h4>
                                    <small>Presente</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-warning text-dark p-3 rounded">
                                    <h4 class="mb-0">{{ $asistenciasAusente }}</h4>
                                    <small>Ausente</small>
                                </div>
                            </div>
                        </div>
                        @if($totalAsistencias > 0)
                        <div class="mt-3">
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $porcentajeAsistencia }}%">
                                    {{ $porcentajeAsistencia }}% Asistencia
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historial de Asistencias</h5>
            </div>
            <div class="card-body">
                @if($asistencias->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha y Hora</th>
                                    <th>Estado</th>
                                    <th>Hash Blockchain</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($asistencias as $asistencia)
                                <tr>
                                    <td>
                                        {{ $asistencia->fecha ? \Carbon\Carbon::parse($asistencia->fecha)->format('d/m/Y H:i:s') : 'N/A' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $asistencia->estado == 'presente' ? 'success' : 'warning' }}">
                                            {{ ucfirst($asistencia->estado) }}
                                        </span>
                                    </td>
                                    <td class="text-truncate" style="max-width: 200px;" title="{{ $asistencia->hash_blockchain }}">
                                        {{ $asistencia->hash_blockchain ? substr($asistencia->hash_blockchain, 0, 20) . '...' : 'N/A' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginación -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $asistencias->links() }}
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle me-2"></i>
                        No hay registros de asistencia para este empleado.
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('empleados.index') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left me-1"></i> Volver a la lista
            </a>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .badge {
        font-family: monospace;
    }
    .card {
        margin-bottom: 1rem;
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
    .progress {
        height: 20px;
    }
</style>
@endsection