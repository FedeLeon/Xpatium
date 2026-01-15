@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1>Reuniones</h1>
            <p>Agenda reuniones individuales o grupales con tus alumnos.</p>
        </div>
    </div>

    <div class="dashboard-grid dashboard-grid--teacher">
        <div class="widget widget-wide">
            <div class="widget-header">
                <h3>Crear reunion</h3>
            </div>

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

            <form method="POST" action="{{ route('teacher.meetings.store') }}" class="form-grid">
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
                    @if ($students->isEmpty())
                        <p class="subtle">No hay alumnos activos para asignar.</p>
                    @else
                        <div class="checkbox-grid">
                            @foreach ($students as $student)
                                <label class="checkbox">
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->student_id }}"
                                        {{ in_array($student->student_id, old('student_ids', []), true) ? 'checked' : '' }}>
                                    <span>{{ $student->student->name }} {{ $student->student->last_name }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="form-actions">
                    <button class="btn" type="submit">Crear reunion</button>
                </div>
            </form>
        </div>

        <div class="widget widget-wide">
            <div class="widget-header">
                <h3>Proximas reuniones</h3>
            </div>
            @if ($meetings->isEmpty())
                <p class="subtle">No hay reuniones agendadas.</p>
            @else
                <div class="list">
                    @foreach ($meetings as $meeting)
                        @php
                            $attendeeNames = $meeting->attendees
                                ->map(fn ($attendee) => trim($attendee->name.' '.$attendee->last_name))
                                ->implode(', ');
                        @endphp
                        <div class="card meeting-card">
                            <div>
                                <strong>{{ $meeting->title }}</strong>
                                <div class="subtle">
                                    {{ $meeting->scheduled_for->format('d/m/Y H:i') }} · {{ $meeting->duration_minutes }} min
                                </div>
                                <div class="subtle">
                                    {{ $meeting->meeting_type === 'individual' ? 'Individual' : 'Grupal' }}
                                </div>
                                <div class="subtle">
                                    Alumnos: {{ $attendeeNames ?: 'Sin alumnos' }}
                                </div>
                            </div>
                            <div class="actions actions-tight">
                                <a class="btn btn-secondary" href="{{ route('teacher.meetings.show', $meeting) }}">Entrar</a>
                                <a class="btn btn-secondary" href="{{ $meeting->daily_room_url }}" target="_blank" rel="noopener">
                                    Abrir en nueva pestaña
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
    </div>
@endsection
