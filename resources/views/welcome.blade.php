<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>CristoApp</title>
        <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    </head>
    <body>
        <main>
            <h1>Bienvenido a CristoApp</h1>
            <p>Tu plataforma para contenidos de ayuda, encuentros y acompanamiento en comunidad.</p>
            @auth
                @php
                    $role = auth()->user()?->role;
                    $dashboardRoute = match ($role) {
                        \App\Models\User::ROLE_ADMIN => 'admin.dashboard',
                        \App\Models\User::ROLE_TEACHER => 'teacher.dashboard',
                        \App\Models\User::ROLE_STUDENT => 'student.dashboard',
                        default => 'home',
                    };
                @endphp
                <div class="actions">
                    <a class="btn" href="{{ route($dashboardRoute) }}">Ir al panel</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-secondary" type="submit">Cerrar sesion</button>
                    </form>
                </div>
            @endauth

            @guest
                <div class="actions">
                    <a class="btn" href="/registro">Registrarse</a>
                    <a class="btn btn-secondary" href="/login">Iniciar sesion</a>
                </div>
            @endguest
        </main>
    </body>
</html>
