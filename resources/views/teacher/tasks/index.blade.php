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
        <p class="subtle">Todavia no hay tareas creadas.</p>
    </div>
@endsection
