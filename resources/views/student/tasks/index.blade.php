@extends('layouts.app')

@section('content')
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
                Listado de tareas
            </h1>
            <p>Tareas que ya entregaste.</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('student.tasks.pending') }}">Ver pendientes</a>
    </div>

    <div class="widget widget-wide">
        <div class="widget-header">
            <h3>Entregadas</h3>
        </div>
        @if ($tasks->isEmpty())
            <p class="subtle">Todavia no entregaste tareas.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Titulo</th>
                        <th>Tipo</th>
                        <th>Entrega</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                        @php
                            $submission = $task->submissions->first();
                        @endphp
                        <tr>
                            <td>{{ $task->title }}</td>
                            <td>{{ $task->task_type === 'mixed' ? 'Mixta' : 'Entrega de archivo' }}</td>
                            <td>{{ $submission?->submitted_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
