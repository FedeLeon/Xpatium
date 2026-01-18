@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1>{{ $course->name }}</h1>
            <p>{{ $course->subtitle }}</p>
        </div>
    </div>

    <div class="card card-narrow">
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
    </div>

    <div class="course-actions">
        <a class="course-action" href="{{ route('teacher.courses.index') }}">
            <span class="course-action__icon">↩️</span>
            <span class="course-action__label">Volver</span>
        </a>
        <a class="course-action" href="{{ route('teacher.courses.edit', $course) }}">
            <span class="course-action__icon">✏️</span>
            <span class="course-action__label">Editar curso</span>
        </a>
        <a class="course-action" href="{{ route('teacher.courses.modules.index', $course) }}">
            <span class="course-action__icon">🧩</span>
            <span class="course-action__label">Modulos y lecciones</span>
        </a>
        <a class="course-action" href="{{ route('courses.landing', $course) }}" target="_blank" rel="noopener">
            <span class="course-action__icon">🌐</span>
            <span class="course-action__label">Ver landing</span>
        </a>
        <form method="POST" action="{{ route('teacher.courses.destroy', $course) }}" data-confirm="Se eliminara el curso y todo su contenido.">
            @csrf
            @method('DELETE')
            <button class="course-action course-action--danger" type="submit">
                <span class="course-action__icon">🗑️</span>
                <span class="course-action__label">Eliminar</span>
            </button>
        </form>
    </div>

@endsection
