<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Panel de Profesor</title>
    </head>
    <body>
        <main>
            <h1>Panel de profesor</h1>
            <p>Publica cursos, agenda clases y sigue tu progreso.</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Cerrar sesion</button>
            </form>
        </main>
    </body>
</html>
