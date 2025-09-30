@extends('layouts.app')
@section('title', 'Gestión de Empleados')
@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h3 class="mb-0"><i class="bi bi-people-fill me-2"></i>Gestión de Empleados</h3>
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
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">Lista de Empleados</h5>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
                <i class="bi bi-plus-circle me-1"></i>Nuevo Empleado
            </button>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>RFID</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($empleados as $empleado)
                    <tr>
                        <td>{{ $empleado->id }}</td>
                        <td>{{ $empleado->nombre }}</td>
                        <td>
                            <span class="badge bg-info">{{ $empleado->id_rfid }}</span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('empleados.show', $empleado->id) }}" 
                                   class="btn btn-info btn-sm" title="Ver detalles">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                                <form action="{{ route('empleados.marcar-ausente', $empleado->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm" 
                                            onclick="return confirm('¿Marcar a {{ $empleado->nombre }} como AUSENTE hoy?')"
                                            title="Marcar como ausente">
                                        <i class="bi bi-x-circle"></i> Ausente
                                    </button>
                                </form>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editEmployeeModal{{ $empleado->id }}">
                                    <i class="bi bi-pencil"></i> Editar
                                </button>

                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteEmployeeModal{{ $empleado->id }}">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">No hay empleados registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Crear Empleado -->
<div class="modal fade" id="createEmployeeModal" tabindex="-1" aria-labelledby="createEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('empleados.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createEmployeeModalLabel">Nuevo Empleado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="id_rfid" class="form-label">RFID</label>
                        <input type="text" name="id_rfid" id="id_rfid" class="form-control" 
                               value="{{ $siguienteRFID ?? 'RFID001' }}" required>
                        <small class="form-text text-muted">Sistema sugiere: {{ $siguienteRFID ?? 'RFID001' }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Empleado</button>
                </div>
            </div>
        </form>
    </div>
</div>
@foreach($empleados as $empleado)
<div class="modal fade" id="editEmployeeModal{{ $empleado->id }}" tabindex="-1" aria-labelledby="editEmployeeModalLabel{{ $empleado->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('empleados.update', $empleado->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEmployeeModalLabel{{ $empleado->id }}">Editar Empleado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" value="{{ $empleado->nombre }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="id_rfid" class="form-label">RFID</label>
                        <input type="text" name="id_rfid" id="id_rfid" class="form-control" value="{{ $empleado->id_rfid }}" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Actualizar Empleado</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="deleteEmployeeModal{{ $empleado->id }}" tabindex="-1" aria-labelledby="deleteEmployeeModalLabel{{ $empleado->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('empleados.destroy', $empleado->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteEmployeeModalLabel{{ $empleado->id }}">Eliminar Empleado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar al empleado {{ $empleado->nombre }}?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection