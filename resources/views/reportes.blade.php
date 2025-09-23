<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Asistencia</title>
</head>
<body>
    <h1>Reporte de Asistencia</h1>
    <form action="{{ route('reportes.filter') }}" method="GET">
        <label for="start_date">Desde:</label>
        <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required>
        
        <label for="end_date">Hasta:</label>
        <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required>
        
        <button type="submit">Filtrar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Empleado</th>
                <th>Asistencia</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($asistencias as $asistencia)
                <tr>
                    <td>{{ $asistencia->empleado->nombre }}</td>
                    <td>{{ $asistencia->estado }}</td>
                    <td>{{ $asistencia->fecha }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
