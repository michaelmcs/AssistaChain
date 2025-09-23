<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AssistaChain - Registro de Asistencia</title>
</head>
<body>
    <header>
        <h1>Bienvenido a AssistaChain</h1>
    </header>
    <nav>
        <ul>
            <li><a href="{{ url('/empleados') }}">Empleados</a></li>
            <li><a href="{{ url('/registrar-asistencia') }}">Registrar Asistencia</a></li>
            <li><a href="{{ url('/asistencias') }}">Reportes de Asistencia</a></li>
        </ul>
    </nav>
    <main>
        <p>Gestión de asistencia de empleados con verificación Blockchain y visualización en tiempo real.</p>
    </main>
</body>
</html>
