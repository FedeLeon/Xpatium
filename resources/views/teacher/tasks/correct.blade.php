@extends('layouts.app')

@section('content')
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
            <h3>Correccion</h3>
        </div>
        <p class="subtle">La correccion estara disponible en una siguiente etapa.</p>
    </div>
@endsection
