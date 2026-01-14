@extends('layouts.app')

@section('content')
    <h1>Bienvenido, {{ auth()->user()->name }} {{ auth()->user()->last_name }}</h1>
    <p>Este es tu espacio de aprendizaje. Continua con tus cursos activos.</p>

    <div class="card card-spaced">
        <div class="section-head">
            <div>
                <h3>Cursos con acceso</h3>
                <p class="subtle">Total de cursos habilitados para vos.</p>
            </div>
            <a class="btn btn-secondary" href="{{ route('student.courses.index') }}">Ver todos</a>
        </div>

        <div class="stat-card">
            <div>
                <span class="stat-label">Cursos activos</span>
                <span class="stat-value">{{ $courses->count() }}</span>
            </div>
            <span class="badge badge-warn">En progreso</span>
        </div>
    </div>
@endsection
