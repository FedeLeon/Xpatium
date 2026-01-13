<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Xpatium</title>
        <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    </head>
    <body>
        <main>
            <h1>Bienvenido a Xpatium</h1>
            <p>Tu academia online para cursos y clases en vivo.</p>
            <div class="actions">
                <a class="btn" href="{{ route('register') }}">Registrarse</a>
                <a class="btn btn-secondary" href="{{ route('login') }}">Iniciar sesion</a>
            </div>
        </main>
    </body>
</html>
