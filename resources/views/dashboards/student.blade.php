@extends('layouts.app')

@section('content')
    @php
        $studentAvatarUrl = auth()->user()?->avatar_path ? asset('storage/' . auth()->user()->avatar_path) : null;
        $studentInitial = strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1));
    @endphp
    <h1 class="view-title">
        <span class="view-avatar">
            @if ($studentAvatarUrl)
                <img src="{{ $studentAvatarUrl }}" alt="Foto de {{ auth()->user()->name }}">
            @else
                {{ $studentInitial }}
            @endif
        </span>
        Bienvenido, {{ auth()->user()->name }} {{ auth()->user()->last_name }}
    </h1>
    <p>Este es tu espacio de aprendizaje. Continua con tus cursos activos.</p>

    @php
        $hasUnreadAnnouncements = $unreadAnnouncementsCount > 0;
        $nextMeeting = $upcomingMeetings->first();
        $nextMeetingJoinAt = $nextMeeting?->scheduled_for?->copy()->subMinutes(15);
        $nextMeetingEndAt = $nextMeeting?->scheduled_for?->copy()->addMinutes($nextMeeting->duration_minutes ?? 0);
        $nextMeetingJoinAvailable = $nextMeeting && now()->between($nextMeetingJoinAt, $nextMeetingEndAt);
    @endphp

    <div class="dashboard-card-grid">
        <div class="card dashboard-card dashboard-card--courses">
            <div class="section-head">
                <div>
                    <h3>Mis cursos</h3>
                    <p class="subtle">Total de cursos habilitados.</p>
                </div>
            </div>

            <div class="stat-card">
                <div>
                    <span class="stat-label">Cursos activos</span>
                    <span class="stat-value">{{ $courses->count() }}</span>
                </div>
            </div>

            <a class="btn btn-secondary" href="{{ route('student.courses.index') }}">Ver cursos</a>
        </div>

        <div class="card dashboard-card dashboard-card--announcements {{ $hasUnreadAnnouncements ? 'dashboard-card--alert' : '' }}">
            <div>
                <h3>Anuncios</h3>
                <p class="subtle">Avisos publicados por tu profesor.</p>
            </div>

            <div class="announcement-status">
                <span class="announcement-count">{{ $unreadAnnouncementsCount }}</span>
                @if ($announcements->isEmpty())
                    <span class="subtle">No hay anuncios disponibles.</span>
                @elseif ($hasUnreadAnnouncements)
                    <span class="subtle">Tenes anuncios sin leer.</span>
                @else
                    <span class="subtle">No hay anuncios pendientes.</span>
                @endif
            </div>

            <button class="btn btn-secondary" type="button" onclick="document.getElementById('studentAnnouncementModal').showModal()">
                Ver anuncios
            </button>
        </div>

        <div class="card dashboard-card dashboard-card--next-meeting">
            <div>
                <h3>Proxima reunion</h3>
                <p class="subtle">Acceso rapido a tu siguiente clase en vivo.</p>
            </div>

            @if (! $nextMeeting)
                <p class="subtle">No hay reuniones proximas.</p>
                <a class="btn btn-secondary" href="{{ route('student.meetings.index') }}">Ver agenda</a>
            @else
                <div class="stat-card">
                    <div>
                        <span class="stat-label">{{ $nextMeeting->title }}</span>
                        <span class="stat-value" data-next-meeting-countdown>Calculando...</span>
                        <span class="subtle">
                            {{ $nextMeeting->scheduled_for->format('d/m/Y H:i') }}
                            @if ($nextMeeting->teacher)
                                · {{ $nextMeeting->teacher->name }} {{ $nextMeeting->teacher->last_name }}
                            @endif
                        </span>
                    </div>
                </div>
                <a
                    class="btn btn-secondary {{ $nextMeetingJoinAvailable ? '' : 'is-disabled' }}"
                    href="{{ $nextMeeting->daily_room_url }}"
                    target="_blank"
                    rel="noopener"
                    data-next-meeting-join
                    data-next-meeting-start="{{ $nextMeeting->scheduled_for->timestamp }}"
                    data-next-meeting-join-at="{{ $nextMeetingJoinAt?->timestamp }}"
                    data-next-meeting-end="{{ $nextMeetingEndAt?->timestamp }}"
                    aria-disabled="{{ $nextMeetingJoinAvailable ? 'false' : 'true' }}"
                >
                    Ingresar a la reunion
                </a>
            @endif
        </div>
    </div>

    <div class="widget widget-wide">
        <div class="widget-header">
            <h3>Calendario</h3>
            <span class="subtle">Tus proximas reuniones.</span>
        </div>
        <div class="calendar">
            <div class="calendar-header" id="student-calendar-title">Mes</div>
            <div class="calendar-grid" id="student-calendar-grid"></div>
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
                        <a class="btn btn-secondary" href="{{ $meeting->daily_room_url }}" target="_blank" rel="noopener">
                            Abrir reunion
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <dialog id="studentAnnouncementModal">
        <div class="modal-header">
            <strong>Anuncios</strong>
            <button class="btn btn-secondary" type="button" onclick="document.getElementById('studentAnnouncementModal').close()">Cerrar</button>
        </div>
        <div class="modal-body">
            @if ($announcements->isEmpty())
                <p class="subtle">No hay anuncios disponibles.</p>
            @else
                <div class="list announcement-list">
                    @foreach ($announcements as $announcement)
                        @php
                            $isRead = in_array($announcement->id, $readAnnouncementIds, true);
                        @endphp
                        <details class="announcement-item {{ $isRead ? '' : 'announcement-item--unread' }}">
                            <summary class="announcement-summary">
                                <div>
                                    <h3>{{ $announcement->title }}</h3>
                                    <p class="subtle">
                                        {{ $announcement->teacher->name ?? 'Profesor' }}
                                        @if ($announcement->starts_at || $announcement->ends_at)
                                            ·
                                            @if ($announcement->starts_at)
                                                Desde {{ $announcement->starts_at->format('d/m/Y') }}
                                            @endif
                                            @if ($announcement->ends_at)
                                                hasta {{ $announcement->ends_at->format('d/m/Y') }}
                                            @endif
                                        @endif
                                    </p>
                                </div>
                                @if (! $isRead)
                                    <span class="badge badge-warn">Nuevo</span>
                                @else
                                    <span class="badge">Leido</span>
                                @endif
                            </summary>
                            <div class="announcement-body">
                                <p>{{ $announcement->body }}</p>
                                @if ($announcement->image_path)
                                    <img class="announcement-image" src="{{ asset('storage/' . $announcement->image_path) }}" alt="Imagen del anuncio">
                                @endif
                                <div class="announcement-item__actions">
                                    @if (! $isRead)
                                        <form method="POST" action="{{ route('student.announcements.read', $announcement) }}">
                                            @csrf
                                            <button class="btn btn-secondary" type="submit">Marcar como leido</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </details>
                    @endforeach
                </div>
            @endif
        </div>
    </dialog>

    <script>
        (function () {
            var calendarTitle = document.getElementById('student-calendar-title');
            var calendarGrid = document.getElementById('student-calendar-grid');
            if (!calendarTitle || !calendarGrid) {
                return;
            }

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
    <script>
        (function () {
            var joinButton = document.querySelector('[data-next-meeting-join]');
            var countdown = document.querySelector('[data-next-meeting-countdown]');
            if (!joinButton || !countdown) {
                return;
            }

            var startAt = Number(joinButton.getAttribute('data-next-meeting-start')) || 0;
            var joinAt = Number(joinButton.getAttribute('data-next-meeting-join-at')) || 0;
            var endAt = Number(joinButton.getAttribute('data-next-meeting-end')) || 0;

            var formatRemaining = function (seconds) {
                var total = Math.max(0, Math.floor(seconds));
                var days = Math.floor(total / 86400);
                var hours = Math.floor((total % 86400) / 3600);
                var minutes = Math.floor((total % 3600) / 60);
                if (days > 0) {
                    return 'Falta ' + days + 'd ' + hours + 'h';
                }
                if (hours > 0) {
                    return 'Falta ' + hours + 'h ' + minutes + 'm';
                }
                return 'Falta ' + Math.max(1, minutes) + 'm';
            };

            var updateMeetingCard = function () {
                var now = Math.floor(Date.now() / 1000);
                if (endAt && now >= endAt) {
                    countdown.textContent = 'Reunion finalizada';
                    joinButton.classList.add('is-disabled');
                    joinButton.setAttribute('aria-disabled', 'true');
                    return;
                }

                if (startAt && now >= startAt) {
                    countdown.textContent = 'Reunion en curso';
                } else if (startAt) {
                    countdown.textContent = formatRemaining(startAt - now);
                }

                if (joinAt && now >= joinAt) {
                    joinButton.classList.remove('is-disabled');
                    joinButton.setAttribute('aria-disabled', 'false');
                } else {
                    joinButton.classList.add('is-disabled');
                    joinButton.setAttribute('aria-disabled', 'true');
                }
            };

            updateMeetingCard();
            setInterval(updateMeetingCard, 30000);
        })();
    </script>
@endsection
