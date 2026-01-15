<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Iniciar sesion</title>
        <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <main>
            <h1>Iniciar sesion</h1>
            <p>Ingresa con tu email y tu contrasena.</p>

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

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div>
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">
                    <div class="hint">Ejemplo: nombre@correo.com</div>
                    @error('email')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="password">Contrasena</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password">
                    <div class="hint">Minimo 8 caracteres.</div>
                    @error('password')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit">Entrar</button>
            </form>

            <p class="footer-link">
                No tenes cuenta?
                <a href="{{ route('register') }}">Registrate</a>
            </p>
        </main>
        @include('partials.alerts')
    </body>
</html>
