@extends('layouts.app')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    @endpush

    <div class="actions">
        <div>
            <h1>Panel de admin</h1>
            <p>Gestiona profesores, alumnos y cursos desde aqui.</p>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="widget widget-wide">
            <div class="widget-header">
                <h3>Acciones rapidas</h3>
            </div>
            <div class="admin-actions">
                <button class="admin-action">
                    <span class="admin-action__icon">📚</span>
                    <span class="admin-action__label">Cursos</span>
                </button>
                <button class="admin-action">
                    <span class="admin-action__icon">👩‍🏫</span>
                    <span class="admin-action__label">Profesores</span>
                </button>
                <button class="admin-action">
                    <span class="admin-action__icon">🎓</span>
                    <span class="admin-action__label">Alumnos</span>
                </button>
                <button class="admin-action">
                    <span class="admin-action__icon">🧾</span>
                    <span class="admin-action__label">Reportes</span>
                </button>
            </div>
        </div>

        <div class="widget">
            <div class="widget-header">
                <h3>Resumen</h3>
            </div>
            <div class="metric-grid">
                <div class="metric">
                    <span class="metric-label">Cursos activos</span>
                    <strong class="metric-value">--</strong>
                </div>
                <div class="metric">
                    <span class="metric-label">Profesores</span>
                    <strong class="metric-value">--</strong>
                </div>
                <div class="metric">
                    <span class="metric-label">Alumnos</span>
                    <strong class="metric-value">--</strong>
                </div>
                <div class="metric">
                    <span class="metric-label">Ingresos</span>
                    <strong class="metric-value">--</strong>
                </div>
            </div>
        </div>

        <div class="widget widget-wide">
            <div class="widget-header">
                <h3>Cursos destacados</h3>
                <span class="info-bubble">Proximamente listado real.</span>
            </div>
            <div class="admin-course">
                <div>
                    <h3>Curso de ejemplo</h3>
                    <p class="subtle">Esta seccion mostrara informacion del curso seleccionado.</p>
                </div>
                <div class="admin-course__actions">
                    <button class="btn btn-secondary">Ver curso</button>
                    <button class="btn">Gestionar</button>
                </div>
            </div>
        </div>

    </div>
@endsection
