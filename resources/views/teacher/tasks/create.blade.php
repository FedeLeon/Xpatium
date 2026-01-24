@extends('layouts.app')

@section('content')
    @php
        $isEdit = isset($task);
    @endphp
    <div class="actions">
        <div>
            <h1 class="view-title">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <path d="M14 2v6h6"></path>
                    <path d="M16 13H8"></path>
                    <path d="M16 17H8"></path>
                    <path d="M10 9H8"></path>
                </svg>
                {{ $isEdit ? 'Editar tarea' : 'Crear tarea' }}
            </h1>
            <p>{{ $isEdit ? 'Ajusta la estructura antes de cargar el contenido.' : 'Prepara una nueva tarea para tus alumnos.' }}</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('teacher.tasks.index') }}">Lista de tareas</a>
    </div>

    <div class="widget widget-wide">
        <div class="widget-header">
            <h3>Detalle de la tarea</h3>
        </div>
        <form class="form-grid form-grid-full" id="task-form" method="POST"
              action="{{ $isEdit ? route('teacher.tasks.update', $task) : route('teacher.tasks.store') }}">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif
            <div>
                <label for="task-title">Titulo de la tarea</label>
                <input id="task-title" name="title" type="text" placeholder="Ej: Ejercicios de clase 3"
                       value="{{ old('title', $task->title ?? '') }}" required>
            </div>
            <div>
                <label for="task-description">Descripcion</label>
                <textarea id="task-description" name="description" rows="4" placeholder="Indicaciones para la tarea">{{ old('description', $task->description ?? '') }}</textarea>
            </div>
            <div>
                <label>Asignar a</label>
                <div class="checkbox-grid" id="task-scope-toggle">
                    <label class="checkbox">
                        <input type="checkbox" id="assign-students" name="assign_scope" value="students">
                        Alumnos
                    </label>
                    <label class="checkbox">
                        <input type="checkbox" id="assign-commissions" name="assign_scope" value="commissions">
                        Comisiones
                    </label>
                </div>
            </div>
            <div id="students-select" hidden>
                <label>Selecciona alumnos</label>
                @if ($students->isEmpty())
                    <p class="subtle">No tienes alumnos registrados.</p>
                @else
                    <div class="checkbox-grid">
                        @foreach ($students as $record)
                            @php
                                $studentUser = $record->student;
                                $fullName = trim(($studentUser->name ?? '').' '.($studentUser->last_name ?? ''));
                                $isAssigned = isset($task) && $task->students->contains('id', $record->student_id);
                            @endphp
                            <label class="checkbox">
                                <input type="checkbox" name="student_ids[]" value="{{ $record->student_id }}"
                                       {{ $isAssigned ? 'checked' : '' }} disabled>
                                {{ $fullName ?: 'Alumno sin nombre' }}
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>
            <div id="commissions-select" hidden>
                <label>Selecciona comision</label>
                @if ($commissions->isEmpty())
                    <p class="subtle">No tienes comisiones creadas.</p>
                @else
                    <div class="checkbox-grid">
                        @foreach ($commissions as $commission)
                            <label class="checkbox">
                                <input type="radio" name="commission_id" value="{{ $commission->id }}"
                                       {{ (isset($task) && $task->commission_id === $commission->id) ? 'checked' : '' }} disabled>
                                {{ $commission->name }} ({{ $commission->students_count }})
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>
            <div>
                <label for="task-due">Fecha limite</label>
                <input id="task-due" name="due_date" type="date" value="{{ old('due_date', optional($task->due_date ?? null)->format('Y-m-d')) }}">
            </div>
            <div>
                <label for="task-type">Tipo de tarea</label>
                <select id="task-type" name="task_type" required>
                    <option value="">Selecciona un tipo</option>
                    <option value="file_delivery" @selected(old('task_type', $task->task_type ?? '') === 'file_delivery')>Entrega de archivo</option>
                    <option value="mixed" @selected(old('task_type', $task->task_type ?? '') === 'mixed')>Mixta</option>
                </select>
            </div>
            <div>
                <label for="grading-mode">Modo de calificacion</label>
                <select id="grading-mode" name="grading_mode" required>
                    <option value="feedback" @selected(old('grading_mode', $task->grading_mode ?? 'feedback') === 'feedback')>Solo feedback</option>
                    <option value="score" @selected(old('grading_mode', $task->grading_mode ?? 'feedback') === 'score')>Calificacion numerica</option>
                    <option value="both" @selected(old('grading_mode', $task->grading_mode ?? 'feedback') === 'both')>Feedback + calificacion</option>
                </select>
            </div>
            <div class="form-actions">
                <button class="btn btn-primary" type="submit">{{ $isEdit ? 'Guardar y continuar' : 'Crear tarea' }}</button>
            </div>
        </form>
    </div>

    <script>
        (function () {
            var studentsToggle = document.getElementById('assign-students');
            var commissionsToggle = document.getElementById('assign-commissions');
            var studentsPanel = document.getElementById('students-select');
            var commissionsPanel = document.getElementById('commissions-select');
            if (!studentsToggle || !commissionsToggle || !studentsPanel || !commissionsPanel) {
                return;
            }

            var togglePanels = function (active) {
                var isStudents = active === 'students';
                studentsPanel.hidden = !isStudents;
                commissionsPanel.hidden = isStudents;

                studentsPanel.querySelectorAll('input[type="checkbox"]').forEach(function (input) {
                    input.disabled = !isStudents;
                });
                commissionsPanel.querySelectorAll('input[type="radio"]').forEach(function (input) {
                    input.disabled = isStudents;
                });
            };

            studentsToggle.addEventListener('change', function () {
                if (studentsToggle.checked) {
                    commissionsToggle.checked = false;
                    togglePanels('students');
                } else {
                    studentsPanel.hidden = true;
                    studentsPanel.querySelectorAll('input[type="checkbox"]').forEach(function (input) {
                        input.disabled = true;
                        input.checked = false;
                    });
                }
            });

            commissionsToggle.addEventListener('change', function () {
                if (commissionsToggle.checked) {
                    studentsToggle.checked = false;
                    togglePanels('commissions');
                } else {
                    commissionsPanel.hidden = true;
                    commissionsPanel.querySelectorAll('input[type="radio"]').forEach(function (input) {
                        input.disabled = true;
                        input.checked = false;
                    });
                }
            });

            var hasStudentsSelected = studentsPanel.querySelector('input[type="checkbox"]:checked');
            var hasCommissionSelected = commissionsPanel.querySelector('input[type="radio"]:checked');
            if (hasCommissionSelected) {
                commissionsToggle.checked = true;
                togglePanels('commissions');
            } else if (hasStudentsSelected) {
                studentsToggle.checked = true;
                togglePanels('students');
            }
        })();
    </script>
@endsection
