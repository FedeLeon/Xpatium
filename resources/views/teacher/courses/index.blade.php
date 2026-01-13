@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1>Cursos</h1>
            <p>Gestiona tus cursos publicados.</p>
        </div>
        <a class="btn" href="{{ route('teacher.courses.create') }}">Crear curso</a>
    </div>

    @if ($courses->isEmpty())
        <p>No tenes cursos todavia. Crea el primero.</p>
    @else
        <div class="grid">
            @foreach ($courses as $course)
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
                    <a class="btn btn-secondary" href="{{ route('teacher.courses.show', $course) }}">Ir al dashboard</a>
                </div>
            @endforeach
        </div>
    @endif
@endsection
