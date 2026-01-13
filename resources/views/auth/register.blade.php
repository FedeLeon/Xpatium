<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Registro</title>
        <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
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
