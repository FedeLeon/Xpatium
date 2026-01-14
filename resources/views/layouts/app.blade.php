<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Xpatium' }}</title>
        <link rel="icon" href="{{ asset('logo2.png') }}" type="image/svg+xml">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        @stack('styles')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        @php
            $role = auth()->user()?->role;
        @endphp
        <div class="app">
            <header>
                <div class="logo">Xpatium</div>
                <div class="header-actions">
                    @if ($role === \App\Models\User::ROLE_STUDENT)
                        <div class="menu-dropdown icon-badge">
                            <button class="icon-btn" type="button" aria-label="Notificaciones">🔔</button>
                            @if (!empty($notificationUnreadCount))
                                <span class="badge">{{ $notificationUnreadCount }}</span>
                            @endif
                            <div class="dropdown">
                                <strong class="dropdown-title">Notificaciones</strong>
                                <a class="dropdown-item" href="{{ route('student.notifications.index') }}">Tienes un nuevo mensaje</a>
                            </div>
                        </div>
                    @else
                        <div class="menu-dropdown icon-badge">
                            <button class="icon-btn" type="button" aria-label="Notificaciones">🔔</button>
                            @if (!empty($notificationUnreadCount))
                                <span class="badge">{{ $notificationUnreadCount }}</span>
                            @endif
                            <div class="dropdown">
                                <strong class="dropdown-title">Notificaciones</strong>
                                @php
                                    $notifications = auth()->user()?->notifications()->latest()->limit(5)->get() ?? collect();
                                @endphp
                                @forelse ($notifications as $notification)
                                    <div class="dropdown-item">
                                        {{ $notification->data['message'] ?? 'Nueva notificacion' }}
                                    </div>
                                @empty
                                    <div class="dropdown-empty">Sin notificaciones</div>
                                @endforelse
                                <form method="POST" action="{{ route('notifications.read') }}">
                                    @csrf
                                    <button type="submit">Marcar como leidas</button>
                                </form>
                            </div>
                        </div>
                    @endif
                    <button class="icon-btn" type="button" id="theme-toggle" aria-label="Cambiar tema" aria-pressed="false">🌙</button>
                    <div class="menu-dropdown">
                        <button class="icon-btn" type="button" aria-label="Perfil">👤</button>
                        <div class="dropdown">
                            @if ($role === \App\Models\User::ROLE_STUDENT)
                                <a href="{{ route('student.profile.edit') }}">Perfil</a>
                            @else
                                <a href="#">Configuracion</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit">Cerrar sesion</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <div class="layout">
                <aside>
                    <div class="menu-title">Menu</div>
                    <nav class="menu">
                        @php
                            $dashboardRoute = match ($role) {
                                \App\Models\User::ROLE_ADMIN => 'admin.dashboard',
                                \App\Models\User::ROLE_TEACHER => 'teacher.dashboard',
                                \App\Models\User::ROLE_STUDENT => 'student.dashboard',
                                default => 'home',
                            };
                        @endphp
                        <a href="{{ route($dashboardRoute) }}">Inicio</a>
                        @if ($role === \App\Models\User::ROLE_TEACHER)
                            <a href="{{ route('teacher.courses.index') }}">Cursos</a>
                            <a href="{{ route('teacher.assets.index') }}">Gestor de contenidos</a>
                            <a href="{{ route('teacher.students.index') }}">Alumnos</a>
                            <a href="{{ route('chat.index') }}">
                                Chat
                                @if (!empty($chatUnreadCount))
                                    <span class="badge">{{ $chatUnreadCount }}</span>
                                @endif
                            </a>
                        @elseif ($role === \App\Models\User::ROLE_STUDENT)
                            <a href="{{ route('student.courses.index') }}">Cursos</a>
                            <a href="{{ route('student.notifications.index') }}">
                                Notificaciones
                                @if (!empty($notificationUnreadCount))
                                    <span class="badge badge-warn">{{ $notificationUnreadCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('student.profile.edit') }}">Perfil</a>
                            <a href="{{ route('chat.index') }}">
                                Chat
                                @if (!empty($chatUnreadCount))
                                    <span class="badge">{{ $chatUnreadCount }}</span>
                                @endif
                            </a>
                        @endif
                    </nav>
                </aside>
                <main>
                    @yield('content')
                </main>
            </div>
        </div>
        <script>
            (function () {
                var root = document.documentElement;
                var stored = localStorage.getItem('theme');
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                var theme = stored || (prefersDark ? 'dark' : 'light');
                root.setAttribute('data-theme', theme);
                var toggle = document.getElementById('theme-toggle');
                if (!toggle) {
                    return;
                }
                var setIcon = function (value) {
                    toggle.textContent = value === 'dark' ? '🌙' : '☀️';
                    toggle.setAttribute('aria-pressed', value === 'dark' ? 'true' : 'false');
                };
                setIcon(theme);
                toggle.addEventListener('click', function () {
                    var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                    root.setAttribute('data-theme', next);
                    localStorage.setItem('theme', next);
                    setIcon(next);
                });
            })();
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var forms = document.querySelectorAll('form[data-confirm]');
                forms.forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        event.preventDefault();
                        var message = form.getAttribute('data-confirm') || 'Confirmar eliminacion?';
                        Swal.fire({
                            title: 'Estas seguro?',
                            text: message,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Si, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then(function (result) {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });
            });
        </script>
    </body>
</html>
