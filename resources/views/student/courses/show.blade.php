@extends('layouts.app')

@section('content')
    <div class="course-hero">
        <div class="course-hero__media">
            @if ($course->image_path)
                <img class="course-hero__image" src="{{ asset('storage/' . $course->image_path) }}" alt="Imagen del contenido de ayuda">
            @else
                <div class="course-hero__placeholder">Sin imagen</div>
            @endif
        </div>
        <div class="course-hero__content">
            <div>
                <h1>{{ $course->name }}</h1>
                @if ($course->subtitle)
                    <p>{{ $course->subtitle }}</p>
                @endif
                <p class="subtle">
                    Duracion: {{ $course->duration ?: 'Sin datos' }} ·
                    Nivel: {{ $course->level ? ucfirst($course->level) : 'Sin datos' }}
                </p>
            </div>
            <a class="btn btn-secondary" href="{{ route('student.courses.index') }}">Volver</a>
        </div>
    </div>

    <div class="card course-progress-card">
        <div class="course-progress course-progress--header">
            <div class="progress-meta">
                <span>Progreso del contenido de ayuda</span>
                <span>{{ $courseProgress }}%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-bar__fill" style="width: {{ $courseProgress }}%"></div>
            </div>
            <span class="progress-count">{{ $completedLessons }} / {{ $totalLessons }} lecciones completadas</span>
        </div>
    </div>

    @if ($course->modules->isEmpty())
        <p>No hay modulos todavia.</p>
    @else
        <div class="list">
            @foreach ($course->modules as $module)
                @php
                    $moduleTotal = $module->lessons->count();
                    $moduleCompleted = $module->lessons->whereIn('id', $completedLessonIdList)->count();
                    $moduleProgress = $moduleTotal > 0 ? (int) round(($moduleCompleted / $moduleTotal) * 100) : 0;
                    $moduleComplete = in_array($module->id, $moduleCompletionIds, true);
                @endphp
                <details class="module-accordion" {{ $moduleComplete ? 'open' : '' }}>
                    <summary class="module-summary">
                        <div class="module-summary__info">
                            <span class="completion-bubble completion-bubble--lg {{ $moduleComplete ? 'is-complete' : '' }}">
                                <span>✓</span>
                            </span>
                            <div>
                                <h3>{{ $module->title }}</h3>
                                <p>{{ $module->subtitle }}</p>
                            </div>
                        </div>
                        <div class="module-actions">
                            <span class="badge">{{ $moduleTotal }} lecciones</span>
                            <span class="module-progress">{{ $moduleProgress }}%</span>
                        </div>
                    </summary>
                    <div class="module-body">
                        <p class="subtle">{{ $module->description }}</p>
                        @if ($module->lessons->isEmpty())
                            <p class="subtle">Sin lecciones.</p>
                        @else
                            <div class="list lesson-list">
                                @foreach ($module->lessons as $lesson)
                                    @php
                                        $lessonDone = in_array($lesson->id, $completedLessonIdList, true);
                                    @endphp
                                    <a class="asset-item lesson-link-row" href="{{ route('student.lessons.show', $lesson) }}">
                                        <div class="lesson-title">
                                            <span class="completion-bubble {{ $lessonDone ? 'is-complete' : '' }}">
                                                <span>✓</span>
                                            </span>
                                            <span>{{ $lesson->title }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </details>
            @endforeach
        </div>
    @endif
@endsection
