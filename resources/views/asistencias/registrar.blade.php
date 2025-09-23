@extends('layouts.app')

@section('title', 'Registrar Asistencia')

@section('content')
<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">Registrar Asistencia</h5>
    </div>
    <div class="card-body">
        <!-- Mensajes de éxito -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Mensajes de error -->
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Mostrar errores de validación -->
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Información de diagnóstico -->
        <div class="alert alert-info">
            <strong>Ruta actual:</strong> {{ Route::currentRouteName() }}<br>
            <strong>Action del formulario:</strong> {{ route('registrar.asistencia') }}
        </div>

        <form method="POST" action="{{ route('registrar.asistencia') }}" id="formAsistencia">
            @csrf
            <div class="mb-3">
                <label for="rfid" class="form-label">RFID del Empleado</label>
                <input type="text" class="form-control" id="rfid" name="rfid" 
                       required autofocus placeholder="Ingrese el código RFID"
                       value="{{ old('rfid') }}">
                @error('rfid') 
                    <small class="text-danger">{{ $message }}</small> 
                @enderror
            </div>
            
            <div class="d-grid gap-2 d-md-flex">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="bi bi-check-circle"></i> Registrar Asistencia
                </button>
                <a href="{{ route('asistencias.index') }}" class="btn btn-secondary">
                    <i class="bi bi-list"></i> Ver Todas las Asistencias
                </a>
                <button type="button" class="btn btn-info" onclick="iniciarCamara()">
                    <i class="bi bi-camera"></i> Escanear RFID
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para la cámara -->
<div class="modal fade" id="modalCamara" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Escanear RFID</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="lector" style="width: 100%; height: 300px;"></div>
                <div class="mt-3">
                    <p class="text-muted">Apunte la cámara hacia el código RFID</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Incluir Html5QrcodeScanner -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/minified/html5-qrcode.min.js"></script>

<script>
// Función para iniciar la cámara
function iniciarCamara() {
    $('#modalCamara').modal('show');
    
    // Esperar a que el modal se muestre completamente
    setTimeout(() => {
        const scanner = new Html5QrcodeScanner("lector", { 
            fps: 10, 
            qrbox: { width: 250, height: 250 } 
        });

        scanner.render((decodedText, decodedResult) => {
            // Cuando se escanea un código, llenar el campo y cerrar el modal
            $('#rfid').val(decodedText);
            $('#modalCamara').modal('hide');
            scanner.clear();
            
            // Opcional: enviar automáticamente el formulario
            // $('#formAsistencia').submit();
        }, (error) => {
            // console.log('Error al escanear:', error);
        });
    }, 500);
}

// Cerrar el scanner cuando se cierra el modal
$('#modalCamara').on('hidden.bs.modal', function () {
    const scannerElement = document.getElementById('lector');
    if (scannerElement) {
        scannerElement.innerHTML = '';
    }
});

// Enfocar automáticamente el campo RFID
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('rfid').focus();
});

// Prevenir envío múltiple del formulario
document.getElementById('formAsistencia').addEventListener('submit', function(e) {
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat spinner"></i> Registrando...';
});
</script>

<style>
.spinner {
    animation: spin 1s linear infinite;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
@endsection