@extends('layouts.app')

@section('title', 'Registrar Asistencia')

@section('content')
<div class="card">
    <div class="card-header bg-success text-white">
        <h3 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Registrar Asistencia de Empleados</h3>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('registrar.asistencia') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="rfid" class="form-label">RFID del Empleado</label>
                <input type="text" class="form-control" id="rfid" name="rfid" required placeholder="Ingresa el RFID del empleado">
            </div>
            <button type="submit" class="btn btn-success">Registrar Asistencia</button>
        </form>
    </div>
</div>
@endsection
