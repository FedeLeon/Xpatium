<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? 'CristoApp' }}</title>
        <link rel="icon" href="{{ asset('logo2.png') }}" type="image/svg+xml">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        @stack('styles')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @vite('resources/js/app.js')
    </head>
    <body>
        @php
            $currentUser = auth()->user();
            $role = $currentUser?->role;
            $avatarUrl = $currentUser?->avatar_path ? asset('storage/' . $currentUser->avatar_path) : null;
            $avatarInitial = strtoupper(substr($currentUser?->name ?? 'U', 0, 1));
            $profileRoute = match ($role) {
                \App\Models\User::ROLE_ADMIN => 'admin.profile.edit',
                \App\Models\User::ROLE_TEACHER => 'teacher.profile.edit',
                \App\Models\User::ROLE_STUDENT => 'student.profile.edit',
                default => 'home',
            };
            $dashboardRoute = match ($role) {
                \App\Models\User::ROLE_ADMIN => 'admin.dashboard',
                \App\Models\User::ROLE_TEACHER => 'teacher.dashboard',
                \App\Models\User::ROLE_STUDENT => 'student.dashboard',
                default => 'home',
            };
            $needsProfilePrompt = false;
            if ($currentUser && in_array($role, [\App\Models\User::ROLE_TEACHER, \App\Models\User::ROLE_STUDENT], true)) {
                $requiredFields = ['name', 'last_name', 'birth_date', 'phone', 'address', 'country', 'state', 'city', 'postal_code'];
                $missingField = false;
                foreach ($requiredFields as $field) {
                    if (empty($currentUser->{$field})) {
                        $missingField = true;
                        break;
                    }
                }
                $profileComplete = ! $missingField && ! empty($currentUser->avatar_path);
                $promptKey = 'profile_prompt_seen_' . now()->toDateString();
                if (! $profileComplete && ! session()->has($promptKey)) {
                    $needsProfilePrompt = true;
                    session()->put($promptKey, true);
                }
            }
        @endphp
        <div class="app">
            <header>
                <a class="logo" href="{{ route($dashboardRoute) }}" aria-label="Ir al inicio">
                    <img src="{{ asset('logo.png') }}" alt="Logo CristoApp" class="logo-mark logo-mark--inline">
                    <span class="logo-text">CristoApp</span>
                </a>
                <div class="header-actions">
                    @if ($role === \App\Models\User::ROLE_STUDENT || $role === \App\Models\User::ROLE_TEACHER)
                        <div class="menu-dropdown icon-badge">
                            <button class="icon-btn" type="button" aria-label="Notificaciones">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M15.5 17h-7a1 1 0 0 1-1-1c0-3 1-5 1-6a3.5 3.5 0 1 1 7 0c0 1 1 3 1 6a1 1 0 0 1-1 1Z"></path>
                                    <path d="M9.5 17a2.5 2.5 0 0 0 5 0"></path>
                                </svg>
                            </button>
                            @if (!empty($notificationUnreadCount))
                                <span class="badge">{{ $notificationUnreadCount }}</span>
                            @endif
                            <div class="dropdown">
                                <strong class="dropdown-title">Notificaciones</strong>
                                @php
                                    $notificationsRoute = $role === \App\Models\User::ROLE_TEACHER
                                        ? 'teacher.notifications.index'
                                        : 'student.notifications.index';
                                @endphp
                                @if (!empty($notificationUnreadCount))
                                    <a class="dropdown-item" href="{{ route($notificationsRoute) }}">Tienes una nueva notificacion</a>
                                @else
                                    <div class="dropdown-empty">Sin notificaciones</div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="menu-dropdown icon-badge">
                            <button class="icon-btn" type="button" aria-label="Notificaciones">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M15.5 17h-7a1 1 0 0 1-1-1c0-3 1-5 1-6a3.5 3.5 0 1 1 7 0c0 1 1 3 1 6a1 1 0 0 1-1 1Z"></path>
                                    <path d="M9.5 17a2.5 2.5 0 0 0 5 0"></path>
                                </svg>
                            </button>
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
                    <button class="icon-btn theme-toggle" type="button" id="theme-toggle" aria-label="Cambiar tema" aria-pressed="false">
                        <svg class="icon-moon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M15.5 5.5a7 7 0 1 0 3 12.5 6.5 6.5 0 0 1-3-12.5Z"></path>
                        </svg>
                        <svg class="icon-sun" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="4.5"></circle>
                            <path d="M12 3.5v2M12 18.5v2M4.5 12h2M17.5 12h2M6.4 6.4l1.4 1.4M16.2 16.2l1.4 1.4M17.6 6.4l-1.4 1.4M7.8 16.2l-1.4 1.4"></path>
                        </svg>
                    </button>
                    <button class="icon-btn mobile-menu-toggle" type="button" id="mobile-menu-toggle" aria-label="Abrir menu" aria-expanded="false">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 6h16"></path>
                            <path d="M4 12h16"></path>
                            <path d="M4 18h16"></path>
                        </svg>
                    </button>
                    <div class="menu-dropdown menu-dropdown--profile">
                        <button class="icon-btn icon-btn--avatar" type="button" aria-label="Perfil">
                            @if ($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="Foto de perfil">
                            @else
                                <span class="avatar-initials">{{ $avatarInitial }}</span>
                            @endif
                        </button>
                        <div class="dropdown">
                            <a href="{{ route($profileRoute) }}">Perfil</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit">Cerrar sesion</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <div class="menu-overlay" id="menu-overlay" aria-hidden="true"></div>
            <div class="layout">
                <aside>
                    <div class="menu-title">Menu</div>
                    <nav class="menu">
                        <a class="{{ request()->routeIs($dashboardRoute) ? 'is-active' : '' }}" href="{{ route($dashboardRoute) }}">
                            <span class="menu-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M3 9l9-7 9 7"></path>
                                    <path d="M9 22V12h6v10"></path>
                                </svg>
                            </span>
                            <span class="menu-label">Inicio</span>
                        </a>
                        @if ($role === \App\Models\User::ROLE_TEACHER)
                            <a class="{{ request()->routeIs('teacher.courses.*') ? 'is-active' : '' }}" href="{{ route('teacher.courses.index') }}">
                                <span class="menu-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"></path>
                                        <path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"></path>
                                    </svg>
                                </span>
                                <span class="menu-label">Contenidos de ayuda</span>
                            </a>
                            <a class="{{ request()->routeIs('teacher.meetings.*') ? 'is-active' : '' }}" href="{{ route('teacher.meetings.index') }}">
                                <span class="menu-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                        <path d="M16 2v4"></path>
                                        <path d="M8 2v4"></path>
                                        <path d="M3 10h18"></path>
                                    </svg>
                                </span>
                                <span class="menu-label">Reuniones</span>
                            </a>
                            <a class="{{ request()->routeIs('teacher.assets.*') ? 'is-active' : '' }}" href="{{ route('teacher.assets.index') }}">
                                <span class="menu-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M3 7h5l2 2h11v10a2 2 0 01-2 2H3z"></path>
                                        <path d="M3 7V6a2 2 0 012-2h4l2 2h10a2 2 0 012 2v1"></path>
                                    </svg>
                                </span>
                                <span class="menu-label">Gestor de contenidos</span>
                            </a>
                            <details class="menu-group" {{ request()->routeIs('teacher.tasks.*') ? 'open' : '' }}>
                                <summary class="menu-group__label">
                                    <span class="menu-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <path d="M14 2v6h6"></path>
                                            <path d="M16 13H8"></path>
                                            <path d="M16 17H8"></path>
                                            <path d="M10 9H8"></path>
                                        </svg>
                                    </span>
                                    <span class="menu-label">Tareas</span>
                                    <span class="menu-chevron" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M6 9l6 6 6-6"></path>
                                        </svg>
                                    </span>
                                </summary>
                                <div class="menu-submenu">
                                    <a class="menu-submenu__link {{ request()->routeIs('teacher.tasks.create') ? 'is-active' : '' }}" href="{{ route('teacher.tasks.create') }}">Crear tarea</a>
                                    <a class="menu-submenu__link {{ request()->routeIs('teacher.tasks.index') ? 'is-active' : '' }}" href="{{ route('teacher.tasks.index') }}">Ver tareas</a>
                                </div>
                            </details>
                            <details class="menu-group" {{ request()->routeIs('teacher.students.*') ? 'open' : '' }}>
                                <summary class="menu-group__label">
                                    <span class="menu-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M17 21v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-1a4 4 0 00-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 010 7.75"></path>
                                        </svg>
                                    </span>
                                    <span class="menu-label">Usuarios</span>
                                    <span class="menu-chevron" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M6 9l6 6 6-6"></path>
                                        </svg>
                                    </span>
                                </summary>
                                <div class="menu-submenu">
                                    <a class="menu-submenu__link {{ request()->routeIs('teacher.students.index') ? 'is-active' : '' }}" href="{{ route('teacher.students.index') }}">Listado de usuarios</a>
                                    <a class="menu-submenu__link {{ request()->routeIs('teacher.students.commission') ? 'is-active' : '' }}" href="{{ route('teacher.students.commission') }}">Comision</a>
                                </div>
                            </details>
                            <a class="{{ request()->routeIs('teacher.notifications.*') ? 'is-active' : '' }}" href="{{ route('teacher.notifications.index') }}">
                                <span class="menu-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M18 8a6 6 0 00-12 0c0 7-3 7-3 7h18s-3 0-3-7"></path>
                                        <path d="M13.73 21a2 2 0 01-3.46 0"></path>
                                    </svg>
                                </span>
                                <span class="menu-label">Notificaciones</span>
                                @if (!empty($notificationUnreadCount))
                                    <span class="badge badge-warn">{{ $notificationUnreadCount }}</span>
                                @endif
                            </a>
                            <a class="{{ request()->routeIs('teacher.profile.*') ? 'is-active' : '' }}" href="{{ route('teacher.profile.edit') }}">
                                <span class="menu-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </span>
                                <span class="menu-label">Perfil</span>
                            </a>
                            <a class="{{ request()->routeIs('chat.index') ? 'is-active' : '' }}" href="{{ route('chat.index') }}">
                                <span class="menu-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8z"></path>
                                    </svg>
                                </span>
                                <span class="menu-label">Chat</span>
                                @if (!empty($chatUnreadCount))
                                    <span class="badge">{{ $chatUnreadCount }}</span>
                                @endif
                            </a>
                        @elseif ($role === \App\Models\User::ROLE_STUDENT)
                            <a class="{{ request()->routeIs('student.courses.*') ? 'is-active' : '' }}" href="{{ route('student.courses.index') }}">
                                <span class="menu-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"></path>
                                        <path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"></path>
                                    </svg>
                                </span>
                                <span class="menu-label">Contenidos de ayuda</span>
                            </a>
                            <a class="{{ request()->routeIs('student.meetings.*') ? 'is-active' : '' }}" href="{{ route('student.meetings.index') }}">
                                <span class="menu-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                        <path d="M16 2v4"></path>
                                        <path d="M8 2v4"></path>
                                        <path d="M3 10h18"></path>
                                    </svg>
                                </span>
                                <span class="menu-label">Reuniones</span>
                            </a>
                            <details class="menu-group" {{ request()->routeIs('student.tasks.*') ? 'open' : '' }}>
                                <summary class="menu-group__label">
                                    <span class="menu-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <path d="M14 2v6h6"></path>
                                            <path d="M16 13H8"></path>
                                            <path d="M16 17H8"></path>
                                            <path d="M10 9H8"></path>
                                        </svg>
                                    </span>
                                    <span class="menu-label">Tareas</span>
                                    <span class="menu-chevron" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M6 9l6 6 6-6"></path>
                                        </svg>
                                    </span>
                                </summary>
                                <div class="menu-submenu">
                                    <a class="menu-submenu__link {{ request()->routeIs('student.tasks.pending') ? 'is-active' : '' }}" href="{{ route('student.tasks.pending') }}">Tareas pendientes</a>
                                    <a class="menu-submenu__link {{ request()->routeIs('student.tasks.index') ? 'is-active' : '' }}" href="{{ route('student.tasks.index') }}">Listado de tareas</a>
                                </div>
                            </details>
                            <a class="{{ request()->routeIs('student.notifications.*') ? 'is-active' : '' }}" href="{{ route('student.notifications.index') }}">
                                <span class="menu-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M18 8a6 6 0 00-12 0c0 7-3 7-3 7h18s-3 0-3-7"></path>
                                        <path d="M13.73 21a2 2 0 01-3.46 0"></path>
                                    </svg>
                                </span>
                                <span class="menu-label">Notificaciones</span>
                                @if (!empty($notificationUnreadCount))
                                    <span class="badge badge-warn">{{ $notificationUnreadCount }}</span>
                                @endif
                            </a>
                            <a class="{{ request()->routeIs('student.profile.*') ? 'is-active' : '' }}" href="{{ route('student.profile.edit') }}">
                                <span class="menu-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </span>
                                <span class="menu-label">Perfil</span>
                            </a>
                            <a class="{{ request()->routeIs('chat.index') ? 'is-active' : '' }}" href="{{ route('chat.index') }}">
                                <span class="menu-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8z"></path>
                                    </svg>
                                </span>
                                <span class="menu-label">Chat</span>
                                @if (!empty($chatUnreadCount))
                                    <span class="badge">{{ $chatUnreadCount }}</span>
                                @endif
                            </a>
                        @elseif ($role === \App\Models\User::ROLE_ADMIN)
                            <a class="{{ request()->routeIs('admin.plans.*') ? 'is-active' : '' }}" href="{{ route('admin.plans.index') }}">
                                <span class="menu-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"></path>
                                        <path d="M14 2v6h6"></path>
                                        <path d="M16 13H8"></path>
                                        <path d="M16 17H8"></path>
                                        <path d="M10 9H8"></path>
                                    </svg>
                                </span>
                                <span class="menu-label">Gestion de planes</span>
                            </a>
                            <a class="{{ request()->routeIs('admin.errors.*') ? 'is-active' : '' }}" href="{{ route('admin.errors.index') }}">
                                <span class="menu-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
                                        <path d="M12 9v4"></path>
                                        <path d="M12 17h.01"></path>
                                    </svg>
                                </span>
                                <span class="menu-label">Errores del sistema</span>
                            </a>
                        @endif
                    </nav>
                </aside>
                <main>
                    @if ($needsProfilePrompt)
                        <div class="alert alert-profile">
                            <div>
                                <strong>Completa tu perfil</strong>
                                <div class="subtle">Para continuar, completa tus datos personales y sube una imagen de perfil.</div>
                            </div>
                            <a class="btn btn-secondary" href="{{ route($profileRoute) }}">Ir al perfil</a>
                        </div>
                    @endif
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
                var toggle = document.getElementById('mobile-menu-toggle');
                var overlay = document.getElementById('menu-overlay');
                var body = document.body;
                if (!toggle || !overlay) {
                    return;
                }
                var closeMenu = function () {
                    body.classList.remove('menu-open');
                    toggle.setAttribute('aria-expanded', 'false');
                };
                var openMenu = function () {
                    body.classList.add('menu-open');
                    toggle.setAttribute('aria-expanded', 'true');
                };
                toggle.addEventListener('click', function () {
                    if (body.classList.contains('menu-open')) {
                        closeMenu();
                        return;
                    }
                    openMenu();
                });
                overlay.addEventListener('click', closeMenu);
                var links = document.querySelectorAll('aside .menu a');
                links.forEach(function (link) {
                    link.addEventListener('click', closeMenu);
                });
            });
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
        @include('partials.alerts')
    </body>
</html>
