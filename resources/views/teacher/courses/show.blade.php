@extends('layouts.app')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/teacher-course-dashboard.css') }}">
    @endpush
    <div class="actions">
        <div>
            <h1>{{ $course->name }}</h1>
            <p>{{ $course->subtitle }}</p>
        </div>
    </div>

    <div class="course-dashboard">
        <div class="course-dashboard__details">
            <div class="card">
                @if ($course->image_path)
                    <img src="{{ asset('storage/' . $course->image_path) }}" alt="Imagen del curso">
                @else
                    <div class="placeholder">Sin imagen</div>
                @endif
                <div>
                    <h3>Descripcion</h3>
                    <p>{{ $course->description }}</p>
                </div>
                <div>
                    <h3>Precios</h3>
                    <p>ARS {{ number_format($course->price_ars, 2) }} / USD {{ number_format($course->price_usd, 2) }}</p>
                </div>
                <div>
                    <h3>Fecha de inicio</h3>
                    <p>{{ $course->start_date?->format('d/m/Y') ?? 'Sin fecha' }}</p>
                </div>
                <div>
                    <h3>Duracion</h3>
                    <p>{{ $course->duration ?: 'Sin datos' }}</p>
                </div>
                <div>
                    <h3>Nivel</h3>
                    <p>{{ $course->level ? ucfirst($course->level) : 'Sin datos' }}</p>
                </div>
            </div>
            <a class="btn btn-secondary course-dashboard__back" href="{{ route('teacher.courses.index') }}">Volver</a>
        </div>

        <aside class="card course-dashboard__tools">
            <h3>Herramientas</h3>
            <div class="course-actions">
                <a class="course-action" href="{{ route('teacher.courses.edit', $course) }}">
                    <span class="course-action__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"></path>
                        </svg>
                    </span>
                    <span class="course-action__label">Editar curso</span>
                </a>
                <a class="course-action" href="{{ route('teacher.courses.modules.index', $course) }}">
                    <span class="course-action__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                            <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                            <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                            <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                        </svg>
                    </span>
                    <span class="course-action__label">Modulos y lecciones</span>
                </a>
                <a class="course-action" href="{{ route('courses.landing', $course) }}" target="_blank" rel="noopener">
                    <span class="course-action__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M3 12h18"></path>
                            <path d="M12 3a14 14 0 0 1 0 18a14 14 0 0 1 0-18"></path>
                        </svg>
                    </span>
                    <span class="course-action__label">Ver landing</span>
                </a>
                <form method="POST" action="{{ route('teacher.courses.destroy', $course) }}" data-confirm="Se eliminara el curso y todo su contenido.">
                    @csrf
                    @method('DELETE')
                    <button class="course-action course-action--danger" type="submit">
                        <span class="course-action__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <path d="M3 6h18"></path>
                                <path d="M8 6V4h8v2"></path>
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                <path d="M10 11v6"></path>
                                <path d="M14 11v6"></path>
                            </svg>
                        </span>
                        <span class="course-action__label">Eliminar</span>
                    </button>
                </form>
            </div>
        </aside>
    </div>

@endsection
