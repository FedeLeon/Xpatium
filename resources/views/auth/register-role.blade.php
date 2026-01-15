<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Tipo de cuenta</title>
        <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <main>
            <h1>Tipo de cuenta</h1>
            <p>Antes de registrarte, contanos si vas a dar clases o estudiar.</p>

            @if ($errors->any())
                <div class="error-box">
                    <p>Selecciona una opcion para continuar.</p>
                </div>
            @endif

            <form method="POST" action="{{ route('register.role') }}">
                @csrf
                <label class="option">
                    <input type="radio" name="role" value="teacher" required>
                    <span>
                        <strong>Profesor</strong>
                        Crea cursos, dicta clases y gestiona alumnos.
                    </span>
                </label>
                <label class="option">
                    <input type="radio" name="role" value="student" required>
                    <span>
                        <strong>Alumno</strong>
                        Accede a cursos, clases en vivo y materiales.
                    </span>
                </label>
                <button type="submit">Continuar</button>
            </form>

            <p class="footer-link">
                Ya tenes cuenta?
                <a href="{{ route('login') }}">Iniciar sesion</a>
            </p>
        </main>
        @include('partials.alerts')
    </body>
</html>
