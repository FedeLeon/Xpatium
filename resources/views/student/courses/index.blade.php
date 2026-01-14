@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1>Mis cursos</h1>
            <p>Cursos disponibles con tu profesor.</p>
        </div>
    </div>

    @if ($courses->isEmpty())
        <p>No hay cursos disponibles.</p>
    @else
        <div class="grid">
            @foreach ($courses as $course)
                @php
                    $hasAccess = in_array($course->id, $enrolledIds, true);
                @endphp
                <div class="card">
                    @if ($course->image_path)
                        <img src="{{ asset('storage/' . $course->image_path) }}" alt="Imagen del curso">
                    @else
                        <div class="placeholder">Sin imagen</div>
                    @endif
                    <div>
                        <h3>{{ $course->name }}</h3>
                        <p>{{ $course->subtitle }}</p>
                    </div>
                    @php
                        $totalLessons = (int) ($lessonTotals->get($course->id, 0));
                        $completedLessons = (int) ($completedTotals->get($course->id, 0));
                        $progress = $totalLessons > 0 ? (int) round(($completedLessons / $totalLessons) * 100) : 0;
                    @endphp
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
                    @if ($hasAccess)
                        <a class="btn btn-secondary" href="{{ route('student.courses.show', $course) }}">Ver curso</a>
                    @else
                        <span class="badge">Sin acceso</span>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
@endsection
