@extends('layouts.app')
@section('title', 'Listado de Asistencias')
@section('content')
<div class="card">
    <div class="card-header bg-info text-white">
        <h3 class="mb-0"><i class="bi bi-list-check me-2"></i>Registros de Asistencia</h3>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="mb-3">
            <a href="{{ route('registrar.asistencia.form') }}" class="btn btn-success">
                <i class="bi bi-plus-circle me-1"></i>Registrar nueva asistencia
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Empleado</th>
                        <th>RFID</th>
                        <th>Estado</th>
                        <th>Fecha y Hora</th>
                        <th>Tipo Registro</th>
                        <th>Hash Blockchain</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asistencias as $a)
                    <tr>
                        <td>{{ $a->id }}</td>
                        <td>{{ optional($a->empleado)->nombre }}</td>
                        <td>{{ optional($a->empleado)->id_rfid }}</td>
                        <td>
                            @if($a->estado === 'presente')
                                <span class="badge bg-success">Presente</span>
                            @else
                                <span class="badge bg-danger">Ausente</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($a->fecha)->format('d/m/Y H:i:s') }}</td>
                        <td>
                            @if($a->tipo_registro === 'automatico')
                                <span class="badge bg-secondary">Automático</span>
                            @else
                                <span class="badge bg-primary">Manual</span>
                            @endif
                        </td>
                        <td>
                            <small title="Hash actual: {{ $a->hash_blockchain }}">
                                {{ Str::limit($a->hash_blockchain, 15) }}
                            </small>
                            <br>
                            <small class="text-muted" title="Hash anterior: {{ $a->prev_hash }}">
                                Prev: {{ $a->prev_hash ? Str::limit($a->prev_hash, 10) : 'GENESIS' }}
                            </small>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#blockModal{{ $a->id }}">
                                <i class="bi bi-info-circle"></i> Detalles
                            </button>
                        </td>
                    </tr>

                    <!-- Modal para detalles del bloque -->
                    <div class="modal fade" id="blockModal{{ $a->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Detalles del Bloque #{{ $a->id }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Empleado:</strong> {{ optional($a->empleado)->nombre }}<br>
                                            <strong>RFID:</strong> {{ optional($a->empleado)->id_rfid }}<br>
                                            <strong>Estado:</strong> 
                                            @if($a->estado === 'presente')
                                                <span class="badge bg-success">Presente</span>
                                            @else
                                                <span class="badge bg-danger">Ausente</span>
                                            @endif<br>
                                            <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($a->fecha)->format('d/m/Y H:i:s') }}<br>
                                            <strong>Tipo Registro:</strong> 
                                            @if($a->tipo_registro === 'automatico')
                                                <span class="badge bg-secondary">Automático</span>
                                            @else
                                                <span class="badge bg-primary">Manual</span>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Hash Blockchain:</strong><br>
                                            <code class="small">{{ $a->hash_blockchain }}</code><br><br>
                                            <strong>Hash Anterior:</strong><br>
                                            <code class="small">{{ $a->prev_hash ?? 'GENESIS' }}</code><br><br>
                                            <strong>ID del Bloque:</strong> {{ $a->id }}<br>
                                            <strong>ID Empleado:</strong> {{ $a->id_empleado }}
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No hay registros de asistencia</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $asistencias->links() }}
    </div>
</div>
@endsection