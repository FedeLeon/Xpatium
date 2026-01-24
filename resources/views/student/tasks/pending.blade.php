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
                Tareas pendientes
            </h1>
            <p>Revisa las tareas que aun debes entregar.</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('student.tasks.index') }}">Ver tareas entregadas</a>
    </div>

    <div class="widget widget-wide">
        <div class="widget-header">
            <h3>Pendientes</h3>
        </div>
        @if ($tasks->isEmpty())
            <p class="subtle">No tienes tareas pendientes.</p>
        @else
            <div class="dashboard-grid">
                @foreach ($tasks as $task)
                    <div class="card">
                        <div class="actions actions-tight">
                            <strong>{{ $task->title }}</strong>
                            <span class="badge">{{ $task->task_type === 'mixed' ? 'Mixta' : 'Entrega de archivo' }}</span>
                        </div>
                        <p>{{ \Illuminate\Support\Str::limit($task->description ?? 'Sin descripcion.', 120) }}</p>
                        @if ($task->due_date)
                            <p class="subtle">Vence: {{ $task->due_date->format('d/m/Y') }}</p>
                        @endif
                        <div class="actions">
                            <div></div>
                            <button class="btn btn-secondary" type="button" disabled>Entrega pendiente</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
