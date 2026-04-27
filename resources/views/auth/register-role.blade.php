<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Tipo de cuenta</title>
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
                        <h1>Tipo de cuenta</h1>
                        <p class="auth__subtitle">Antes de registrarte, contanos si vas a liderar o participar en la comunidad.</p>
                    </div>

                    @if ($errors->any())
                        <div class="error-box">
                            <p>Selecciona una opcion para continuar.</p>
                        </div>
                    @endif

                    <form class="auth__form" method="POST" action="{{ route('register.role') }}">
                        @csrf
                        <label class="auth__option">
                            <input type="radio" name="role" value="teacher" required>
                            <span>
                                <strong>Lider</strong>
                                Crea contenidos de ayuda, organiza encuentros y gestiona usuarios.
                            </span>
                        </label>
                        <label class="auth__option">
                            <input type="radio" name="role" value="student" required>
                            <span>
                                <strong>Usuario</strong>
                                Accede a contenidos de ayuda, encuentros en vivo y materiales.
                            </span>
                        </label>
                        <div class="auth__actions">
                            <button class="btn btn--solid-pink" type="submit">Continuar</button>
                            <a class="btn btn--ghost" href="{{ route('login') }}">Iniciar sesion</a>
                        </div>
                    </form>

                    <p class="auth__footer">
                        Ya tenes cuenta?
                        <a href="{{ route('login') }}">Iniciar sesion</a>
                    </p>
                </div>
            </div>
        </section>
        @include('landing.blocks.footer')
        @include('partials.alerts')
    </body>
</html>
