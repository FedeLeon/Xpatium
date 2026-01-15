<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>No encontrado</title>
        <link rel="stylesheet" href="{{ asset('css/simple.css') }}">
    </head>
    <body>
        <main>
            <h1>Pagina no encontrada</h1>
            <p>El recurso que buscas no existe o fue movido.</p>
            <a class="btn btn-secondary" href="{{ route('home') }}">Volver al inicio</a>
        </main>
    </body>
</html>
