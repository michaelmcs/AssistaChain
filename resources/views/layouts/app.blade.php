<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AssistaChain - @yield('title', 'Sistema de Gestión')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .nav-link {
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: #3498db !important;
            transform: translateY(-1px);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .card-header {
            border-radius: 15px 15px 0 0 !important;
        }
        .badge {
            font-size: 0.85em;
        }
        footer {
            margin-top: auto;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    @if(session('authenticated'))
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-shield-lock-fill me-2"></i>AssistaChain
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('empleados.index') }}">
                            <i class="bi bi-people-fill me-1"></i>Empleados
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('registrar.asistencia.form') }}">
                            <i class="bi bi-calendar-check me-1"></i>Registrar Asistencia
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('asistencias.index') }}">
                            <i class="bi bi-list-check me-1"></i>Ver Asistencias
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('blockchain.verify') }}">
                            <i class="bi bi-shield-check me-1"></i>Verificar Blockchain
                        </a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center">
                    <span class="text-light me-3">
                        <i class="bi bi-person-circle me-1"></i>{{ session('user_name') }}
                    </span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-box-arrow-right me-1"></i>Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endif

    <main class="container py-4 flex-grow-1">
        @yield('content')
    </main>

    <footer class="mt-auto py-3 bg-dark text-light text-center">
        <div class="container">
            <small>
                AssistaChain © 2025 - Sistema protegido por derechos de autor<br>
                Prohibida la reproducción total o parcial sin autorización
            </small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
</body>
</html>