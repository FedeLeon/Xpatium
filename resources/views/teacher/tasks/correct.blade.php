@extends('layouts.app')

@section('content')
    @push('styles')
        <style>
            .task-corrections-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 16px;
            }

            .task-correction-card {
                text-align: left;
                display: grid;
                gap: 6px;
                padding: 16px;
                border-radius: 16px;
                border: 1px solid rgba(148, 163, 184, 0.25);
                background: rgba(15, 23, 42, 0.5);
            }

            .task-correction-card.is-clickable {
                cursor: pointer;
            }

            .task-correction-card.status-pending {
                border-color: rgba(248, 113, 113, 0.6);
                background: rgba(239, 68, 68, 0.08);
            }

            .task-correction-card.status-submitted {
                border-color: rgba(251, 191, 36, 0.6);
                background: rgba(234, 179, 8, 0.08);
            }

            .task-correction-card.status-corrected {
                border-color: rgba(34, 197, 94, 0.6);
                background: rgba(22, 163, 74, 0.1);
            }

            .submission-meta {
                font-size: 12px;
                color: rgba(148, 163, 184, 0.9);
            }

            .correction-section {
                display: grid;
                gap: 10px;
                margin-bottom: 18px;
            }

            .correction-answer {
                padding: 10px 12px;
                border-radius: 12px;
                border: 1px solid rgba(148, 163, 184, 0.2);
                background: rgba(15, 23, 42, 0.55);
            }
        </style>
    @endpush

    <div class="actions">
        <div>
            <h1 class="view-title">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 7h18"></path>
                    <path d="M6 7V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2"></path>
                    <path d="M5 7l1 14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-14"></path>
                </svg>
                Corregir tarea
            </h1>
            <p>{{ $task->title }}</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('teacher.tasks.index') }}">Volver</a>
    </div>

    <div class="widget widget-wide">
        <div class="widget-header">
            <h3>Alumnos asignados</h3>
        </div>
        @if ($students->isEmpty())
            <p class="subtle">No hay alumnos asignados.</p>
        @else
            <div class="task-corrections-grid">
                @foreach ($students as $student)
                    @php
                        $submission = $submissions->get($student->id);
                        $hasSubmission = $submission && $submission->submitted_at;
                        $isCorrected = $submission && $submission->corrected_at;
                        $statusLabel = ! $hasSubmission ? 'Pendiente de entrega' : ($isCorrected ? 'Corregida' : 'Pendiente de correccion');
                        $statusClass = ! $hasSubmission ? 'status-pending' : ($isCorrected ? 'status-corrected' : 'status-submitted');
                        $modalId = 'correctionModal-' . $student->id;
                    @endphp
                    @if ($hasSubmission)
                        <button class="task-correction-card {{ $statusClass }} is-clickable" type="button"
                                onclick="document.getElementById('{{ $modalId }}').showModal()">
                            <strong>{{ $student->name }} {{ $student->last_name }}</strong>
                            <span>{{ $statusLabel }}</span>
                            @if ($submission->submitted_at)
                                <span class="submission-meta">Entregado: {{ $submission->submitted_at->format('d/m/Y H:i') }}</span>
                            @endif
                        </button>
                    @else
                        <div class="task-correction-card {{ $statusClass }}">
                            <strong>{{ $student->name }} {{ $student->last_name }}</strong>
                            <span>{{ $statusLabel }}</span>
                            <span class="submission-meta">Sin entrega</span>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    @php
        $taskContentItem = $task->items->firstWhere('type', $task->task_type);
        $taskContent = $taskContentItem?->content ?? [];
        $gradingMode = $task->grading_mode ?? 'feedback';
        $typeLabels = [
            'open_question' => 'Pregunta abierta',
            'multiple_choice' => 'Multiple choice',
        ];
    @endphp

    @foreach ($students as $student)
        @php
            $submission = $submissions->get($student->id);
            if (! $submission || ! $submission->submitted_at) {
                continue;
            }
            $modalId = 'correctionModal-' . $student->id;
            $submissionContent = $submission->content ?? [];
        @endphp
        <dialog id="{{ $modalId }}">
            <div class="modal-header">
                <strong>Correccion · {{ $student->name }} {{ $student->last_name }}</strong>
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('{{ $modalId }}').close()">Cerrar</button>
            </div>
            <form method="POST" action="{{ route('teacher.tasks.submissions.correct', [$task, $submission]) }}">
                @csrf
                <div class="modal-body">
                    <div class="correction-section">
                        <strong>Contenido de la tarea</strong>
                        @if ($task->task_type === 'file_delivery')
                            <div class="correction-answer">
                                {{ $taskContent['instructions'] ?? '—' }}
                            </div>
                        @elseif ($task->task_type === 'mixed')
                            @php
                                $items = $taskContent['items'] ?? [];
                            @endphp
                            @forelse ($items as $index => $item)
                                @php
                                    $itemType = $item['type'] ?? 'open_question';
                                    $itemLabel = $typeLabels[$itemType] ?? $itemType;
                                @endphp
                                <div class="correction-answer">
                                    <div class="actions actions-tight">
                                        <strong>Item {{ $index + 1 }}</strong>
                                        <span class="subtle">{{ $itemLabel }}</span>
                                    </div>
                                    <div>{{ $item['question'] ?? '—' }}</div>
                                </div>
                            @empty
                                <p class="subtle">No hay items cargados.</p>
                            @endforelse
                        @endif
                    </div>
                    <div class="correction-section">
                        <strong>Entrega del alumno</strong>
                        @if ($task->task_type === 'file_delivery')
                            @if ($submission->file_path)
                                <a class="btn btn-secondary" href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" rel="noopener">Ver archivo</a>
                            @else
                                <p class="subtle">Sin archivo adjunto.</p>
                            @endif
                        @elseif ($task->task_type === 'mixed')
                            @php
                                $submissionItems = $submissionContent['items'] ?? [];
                                $taskItems = $taskContent['items'] ?? [];
                            @endphp
                            @forelse ($taskItems as $index => $item)
                                @php
                                    $itemType = $item['type'] ?? 'open_question';
                                    $answer = $submissionItems[$index]['answer'] ?? null;
                                    $selectedIndex = $submissionItems[$index]['selected_option'] ?? null;
                                    $options = $item['options'] ?? [];
                                @endphp
                                <div class="correction-answer">
                                    <strong>Respuesta {{ $index + 1 }}</strong>
                                    @if ($itemType === 'multiple_choice')
                                        @if ($selectedIndex !== null && isset($options[$selectedIndex]))
                                            <div>{{ $options[$selectedIndex] }}</div>
                                        @else
                                            <div class="subtle">Sin respuesta seleccionada.</div>
                                        @endif
                                    @else
                                        <div>{{ $answer ?: 'Sin respuesta.' }}</div>
                                    @endif
                                </div>
                            @empty
                                <p class="subtle">No hay respuestas registradas.</p>
                            @endforelse
                        @endif
                    </div>
                    <div class="correction-section">
                        <strong>Correccion</strong>
                        @if ($gradingMode === 'feedback' || $gradingMode === 'both')
                            <div>
                                <label for="feedback-{{ $student->id }}">Feedback</label>
                                <textarea id="feedback-{{ $student->id }}" name="feedback" rows="4">{{ old('feedback', $submission->feedback) }}</textarea>
                            </div>
                        @endif
                        @if ($gradingMode === 'score' || $gradingMode === 'both')
                            <div>
                                <label for="score-{{ $student->id }}">Calificacion</label>
                                <input id="score-{{ $student->id }}" name="score" type="number" step="0.01" value="{{ old('score', $submission->score) }}">
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" onclick="document.getElementById('{{ $modalId }}').close()">Cancelar</button>
                    <button class="btn" type="submit">Guardar correccion</button>
                </div>
            </form>
        </dialog>
    @endforeach
@endsection
