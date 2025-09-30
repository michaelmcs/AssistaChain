@extends('layouts.app')
@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Dashboard Principal</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>¡Bienvenido, {{ session('user_name') }}!</h5>
                        <p class="mb-0">Sistema de gestión de asistencia con blockchain</p>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5>Gestión de Empleados</h5>
                                    <p>Administra el registro de empleados</p>
                                    <a href="{{ route('empleados.index') }}" class="btn btn-primary">Acceder</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5>Registrar Asistencia</h5>
                                    <p>Registro diario de asistencia</p>
                                    <a href="{{ route('registrar.asistencia') }}" class="btn btn-success">Acceder</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5>Ver Asistencias</h5>
                                    <p>Consulta históricos de asistencia</p>
                                    <a href="{{ route('asistencias.index') }}" class="btn btn-info">Acceder</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
