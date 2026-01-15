@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1>Panel de profesor</h1>
            <p>Resumen general de tu academia.</p>
        </div>
    </div>

    <div class="dashboard-grid dashboard-grid--teacher">
        <div class="widget widget--metrics">
            <div class="widget-header">
                <h3>Metricas</h3>
            </div>
            <div class="metric-grid">
                <div class="metric metric--courses">
                    <div class="metric-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M4 5.5a2 2 0 0 1 2-2h9a3 3 0 0 1 3 3V19a1 1 0 0 1-1.6.8l-2.9-2.2H6a2 2 0 0 1-2-2V5.5zm2-1a1 1 0 0 0-1 1V15a1 1 0 0 0 1 1h7.1l2.9 2.2V6.5a2 2 0 0 0-2-2H6z"/>
                        </svg>
                    </div>
                    <div class="metric-body">
                        <span class="metric-label">Cursos</span>
                        <strong class="metric-value">{{ $courseCount }}</strong>
                    </div>
                </div>
                <div class="metric metric--students">
                    <div class="metric-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M16 11a4 4 0 1 0-3.9-5H6a3 3 0 0 0-3 3v9a2 2 0 0 0 2 2h7.1a5 5 0 0 0 9.4-2.5V16a5 5 0 0 0-5-5zM8 12a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1.5c-2.4 0-4.5 1.2-4.5 3V18h9v-1.5c0-1.8-2.1-3-4.5-3zm11-2.5a4 4 0 1 1-4 4 4 4 0 0 1 4-4z"/>
                        </svg>
                    </div>
                    <div class="metric-body">
                        <span class="metric-label">Alumnos</span>
                        <strong class="metric-value">{{ $studentCount }}</strong>
                    </div>
                </div>
                <div class="metric metric--lessons">
                    <div class="metric-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M5 4a2 2 0 0 1 2-2h8a3 3 0 0 1 3 3v14a1 1 0 0 1-1.6.8L13 17.5H7a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v11.5a1 1 0 0 0 1 1h6.2l2.8 2.1V5a2 2 0 0 0-2-2H7z"/>
                        </svg>
                    </div>
                    <div class="metric-body">
                        <span class="metric-label">Lecciones</span>
                        <strong class="metric-value">{{ $lessonCount }}</strong>
                    </div>
                </div>
                <div class="metric metric--announcements">
                    <div class="metric-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 8a2 2 0 0 1 2-2h8.6l4.7-2.7A1 1 0 0 1 20 4v12a1 1 0 0 1-1.7.7L13.6 14H6l-1 4H3l1-4a2 2 0 0 1-1-2V8zm3 0v4h7.9l4.1 2.3V5.7L13.9 8H6z"/>
                        </svg>
                    </div>
                    <div class="metric-body">
                        <span class="metric-label">Anuncios</span>
                        <strong class="metric-value">{{ $announcementCount }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="widget widget--clock">
            <div class="widget-header">
                <h3>Fecha y hora</h3>
            </div>
            <div class="clock">
                <div class="clock-panel">
                    <div class="clock-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 2a8 8 0 1 1-8 8 8 8 0 0 1 8-8zm-1 3h2v5.2l3.6 2.1-1 1.7L11 13V7z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="clock-label">Ahora</div>
                        <div class="clock-date" id="clock-date">--/--/----</div>
                    </div>
                </div>
                <div class="clock-time" id="clock-time">--:--</div>
            </div>
        </div>

        <div class="widget widget-wide">
            <div class="widget-header">
                <h3>Anuncios</h3>
                <a class="btn btn-secondary" href="{{ route('teacher.announcements.index') }}">Gestionar</a>
            </div>
            @if ($announcements->isEmpty())
                <p class="subtle">No hay anuncios creados.</p>
            @else
                <div class="list">
                    @foreach ($announcements as $announcement)
                        <div class="card">
                            <div>
                                <div class="actions actions-tight">
                                    <h3>{{ $announcement->title }}</h3>
                                    <span class="badge">{{ $announcement->status }}</span>
                                </div>
                                <p>{{ $announcement->body }}</p>
                                @if ($announcement->starts_at || $announcement->ends_at)
                                    <p class="subtle">
                                        @if ($announcement->starts_at)
                                            Desde {{ $announcement->starts_at->format('d/m/Y') }}
                                        @endif
                                        @if ($announcement->ends_at)
                                            hasta {{ $announcement->ends_at->format('d/m/Y') }}
                                        @endif
                                    </p>
                                @endif
                            </div>
                            @if ($announcement->image_path)
                                <img class="announcement-image" src="{{ asset('storage/' . $announcement->image_path) }}" alt="Imagen del anuncio">
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="widget widget-wide">
            <div class="widget-header">
                <h3>Calendario</h3>
                <span class="subtle">Proximamente reuniones y clases en vivo.</span>
            </div>
            <div class="calendar">
                <div class="calendar-header" id="calendar-title">Mes</div>
                <div class="calendar-grid" id="calendar-grid"></div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var dateEl = document.getElementById('clock-date');
            var timeEl = document.getElementById('clock-time');
            var updateClock = function () {
                var now = new Date();
                dateEl.textContent = now.toLocaleDateString('es-AR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
                timeEl.textContent = now.toLocaleTimeString('es-AR', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            };
            updateClock();
            setInterval(updateClock, 1000);

            var calendarTitle = document.getElementById('calendar-title');
            var calendarGrid = document.getElementById('calendar-grid');
            var now = new Date();
            var year = now.getFullYear();
            var month = now.getMonth();
            var firstDay = new Date(year, month, 1);
            var lastDay = new Date(year, month + 1, 0);
            var startWeekday = firstDay.getDay();
            var totalDays = lastDay.getDate();
            calendarTitle.textContent = now.toLocaleDateString('es-AR', { month: 'long', year: 'numeric' });

            var weekdays = ['D', 'L', 'M', 'X', 'J', 'V', 'S'];
            weekdays.forEach(function (day) {
                var cell = document.createElement('div');
                cell.className = 'calendar-day calendar-day--header';
                cell.textContent = day;
                calendarGrid.appendChild(cell);
            });

            for (var i = 0; i < startWeekday; i++) {
                var empty = document.createElement('div');
                empty.className = 'calendar-day calendar-day--empty';
                calendarGrid.appendChild(empty);
            }

            for (var d = 1; d <= totalDays; d++) {
                var cell = document.createElement('div');
                cell.className = 'calendar-day';
                if (d === now.getDate()) {
                    cell.className += ' calendar-day--today';
                }
                cell.textContent = d;
                calendarGrid.appendChild(cell);
            }
        })();
    </script>
@endsection
