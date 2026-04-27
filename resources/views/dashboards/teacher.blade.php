@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1 class="view-title">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 9l9-7 9 7"></path>
                    <path d="M9 22V12h6v10"></path>
                </svg>
                Panel de lider
            </h1>
            <p>Resumen general de tu comunidad.</p>
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
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                    </div>
                    <div class="metric-body">
                        <span class="metric-label">Contenidos de ayuda</span>
                        <strong class="metric-value">{{ $courseCount }}</strong>
                    </div>
                </div>
                <div class="metric metric--students">
                    <div class="metric-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M17 21v-1a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v1"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-1a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div class="metric-body">
                        <span class="metric-label">Usuarios</span>
                        <strong class="metric-value">{{ $studentCount }}</strong>
                    </div>
                </div>
                <div class="metric metric--lessons">
                    <div class="metric-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <path d="M14 2v6h6"></path>
                            <path d="M16 13H8"></path>
                            <path d="M16 17H8"></path>
                            <path d="M10 9H8"></path>
                        </svg>
                    </div>
                    <div class="metric-body">
                        <span class="metric-label">Lecciones</span>
                        <strong class="metric-value">{{ $lessonCount }}</strong>
                    </div>
                </div>
                <div class="metric metric--announcements">
                    <div class="metric-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M3 11h6l7-4v10l-7-4H3z"></path>
                            <path d="M3 11v2"></path>
                            <path d="M9 15l1 4"></path>
                        </svg>
                    </div>
                    <div class="metric-body">
                        <span class="metric-label">Anuncios</span>
                        <strong class="metric-value">{{ $announcementCount }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="widget widget--clock widget--compact">
            <div class="widget-header">
                <h3>Fecha y hora</h3>
            </div>
            <div class="clock">
                <div class="clock-panel">
                    <div class="clock-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M12 7v6l4 2"></path>
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

        <div class="widget widget--calendar">
            <div class="widget-header">
                <h3>Calendario</h3>
                <span class="subtle">Reuniones agendadas este mes.</span>
            </div>
            <div class="calendar">
                <div class="calendar-header" id="calendar-title">Mes</div>
                <div class="calendar-grid" id="calendar-grid"></div>
            </div>
            @if ($upcomingMeetings->isEmpty())
                <p class="subtle">No hay reuniones proximas.</p>
            @else
                <div class="list">
                    @foreach ($upcomingMeetings as $meeting)
                        <div class="card meeting-card">
                            <div>
                                <strong>{{ $meeting->title }}</strong>
                                <div class="subtle">
                                    {{ $meeting->scheduled_for->format('d/m/Y H:i') }} · {{ $meeting->duration_minutes }} min
                                </div>
                            </div>
                            <a class="btn btn-secondary" href="{{ $meeting->jitsi_room_url }}" target="_blank" rel="noopener">
                                Abrir reunion
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="widget widget--announcements widget--compact">
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

            var meetingDays = @json($calendarMeetings->map(function ($meeting) {
                return [
                    'date' => $meeting->scheduled_for->toDateString(),
                    'title' => $meeting->title,
                ];
            })->values());
            var meetingMap = {};
            meetingDays.forEach(function (meeting) {
                if (!meetingMap[meeting.date]) {
                    meetingMap[meeting.date] = [];
                }
                meetingMap[meeting.date].push(meeting.title);
            });

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
                var dateKey = new Date(year, month, d).toISOString().slice(0, 10);
                if (meetingMap[dateKey]) {
                    cell.className += ' calendar-day--event';
                    cell.title = meetingMap[dateKey].join(', ');
                }
                cell.textContent = d;
                calendarGrid.appendChild(cell);
            }
        })();
    </script>
@endsection
