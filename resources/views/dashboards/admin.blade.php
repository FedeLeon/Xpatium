@extends('layouts.app')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    @endpush

    <div class="actions">
        <div>
            <h1>Panel de admin</h1>
            <p>Gestiona lideres, usuarios y contenidos de ayuda desde aqui.</p>
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
                    <span class="admin-action__label">Contenidos de ayuda</span>
                </button>
                <button class="admin-action">
                    <span class="admin-action__icon">👩‍🏫</span>
                    <span class="admin-action__label">Lideres</span>
                </button>
                <button class="admin-action">
                    <span class="admin-action__icon">🎓</span>
                    <span class="admin-action__label">Usuarios</span>
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
                    <span class="metric-label">Contenidos de ayuda activos</span>
                    <strong class="metric-value">--</strong>
                </div>
                <div class="metric">
                    <span class="metric-label">Lideres</span>
                    <strong class="metric-value">--</strong>
                </div>
                <div class="metric">
                    <span class="metric-label">Usuarios</span>
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
                <h3>Contenidos de ayuda destacados</h3>
                <span class="info-bubble">Proximamente listado real.</span>
            </div>
            <div class="admin-course">
                <div>
                    <h3>Contenido de ayuda de ejemplo</h3>
                    <p class="subtle">Esta seccion mostrara informacion del contenido de ayuda seleccionado.</p>
                </div>
                <div class="admin-course__actions">
                    <button class="btn btn-secondary">Ver contenido de ayuda</button>
                    <button class="btn">Gestionar</button>
                </div>
            </div>
        </div>

    </div>
@endsection
