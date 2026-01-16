@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1>Reuniones</h1>
            <p>Proximas reuniones programadas por tu profesor.</p>
        </div>
    </div>

    <div class="widget widget-wide">
        <div class="widget-header">
            <h3>Agenda</h3>
        </div>

        @if ($meetings->isEmpty())
            <p class="subtle">No tienes reuniones agendadas.</p>
        @else
            <div class="list">
                @foreach ($meetings as $meeting)
                    @php
                        $startAt = $meeting->scheduled_for;
                        $joinAt = $meeting->join_available_at;
                        $endAt = $meeting->meeting_end_at;
                    @endphp
                    <div class="card meeting-card">
                        <div>
                            <strong>{{ $meeting->title }}</strong>
                            <div class="subtle">
                                {{ $startAt->format('d/m/Y H:i') }} · {{ $meeting->duration_minutes }} min
                            </div>
                            <div class="subtle">
                                Profesor: {{ $meeting->teacher?->name }} {{ $meeting->teacher?->last_name }}
                            </div>
                            <div class="subtle">
                                Disponible desde {{ $joinAt->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        <a class="btn btn-secondary meeting-join {{ $meeting->join_available ? '' : 'is-disabled' }}"
                           href="{{ $meeting->daily_room_url }}"
                           target="_blank"
                           rel="noopener"
                           data-meeting-join
                           data-meeting-start="{{ $startAt->timestamp }}"
                           data-meeting-end="{{ $endAt->timestamp }}"
                           aria-disabled="{{ $meeting->join_available ? 'false' : 'true' }}">
                            Abrir en nueva pestaña
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <script>
        (function () {
            var buttons = document.querySelectorAll('[data-meeting-join]');
            if (!buttons.length) {
                return;
            }

            var toggleButtons = function () {
                var now = Math.floor(Date.now() / 1000);
                buttons.forEach(function (button) {
                    var start = Number(button.getAttribute('data-meeting-start')) || 0;
                    var end = Number(button.getAttribute('data-meeting-end')) || 0;
                    var joinAt = start - (15 * 60);
                    var canJoin = now >= joinAt && now <= end;
                    button.classList.toggle('is-disabled', !canJoin);
                    button.setAttribute('aria-disabled', canJoin ? 'false' : 'true');
                });
            };

            buttons.forEach(function (button) {
                button.addEventListener('click', function (event) {
                    if (button.classList.contains('is-disabled')) {
                        event.preventDefault();
                    }
                });
            });

            toggleButtons();
            setInterval(toggleButtons, 60000);
        })();
    </script>
@endsection
