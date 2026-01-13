<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Panel de Alumno</title>
    </head>
    <body>
        <main>
            <h1>Panel de alumno</h1>
            <p>Accede a tus cursos, clases en vivo y materiales.</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Cerrar sesion</button>
            </form>
        </main>
    </body>
</html>
