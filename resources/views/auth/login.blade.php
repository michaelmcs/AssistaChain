<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AssistaChain</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            border-bottom: none;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .logo-text {
            color: white;
            font-weight: bold;
            font-size: 18px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
            border: none;
            border-radius: 25px;
            padding: 12px;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .form-control {
            border-radius: 10px;
            padding: 12px;
            border: 2px solid #e9ecef;
        }
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card login-card">
                    <div class="card-header text-center py-4">
                        <div class="logo-container">
                            <div class="logo">
                                <span class="logo-text">AC</span>
                            </div>
                        </div>
                        <h4 class="mb-0">AssistaChain</h4>
                        <p class="mb-0 opacity-75"></p>
                    </div>

                    <div class="card-body p-5">
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="username" class="form-label fw-bold">
                                    <i class="bi bi-person-fill me-2"></i>Usuario
                                </label>
                                <input type="text" name="username" id="username" class="form-control" 
                                       required autofocus placeholder="Ingresa tu usuario">
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label fw-bold">
                                    <i class="bi bi-lock-fill me-2"></i>Contraseña
                                </label>
                                <input type="password" name="password" id="password" class="form-control" 
                                       required placeholder="Ingresa tu contraseña">
                            </div>

                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                    <i class="bi bi-box-arrow-in-right me-2"></i> Iniciar sesión
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p class="text-muted small">
                                <i class="bi bi-info-circle me-1"></i>
                                Sistema seguro de gestión de asistencia con tecnología blockchain
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-eliminar alerts después de 5 segundos
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(alert => {
                new bootstrap.Alert(alert).close();
            });
        }, 5000);
    </script>
</body>
</html>