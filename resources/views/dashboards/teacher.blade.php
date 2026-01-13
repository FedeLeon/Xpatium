@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1>Panel de profesor</h1>
            <p>Resumen general de tu academia.</p>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="widget">
            <div class="widget-header">
                <h3>Metricas</h3>
            </div>
            <div class="metric-grid">
                <div class="metric">
                    <span class="metric-label">Cursos</span>
                    <strong class="metric-value">{{ $courseCount }}</strong>
                </div>
                <div class="metric">
                    <span class="metric-label">Alumnos</span>
                    <strong class="metric-value">{{ $studentCount }}</strong>
                </div>
                <div class="metric">
                    <span class="metric-label">Lecciones</span>
                    <strong class="metric-value">{{ $lessonCount }}</strong>
                </div>
                <div class="metric">
                    <span class="metric-label">Anuncios</span>
                    <strong class="metric-value">{{ $announcementCount }}</strong>
                </div>
            </div>
        </div>

        <div class="widget">
            <div class="widget-header">
                <h3>Fecha y hora</h3>
            </div>
            <div class="clock">
                <div class="clock-date" id="clock-date">--/--/----</div>
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
