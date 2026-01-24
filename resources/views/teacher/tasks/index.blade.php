@extends('layouts.app')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/teacher-students.css') }}">
    @endpush

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
                Tareas
            </h1>
            <p>Gestiona las tareas asignadas a tus alumnos.</p>
        </div>
        <a class="btn btn-action" href="{{ route('teacher.tasks.create') }}">Crear tarea</a>
    </div>

    <div class="widget widget-wide">
        <div class="widget-header">
            <h3>Listado de tareas</h3>
        </div>
        @if ($tasks->isEmpty())
            <p class="subtle">Todavia no hay tareas creadas.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Titulo</th>
                        <th>Tipo</th>
                        <th>Descripcion</th>
                        <th>Alumnos</th>
                        <th>Comision</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                        @php
                            $typeLabel = match ($task->task_type) {
                                'file_delivery' => 'Entrega de archivo',
                                'mixed' => 'Mixta',
                                default => $task->task_type,
                            };
                        @endphp
                        <tr>
                            <td>{{ $task->title }}</td>
                            <td>{{ $typeLabel }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($task->description ?? '—', 80) }}</td>
                            <td>
                                @if ($task->students_count > 0)
                                    <button class="btn btn-secondary btn-icon" type="button"
                                            onclick="document.getElementById('taskStudentsModal-{{ $task->id }}').showModal()">
                                        {{ $task->students_count }}
                                    </button>
                                @else
                                    <span class="subtle">0</span>
                                @endif
                            </td>
                            <td>{{ $task->commission?->name ?? '—' }}</td>
                            <td>
                                <div class="student-actions">
                                    <a class="btn btn-secondary btn-icon" href="{{ route('teacher.tasks.show', $task) }}">
                                        Ver detalle
                                    </a>
                                    <a class="btn btn-secondary btn-icon" href="{{ route('teacher.tasks.correct', $task) }}">
                                        Corregir
                                    </a>
                                    <form method="POST" action="{{ route('teacher.tasks.destroy', $task) }}"
                                          data-confirm="Se eliminara la tarea y su contenido.">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-icon" type="submit">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @foreach ($tasks as $task)
        <dialog id="taskStudentsModal-{{ $task->id }}">
            <div class="modal-header">
                <strong>Alumnos de {{ $task->title }}</strong>
                <button class="btn btn-secondary" type="button"
                        onclick="document.getElementById('taskStudentsModal-{{ $task->id }}').close()">Cerrar</button>
            </div>
            <div class="modal-body">
                @if ($task->students->isEmpty())
                    <p class="subtle">No hay alumnos asignados.</p>
                @else
                    <div class="commission-students__list">
                        @foreach ($task->students as $student)
                            <div class="commission-students__item">
                                {{ $student->name }} {{ $student->last_name }}
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </dialog>
    @endforeach
@endsection
