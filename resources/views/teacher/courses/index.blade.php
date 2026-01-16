@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1 class="view-title">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                </svg>
                Cursos
            </h1>
            <p>Gestiona tus cursos publicados.</p>
        </div>
        <a class="btn btn-action" href="{{ route('teacher.courses.create') }}">Crear curso</a>
    </div>

    @if ($courses->isEmpty())
        <p>No tenes cursos todavia. Crea el primero.</p>
    @else
        <div class="courses-grid">
            @foreach ($courses as $course)
                <article class="course-card">
                    <div class="course-card__media">
                        @if ($course->image_path)
                            <img class="course-card__image" src="{{ asset('storage/' . $course->image_path) }}" alt="Imagen del curso">
                        @else
                            <div class="course-card__placeholder">Sin imagen</div>
                        @endif
                    </div>
                    <div class="course-card__body">
                        <div>
                            <h3>{{ $course->name }}</h3>
                            @if ($course->subtitle)
                                <p class="course-card__subtitle">{{ $course->subtitle }}</p>
                            @else
                                <p class="course-card__subtitle course-card__subtitle--empty">Sin subtitulo</p>
                            @endif
                        </div>
                        <div class="course-card__meta">
                            <div class="meta-item">
                                <span class="meta-label">Modulos</span>
                                <span class="meta-value">{{ $course->modules_count }}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Lecciones</span>
                                <span class="meta-value">{{ $course->lessons_count }}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Duracion</span>
                                <span class="meta-value">Sin datos</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Inicio</span>
                                <span class="meta-value">
                                    {{ $course->start_date ? $course->start_date->format('d/m/Y') : 'Sin fecha' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="course-card__actions">
                        <a class="btn btn-secondary" href="{{ route('teacher.courses.show', $course) }}">Ir al dashboard</a>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection
