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
                <span class="subtle">Navega meses y consulta reuniones por dia.</span>
            </div>
            <div class="calendar teacher-calendar">
                <div class="calendar-controls">
                    <button class="calendar-nav-btn" id="calendar-prev" type="button" aria-label="Mes anterior">‹</button>
                    <div class="calendar-header" id="calendar-title">Mes</div>
                    <button class="calendar-nav-btn" id="calendar-next" type="button" aria-label="Mes siguiente">›</button>
                </div>
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
                            <a class="btn btn-secondary meeting-open-btn" href="{{ $meeting->jitsi_room_url }}" target="_blank" rel="noopener">
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

    <dialog id="calendarMeetingModal">
        <div class="modal-header">
            <strong id="calendar-modal-title">Reuniones</strong>
            <button class="btn btn-secondary" type="button" onclick="document.getElementById('calendarMeetingModal').close()">Cerrar</button>
        </div>
        <div class="modal-body" id="calendar-modal-body">
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
                'attendees' => $meeting->attendees
                    ->map(fn ($attendee) => trim($attendee->name . ' ' . $attendee->last_name))
                    ->filter()
                    ->values(),
            ];
        })->values();
    @endphp

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
            var prevButton = document.getElementById('calendar-prev');
            var nextButton = document.getElementById('calendar-next');
            var modal = document.getElementById('calendarMeetingModal');
            var modalTitle = document.getElementById('calendar-modal-title');
            var modalBody = document.getElementById('calendar-modal-body');
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

                    var attendees = document.createElement('p');
                    attendees.className = 'subtle';
                    attendees.textContent = 'Usuarios: ' + (meeting.attendees.length ? meeting.attendees.join(', ') : 'Sin usuarios');
                    card.appendChild(attendees);

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
@endsection
