@extends('layouts.app')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/teacher-students.css') }}">
    @endpush

    <div class="actions">
        <div>
            <h1 class="view-title">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M17 21v-1a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v1"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-1a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Comision
            </h1>
            <p>Administra las comisiones de alumnos.</p>
            <button class="btn btn-action" type="button">
                Crear comision
            </button>
        </div>
    </div>

    <p class="subtle">No hay comisiones creadas.</p>
@endsection
