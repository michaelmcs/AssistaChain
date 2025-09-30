@extends('layouts.app')
@section('title', 'Verificación de Blockchain - AssistaChain')
@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h3 class="mb-0"><i class="bi bi-shield-check me-2"></i>Verificación de Integridad Blockchain</h3>
    </div>
    <div class="card-body">
        @if(isset($results['error']))
            <div class="alert alert-danger">
                <strong>Error:</strong> {{ $results['error'] }}
            </div>
        @else
            @if($results['total_blocks'] > 0)
            <div class="alert alert-info">
                <strong>Resumen de verificación:</strong><br>
                - Bloques totales: {{ $results['total_blocks'] }}<br>
                - Bloques válidos: {{ $results['valid_blocks'] }}<br>
                - Bloques inválidos: {{ $results['invalid_blocks'] }}<br>
                - Integridad: 
                @if($results['invalid_blocks'] === 0)
                    <span class="badge bg-success">100% INTACTA</span>
                @else
                    <span class="badge bg-danger">COMPROMETIDA</span>
                @endif
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th># Bloque</th>
                            <th>Empleado</th>
                            <th>Fecha</th>
                            <th>Hash Actual</th>
                            <th>Hash Esperado</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results['blocks'] as $result)
                        <tr>
                            <td>{{ $result['block_id'] }}</td>
                            <td>{{ $result['empleado_nombre'] }}</td>
                            <td>{{ $result['timestamp'] }}</td> 
                            <td><small>{{ Str::limit($result['current_hash'], 10) }}</small></td>
                            <td><small>{{ Str::limit($result['expected_hash'], 10) }}</small></td>
                            <td>
                                @if($result['is_valid'])
                                    <span class="badge bg-success">VÁLIDO</span>
                                @else
                                    <span class="badge bg-danger">INVÁLIDO</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> No hay bloques en la blockchain para verificar.
            </div>
            @endif
        @endif

        <div class="mt-3">
            <a href="{{ route('asistencias.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver a Asistencias
            </a>
            
            @if($results['total_blocks'] > 0)
            <button class="btn btn-info" onclick="window.location.reload()">
                <i class="bi bi-arrow-repeat"></i> Re-verificar
            </button>
            @endif
        </div>
    </div>
</div>
@endsection