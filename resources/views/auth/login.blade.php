<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Iniciar sesion</title>
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('landing/assets/img/favicon.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('landing/assets/img/favicon.png') }}">
        <link rel="shortcut icon" href="{{ asset('landing/assets/img/favicon.png') }}">
        <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('landing/assets/css/landing.css') }}">
        <link rel="stylesheet" href="{{ asset('landing/assets/css/auth.css') }}">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body class="theme-two">
        @include('landing.blocks.nav')
        <section class="sec sec--violet auth">
            <div class="wrap auth__wrap">
                <div class="auth__card">
                    <div class="auth__header">
                        <h1>Iniciar sesion</h1>
                        <p class="auth__subtitle">Ingresa con tu email y tu contrasena.</p>
                    </div>

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

                    <form class="auth__form" method="POST" action="{{ route('login.submit') }}">
                        @csrf
                        <div class="auth__field">
                            <label for="email">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">
                            <div class="hint">Ejemplo: nombre@correo.com</div>
                            @error('email')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="auth__field">
                            <label for="password">Contrasena</label>
                            <input id="password" name="password" type="password" required autocomplete="current-password">
                            <div class="hint">Minimo 8 caracteres.</div>
                            @error('password')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="auth__actions">
                            <button class="btn btn--solid-pink" type="submit">Entrar</button>
                            <a class="btn btn--ghost" href="{{ route('register') }}">Crear cuenta</a>
                        </div>
                    </form>

                    <p class="auth__footer">
                        No tenes cuenta?
                        <a href="{{ route('register') }}">Registrate</a>
                    </p>
                </div>
            </div>
        </section>
        @include('landing.blocks.footer')
        @include('partials.alerts')
    </body>
</html>
