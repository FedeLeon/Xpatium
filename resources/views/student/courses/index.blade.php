@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1 class="view-title">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                </svg>
                Mis cursos
            </h1>
            <p>Cursos disponibles con tu profesor.</p>
        </div>
    </div>

    @if ($courses->isEmpty())
        <p>No hay cursos disponibles.</p>
    @else
        <div class="courses-grid">
            @foreach ($courses as $course)
                @php
                    $hasAccess = in_array($course->id, $enrolledIds, true);
                @endphp
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
                        @php
                            $totalLessons = (int) ($lessonTotals->get($course->id, 0));
                            $completedLessons = (int) ($completedTotals->get($course->id, 0));
                            $progress = $totalLessons > 0 ? (int) round(($completedLessons / $totalLessons) * 100) : 0;
                        @endphp
                        <div class="course-card__meta">
                            <div class="meta-item">
                                <span class="meta-label">Modulos</span>
                                <span class="meta-value">{{ $course->modules_count ?? 0 }}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Lecciones</span>
                                <span class="meta-value">{{ $totalLessons }}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Progreso</span>
                                <span class="meta-value">{{ $progress }}%</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Completadas</span>
                                <span class="meta-value">{{ $completedLessons }}</span>
                            </div>
                        </div>
                        <div class="course-progress">
                            <div class="progress-meta">
                                <span>Progreso</span>
                                <span>{{ $progress }}%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-bar__fill" style="width: {{ $progress }}%"></div>
                            </div>
                            <span class="progress-count">{{ $completedLessons }} / {{ $totalLessons }} lecciones</span>
                        </div>
                    </div>
                    <div class="course-card__actions">
                        @if ($hasAccess)
                            <a class="btn btn-secondary" href="{{ route('student.courses.show', $course) }}">Ver curso</a>
                        @else
                            <span class="badge">Sin acceso</span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection
