<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Error del sistema</title>
        <link rel="stylesheet" href="{{ asset('css/simple.css') }}">
    </head>
    <body>
        <main>
            <h1>Ocurrio un error</h1>
            <p>Estamos revisando el problema. Intenta nuevamente mas tarde.</p>
            <a class="btn btn-secondary" href="{{ route('home') }}">Volver al inicio</a>
        </main>
    </body>
</html>
