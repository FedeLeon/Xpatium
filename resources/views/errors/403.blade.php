<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Acceso denegado</title>
        <link rel="stylesheet" href="{{ asset('css/simple.css') }}">
    </head>
    <body>
        <main>
            <h1>No tenes permisos</h1>
            <p>Tu cuenta no tiene acceso a esta seccion.</p>
            <a class="btn btn-secondary" href="{{ route('home') }}">Volver al inicio</a>
        </main>
    </body>
</html>
