@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1>{{ $course->name }}</h1>
            <p>{{ $course->subtitle }}</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('student.courses.index') }}">Volver</a>
    </div>

    @if ($course->modules->isEmpty())
        <p>No hay modulos todavia.</p>
    @else
        <div class="list">
            @foreach ($course->modules as $module)
                <div class="card">
                    <div>
                        <h3>{{ $module->title }}</h3>
                        <p>{{ $module->subtitle }}</p>
                        <p class="subtle">{{ $module->description }}</p>
                    </div>
                    @if ($module->lessons->isEmpty())
                        <p class="subtle">Sin lecciones.</p>
                    @else
                        <div class="list">
                            @foreach ($module->lessons as $lesson)
                                <div class="asset-item">
                                    <span>{{ $lesson->title }}</span>
                                    <a class="btn btn-secondary" href="{{ route('student.lessons.show', $lesson) }}">Ver leccion</a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
@endsection
