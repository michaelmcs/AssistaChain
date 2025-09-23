@extends('layouts.app')

@section('title', 'Asistencias del Empleado')

@section('content')
<div class="card">
    <div class="card-header bg-info text-white">
        <h3 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Asistencias de {{ $asistencias->first()->empleado->nombre }}</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th>Tipo Registro</th>
                        <th>Hash Blockchain</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($asistencias as $a)
                    <tr>
                        <td>{{ $a->id }}</td>
                        <td>{{ \Carbon\Carbon::parse($a->fecha)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($a->fecha)->format('H:i:s') }}</td>
                        <td>
                            @if($a->estado === 'presente')
                                <span class="badge bg-success">Presente</span>
                            @else
                                <span class="badge bg-danger">Ausente</span>
                            @endif
                        </td>
                        <td>
                            @if($a->tipo_registro === 'automatico')
                                <span class="badge bg-secondary">Automático</span>
                            @else
                                <span class="badge bg-primary">Manual</span>
                            @endif
                        </td>
                        <td><small>{{ Str::limit($a->hash_blockchain, 15) }}</small></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <a href="{{ route('asistencias.index') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left"></i> Volver al listado
        </a>
    </div>
</div>
@endsection