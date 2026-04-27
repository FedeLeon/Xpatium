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
    <p>Este es tu espacio de aprendizaje. Continua con tus contenidos de ayuda activos.</p>

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
                    <h3>Mis contenidos de ayuda</h3>
                    <p class="subtle">Total de contenidos de ayuda habilitados.</p>
                </div>
            </div>

            <div class="stat-card">
                <div>
                    <span class="stat-label">Contenidos de ayuda activos</span>
                    <span class="stat-value">{{ $courses->count() }}</span>
                </div>
            </div>

            <a class="btn btn-secondary" href="{{ route('student.courses.index') }}">Ver contenidos de ayuda</a>
        </div>

        <div class="card dashboard-card dashboard-card--announcements {{ $hasUnreadAnnouncements ? 'dashboard-card--alert' : '' }}">
            <div>
                <h3>Anuncios</h3>
                <p class="subtle">Avisos publicados por tu lider.</p>
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
                    href="{{ $nextMeeting->jitsi_room_url }}"
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
            <span class="subtle">Navega meses y consulta reuniones por dia.</span>
        </div>
        <div class="calendar teacher-calendar">
            <div class="calendar-controls">
                <button class="calendar-nav-btn" id="student-calendar-prev" type="button" aria-label="Mes anterior">‹</button>
                <div class="calendar-header" id="student-calendar-title">Mes</div>
                <button class="calendar-nav-btn" id="student-calendar-next" type="button" aria-label="Mes siguiente">›</button>
            </div>
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
                        <a class="btn btn-secondary meeting-open-btn" href="{{ $meeting->jitsi_room_url }}" target="_blank" rel="noopener">
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
                                        {{ $announcement->teacher->name ?? 'Lider' }}
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

    <dialog id="studentCalendarMeetingModal">
        <div class="modal-header">
            <strong id="student-calendar-modal-title">Reuniones</strong>
            <button class="btn btn-secondary" type="button" onclick="document.getElementById('studentCalendarMeetingModal').close()">Cerrar</button>
        </div>
        <div class="modal-body" id="student-calendar-modal-body">
            <p class="subtle">Sin reuniones para este dia.</p>
        </div>
    </dialog>

    @php
        $calendarMeetingsForJs = $calendarMeetings->map(function ($meeting) {
            return [
                'date' => $meeting->scheduled_for->toDateString(),
                'date_time' => $meeting->scheduled_for->format('d/m/Y H:i'),
                'title' => $meeting->title,
                'meeting_type' => $meeting->meeting_type === 'individual' ? 'Individual' : 'Grupal',
                'duration_minutes' => $meeting->duration_minutes,
                'jitsi_room_url' => $meeting->jitsi_room_url,
                'teacher_name' => trim(($meeting->teacher->name ?? '') . ' ' . ($meeting->teacher->last_name ?? '')),
            ];
        })->values();
    @endphp

    <script>
        (function () {
            var calendarTitle = document.getElementById('student-calendar-title');
            var calendarGrid = document.getElementById('student-calendar-grid');
            var prevButton = document.getElementById('student-calendar-prev');
            var nextButton = document.getElementById('student-calendar-next');
            var modal = document.getElementById('studentCalendarMeetingModal');
            var modalTitle = document.getElementById('student-calendar-modal-title');
            var modalBody = document.getElementById('student-calendar-modal-body');
            if (!calendarTitle || !calendarGrid || !prevButton || !nextButton || !modal || !modalTitle || !modalBody) {
                return;
            }

            var meetings = @json($calendarMeetingsForJs);
            var meetingsByDate = {};
            meetings.forEach(function (meeting) {
                if (!meetingsByDate[meeting.date]) {
                    meetingsByDate[meeting.date] = [];
                }
                meetingsByDate[meeting.date].push(meeting);
            });

            var today = new Date();
            var viewYear = today.getFullYear();
            var viewMonth = today.getMonth();
            var weekdays = ['D', 'L', 'M', 'X', 'J', 'V', 'S'];

            var formatDateKey = function (year, month, day) {
                var monthValue = String(month + 1).padStart(2, '0');
                var dayValue = String(day).padStart(2, '0');
                return year + '-' + monthValue + '-' + dayValue;
            };

            var openMeetingModal = function (dateKey, dayMeetings) {
                var parts = dateKey.split('-');
                var displayDate = parts[2] + '/' + parts[1] + '/' + parts[0];
                modalTitle.textContent = 'Reuniones del ' + displayDate;
                modalBody.innerHTML = '';

                dayMeetings.forEach(function (meeting) {
                    var card = document.createElement('div');
                    card.className = 'card';

                    var title = document.createElement('strong');
                    title.textContent = meeting.title;
                    card.appendChild(title);

                    var meta = document.createElement('p');
                    meta.className = 'subtle';
                    meta.textContent = meeting.date_time + ' · ' + meeting.duration_minutes + ' min · ' + meeting.meeting_type;
                    card.appendChild(meta);

                    var teacher = document.createElement('p');
                    teacher.className = 'subtle';
                    teacher.textContent = 'Lider: ' + (meeting.teacher_name || 'Sin asignar');
                    card.appendChild(teacher);

                    var button = document.createElement('a');
                    button.className = 'btn btn-secondary meeting-open-btn';
                    button.href = meeting.jitsi_room_url;
                    button.target = '_blank';
                    button.rel = 'noopener';
                    button.textContent = 'Abrir reunion';
                    card.appendChild(button);

                    modalBody.appendChild(card);
                });

                modal.showModal();
            };

            var renderCalendar = function () {
                calendarGrid.innerHTML = '';
                var firstDay = new Date(viewYear, viewMonth, 1);
                var lastDay = new Date(viewYear, viewMonth + 1, 0);
                var startWeekday = firstDay.getDay();
                var totalDays = lastDay.getDate();
                calendarTitle.textContent = firstDay.toLocaleDateString('es-AR', { month: 'long', year: 'numeric' });

                weekdays.forEach(function (day) {
                    var headerCell = document.createElement('div');
                    headerCell.className = 'calendar-day calendar-day--header';
                    headerCell.textContent = day;
                    calendarGrid.appendChild(headerCell);
                });

                for (var i = 0; i < startWeekday; i++) {
                    var empty = document.createElement('div');
                    empty.className = 'calendar-day calendar-day--empty';
                    calendarGrid.appendChild(empty);
                }

                for (var d = 1; d <= totalDays; d++) {
                    var cell = document.createElement('button');
                    cell.type = 'button';
                    cell.className = 'calendar-day';
                    cell.textContent = d;

                    var isToday = d === today.getDate()
                        && viewMonth === today.getMonth()
                        && viewYear === today.getFullYear();
                    if (isToday) {
                        cell.classList.add('calendar-day--today');
                    }

                    var dateKey = formatDateKey(viewYear, viewMonth, d);
                    var dayMeetings = meetingsByDate[dateKey] || [];
                    if (dayMeetings.length) {
                        cell.classList.add('calendar-day--event', 'calendar-day--clickable');
                        cell.title = dayMeetings.map(function (meeting) {
                            return meeting.title;
                        }).join(', ');
                        (function (currentDateKey, currentDayMeetings) {
                            cell.addEventListener('click', function () {
                                openMeetingModal(currentDateKey, currentDayMeetings);
                            });
                        })(dateKey, dayMeetings);
                    } else {
                        cell.classList.add('calendar-day--plain');
                    }

                    calendarGrid.appendChild(cell);
                }
            };

            prevButton.addEventListener('click', function () {
                viewMonth -= 1;
                if (viewMonth < 0) {
                    viewMonth = 11;
                    viewYear -= 1;
                }
                renderCalendar();
            });

            nextButton.addEventListener('click', function () {
                viewMonth += 1;
                if (viewMonth > 11) {
                    viewMonth = 0;
                    viewYear += 1;
                }
                renderCalendar();
            });

            renderCalendar();
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
                    countdown.textContent = 'Reunion en contenido de ayuda';
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
