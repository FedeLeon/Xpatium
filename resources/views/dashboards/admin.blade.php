<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Panel de Admin</title>
    </head>
    <body>
        <main>
            <h1>Panel de admin</h1>
            <p>Gestiona profesores, alumnos y cursos desde aqui.</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Cerrar sesion</button>
            </form>
        </main>
    </body>
</html>
