<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Registro</title>
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
                gap: 14px;
            }
            label {
                display: block;
                margin-bottom: 6px;
                font-weight: 600;
                font-size: 14px;
            }
            input {
                width: 100%;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                padding: 10px 12px;
                font-size: 14px;
            }
            input:focus {
                outline: 2px solid #2563eb;
                border-color: #2563eb;
            }
            .hint {
                font-size: 12px;
                color: #6b7280;
                margin-top: 6px;
            }
            .error-box {
                background: #fef2f2;
                border: 1px solid #fecaca;
                color: #b91c1c;
                padding: 10px 12px;
                border-radius: 8px;
                font-size: 13px;
            }
            .field-error {
                color: #b91c1c;
                font-size: 12px;
                margin-top: 6px;
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
            <h1>Crear cuenta</h1>
            <p>Completa tus datos para empezar.</p>
            <p>
                Tipo de cuenta:
                <strong>{{ session('register_role') === 'teacher' ? 'Profesor' : 'Alumno' }}</strong>
            </p>

            @if ($errors->any())
                <div class="error-box">
                    <p>Revisa los datos:</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.submit') }}">
                @csrf
                <div>
                    <label for="name">Nombre</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required>
                    <div class="hint">Como queres que te vean tus alumnos.</div>
                    @error('name')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                    <div class="hint">Usa un email al que tengas acceso.</div>
                    @error('email')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="password">Contrasena</label>
                    <input id="password" name="password" type="password" required>
                    <div class="hint">Minimo 8 caracteres.</div>
                    @error('password')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation">Confirmar contrasena</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required>
                    <div class="hint">Repite la misma contrasena.</div>
                </div>
                <button type="submit">Registrarme</button>
            </form>

            <p class="footer-link">
                Ya tenes cuenta?
                <a href="{{ route('login') }}">Iniciar sesion</a>
            </p>
        </main>
    </body>
</html>
