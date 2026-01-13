<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Tipo de cuenta</title>
        <style>
            :root {
                color-scheme: light;
            }
            body {
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                background: #f6f7fb;
                color: #1f2937;
            }
            main {
                max-width: 420px;
                margin: 48px auto;
                background: #ffffff;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                padding: 24px;
                box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            }
            h1 {
                margin: 0 0 6px;
                font-size: 24px;
            }
            p {
                margin: 0 0 16px;
                color: #6b7280;
            }
            form {
                display: grid;
                gap: 12px;
            }
            .option {
                border: 1px solid #d1d5db;
                border-radius: 10px;
                padding: 12px;
                display: flex;
                gap: 10px;
                align-items: flex-start;
            }
            .option input {
                margin-top: 4px;
            }
            .option strong {
                display: block;
            }
            .error-box {
                background: #fef2f2;
                border: 1px solid #fecaca;
                color: #b91c1c;
                padding: 10px 12px;
                border-radius: 8px;
                font-size: 13px;
            }
            button {
                border: 0;
                border-radius: 8px;
                padding: 12px 14px;
                background: #2563eb;
                color: #ffffff;
                font-weight: 600;
                cursor: pointer;
            }
            button:hover {
                background: #1d4ed8;
            }
            .footer-link {
                margin-top: 16px;
                font-size: 14px;
            }
            a {
                color: #2563eb;
                text-decoration: none;
            }
            a:hover {
                text-decoration: underline;
            }
        </style>
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
    </body>
</html>
