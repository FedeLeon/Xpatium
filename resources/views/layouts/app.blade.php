<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Xpatium' }}</title>
        <style>
            :root {
                color-scheme: light;
            }
            body {
                margin: 0;
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                background: #f6f7fb;
                color: #1f2937;
            }
            .app {
                display: grid;
                grid-template-rows: 64px 1fr;
                min-height: 100vh;
            }
            header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0 20px;
                background: #ffffff;
                border-bottom: 1px solid #e5e7eb;
            }
            .logo {
                font-weight: 700;
                letter-spacing: 0.5px;
            }
            .header-actions {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .icon-btn {
                width: 36px;
                height: 36px;
                border-radius: 8px;
                border: 1px solid #e5e7eb;
                background: #ffffff;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 18px;
                cursor: pointer;
            }
            .layout {
                display: grid;
                grid-template-columns: 220px 1fr;
                gap: 16px;
            }
            aside {
                background: #ffffff;
                border-right: 1px solid #e5e7eb;
                padding: 16px;
            }
            .menu-title {
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: #6b7280;
                margin-bottom: 10px;
            }
            .menu a {
                display: block;
                padding: 10px 12px;
                border-radius: 8px;
                color: #1f2937;
                text-decoration: none;
                border: 1px solid transparent;
            }
            .menu a:hover {
                background: #f3f4f6;
                border-color: #e5e7eb;
            }
            main {
                padding: 24px;
            }
            .actions {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 12px;
                margin-bottom: 16px;
            }
            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 10px 14px;
                border-radius: 8px;
                border: 1px solid #2563eb;
                background: #2563eb;
                color: #ffffff;
                text-decoration: none;
                font-weight: 600;
            }
            .btn-secondary {
                border-color: #e5e7eb;
                background: #ffffff;
                color: #1f2937;
            }
            .grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                gap: 16px;
            }
            .card {
                background: #ffffff;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                padding: 16px;
                display: grid;
                gap: 10px;
            }
            .card img {
                width: 100%;
                height: 140px;
                object-fit: cover;
                border-radius: 8px;
                background: #f3f4f6;
            }
            .placeholder {
                width: 100%;
                height: 140px;
                border-radius: 8px;
                background: #f3f4f6;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #9ca3af;
                font-size: 12px;
            }
            .card h3 {
                margin: 0;
                font-size: 16px;
            }
            .card p {
                margin: 0;
                color: #6b7280;
                font-size: 13px;
            }
            .form-grid {
                display: grid;
                gap: 14px;
                max-width: 520px;
            }
            .form-grid label {
                display: block;
                margin-bottom: 6px;
                font-weight: 600;
                font-size: 14px;
            }
            .form-grid input,
            .form-grid textarea {
                width: 100%;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                padding: 10px 12px;
                font-size: 14px;
            }
            .form-grid textarea {
                min-height: 110px;
                resize: vertical;
            }
            .form-grid .hint {
                font-size: 12px;
                color: #6b7280;
                margin-top: 6px;
            }
            .alert {
                background: #fef2f2;
                border: 1px solid #fecaca;
                color: #b91c1c;
                padding: 10px 12px;
                border-radius: 8px;
                font-size: 13px;
                margin-bottom: 16px;
            }
            .subtle {
                color: #6b7280;
                font-size: 13px;
            }
            .badge {
                display: inline-block;
                background: #eef2ff;
                color: #3730a3;
                font-size: 12px;
                padding: 4px 8px;
                border-radius: 999px;
            }
            .list {
                display: grid;
                gap: 12px;
            }
            dialog {
                border: 0;
                border-radius: 12px;
                padding: 0;
                width: min(520px, 92vw);
                box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
            }
            dialog::backdrop {
                background: rgba(15, 23, 42, 0.4);
            }
            .modal-body {
                padding: 18px;
            }
            .modal-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px solid #e5e7eb;
                padding: 14px 18px;
            }
            .modal-footer {
                display: flex;
                justify-content: flex-end;
                gap: 8px;
                border-top: 1px solid #e5e7eb;
                padding: 14px 18px;
            }
            .menu-dropdown {
                position: relative;
            }
            .dropdown {
                position: absolute;
                right: 0;
                top: 44px;
                background: #ffffff;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                padding: 8px;
                min-width: 180px;
                display: none;
                box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
            }
            .menu-dropdown:focus-within .dropdown,
            .menu-dropdown:hover .dropdown {
                display: block;
            }
            .dropdown a,
            .dropdown button {
                display: block;
                width: 100%;
                text-align: left;
                padding: 8px 10px;
                border: 0;
                background: transparent;
                color: #1f2937;
                cursor: pointer;
                text-decoration: none;
            }
            .dropdown a:hover,
            .dropdown button:hover {
                background: #f3f4f6;
                border-radius: 6px;
            }
        </style>
    </head>
    <body>
        <div class="app">
            <header>
                <div class="logo">Xpatium</div>
                <div class="header-actions">
                    <button class="icon-btn" type="button" aria-label="Notificaciones">🔔</button>
                    <div class="menu-dropdown">
                        <button class="icon-btn" type="button" aria-label="Perfil">👤</button>
                        <div class="dropdown">
                            <a href="#">Configuracion</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit">Cerrar sesion</button>
                            </form>
                        </div>
                    </div>
                    <button class="icon-btn" type="button" aria-label="Menu">☰</button>
                </div>
            </header>

            <div class="layout">
                <aside>
                    <div class="menu-title">Menu</div>
                    <nav class="menu">
                        @php
                            $role = auth()->user()?->role;
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
                        @endif
                    </nav>
                </aside>
                <main>
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
