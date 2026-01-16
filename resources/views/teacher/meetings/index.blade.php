@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1>Reuniones</h1>
            <p>Agenda reuniones individuales o grupales con tus alumnos.</p>
            <button class="btn btn-icon meeting-new-btn" type="button" onclick="document.getElementById('meetingModal').showModal()">
                <span aria-hidden="true">+</span>
                Nueva reunion
            </button>
        </div>
    </div>

    <div class="widget widget-wide">
        <div class="widget-header">
            <h3>Reuniones programadas</h3>
        </div>
        @if ($meetings->isEmpty())
            <p class="subtle">No hay reuniones agendadas.</p>
        @else
            <div class="meeting-grid-table">
                <div class="meeting-grid-row meeting-grid-head">
                    <div>Titulo</div>
                    <div>Fecha</div>
                    <div>Tipo</div>
                    <div>Alumnos</div>
                    <div>Acciones</div>
                </div>
                @foreach ($meetings as $meeting)
                    @php
                        $attendeeNames = $meeting->attendees
                            ->map(fn ($attendee) => trim($attendee->name.' '.$attendee->last_name))
                            ->implode(', ');
                    @endphp
                    <div class="meeting-grid-row">
                        <div class="meeting-grid-title">{{ $meeting->title }}</div>
                        <div>{{ $meeting->scheduled_for->format('d/m/Y H:i') }}</div>
                        <div>{{ $meeting->meeting_type === 'individual' ? 'Individual' : 'Grupal' }}</div>
                        <div class="meeting-grid-attendees">{{ $attendeeNames ?: 'Sin alumnos' }}</div>
                        <div class="meeting-grid-actions">
                            <a class="btn btn-secondary" href="{{ $meeting->daily_room_url }}" target="_blank" rel="noopener">
                                Abrir reunion
                            </a>
                            <form method="POST" action="{{ route('teacher.meetings.destroy', $meeting) }}"
                                  data-confirm="Se eliminara la reunion y no podras recuperarla.">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-secondary" type="submit">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @php
        $selectedStudents = old('student_ids', []);
        $hasStudents = $students->isNotEmpty();
    @endphp

    <dialog id="meetingModal">
        <div class="modal-header">
            <strong>Nueva reunion</strong>
            <button class="btn btn-secondary" type="button" onclick="document.getElementById('meetingModal').close()">Cerrar</button>
        </div>
        <div class="modal-body">
            @if ($errors->any())
                <div class="alert">
                    <strong>Revisa los datos:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form id="meeting-form" method="POST" action="{{ route('teacher.meetings.store') }}" class="form-grid">
                @csrf
                <div class="form-field">
                    <label for="meeting-title">Titulo</label>
                    <input id="meeting-title" name="title" type="text" value="{{ old('title') }}" required>
                </div>
                <div class="form-field">
                    <label for="meeting-description">Descripcion</label>
                    <textarea id="meeting-description" name="description" rows="3">{{ old('description') }}</textarea>
                </div>
                <div class="form-field">
                    <label for="meeting-type">Tipo</label>
                    <select id="meeting-type" name="meeting_type" required>
                        <option value="individual" {{ old('meeting_type') === 'individual' ? 'selected' : '' }}>Individual</option>
                        <option value="group" {{ old('meeting_type') === 'group' ? 'selected' : '' }}>Grupal</option>
                    </select>
                </div>
                <div class="form-field">
                    <label for="meeting-date">Fecha</label>
                    <input id="meeting-date" name="scheduled_date" type="date" value="{{ old('scheduled_date') }}" required>
                </div>
                <div class="form-field">
                    <label for="meeting-time">Hora</label>
                    <input id="meeting-time" name="scheduled_time" type="time" value="{{ old('scheduled_time') }}" required>
                </div>
                <div class="form-field">
                    <label for="meeting-duration">Duracion (minutos)</label>
                    <input id="meeting-duration" name="duration_minutes" type="number" min="15" max="480" value="{{ old('duration_minutes', 60) }}" required>
                </div>
                <div class="form-field form-field--wide">
                    <label>Alumnos</label>
                    @if (! $hasStudents)
                        <p class="subtle">No hay alumnos activos para asignar.</p>
                    @else
                        <div class="meeting-student-actions">
                            <button class="btn btn-secondary btn-icon" type="button"
                                    onclick="document.getElementById('studentPickerModal').showModal()">
                                <span aria-hidden="true">+</span>
                                Elegir alumnos
                            </button>
                            <span id="student-selection-count" class="subtle">0 seleccionados</span>
                        </div>
                    @endif
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" type="button" onclick="document.getElementById('meetingModal').close()">Cancelar</button>
            <button class="btn" type="submit" form="meeting-form" {{ $hasStudents ? '' : 'disabled' }}>Crear reunion</button>
        </div>
    </dialog>

    <dialog id="studentPickerModal">
        <div class="modal-header">
            <strong>Seleccionar alumnos</strong>
            <button class="btn btn-secondary" type="button" onclick="document.getElementById('studentPickerModal').close()">Cerrar</button>
        </div>
        <div class="modal-body">
            @if (! $hasStudents)
                <p class="subtle">No hay alumnos activos para asignar.</p>
            @else
                <div class="meeting-students">
                    @foreach ($students as $student)
                        @php
                            $studentUser = $student->student;
                            $fullName = trim($studentUser->name.' '.$studentUser->last_name);
                            $avatarUrl = $studentUser->avatar_path ? asset('storage/' . $studentUser->avatar_path) : null;
                            $initials = strtoupper(substr($studentUser->name, 0, 1)).strtoupper(substr($studentUser->last_name ?? '', 0, 1));
                        @endphp
                        <label class="meeting-student {{ in_array($student->student_id, $selectedStudents, true) ? 'is-selected' : '' }}">
                            <input type="checkbox" form="meeting-form" name="student_ids[]" value="{{ $student->student_id }}"
                                {{ in_array($student->student_id, $selectedStudents, true) ? 'checked' : '' }}>
                            <span class="chat-avatar meeting-student__avatar">
                                @if ($avatarUrl)
                                    <img src="{{ $avatarUrl }}" alt="{{ $fullName }}">
                                @else
                                    {{ $initials }}
                                @endif
                            </span>
                            <span class="meeting-student__name">{{ $fullName }}</span>
                        </label>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" type="button" onclick="document.getElementById('studentPickerModal').close()">Listo</button>
        </div>
    </dialog>

    <script>
        (function () {
            var countEl = document.getElementById('student-selection-count');
            var studentInputs = document.querySelectorAll('.meeting-student input[type="checkbox"]');
            if (!countEl || !studentInputs.length) {
                return;
            }

            var updateCount = function () {
                var count = 0;
                studentInputs.forEach(function (input) {
                    if (input.checked) {
                        count += 1;
                    }
                    input.closest('.meeting-student').classList.toggle('is-selected', input.checked);
                });
                countEl.textContent = count + ' seleccionados';
            };

            studentInputs.forEach(function (input) {
                input.addEventListener('change', updateCount);
            });

            updateCount();

            @if ($errors->any())
                document.getElementById('meetingModal').showModal();
            @endif
        })();
    </script>
@endsection
