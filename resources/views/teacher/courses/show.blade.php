@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1>{{ $course->name }}</h1>
            <p>{{ $course->subtitle }}</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('teacher.courses.index') }}">Volver</a>
    </div>

    <div class="card" style="max-width: 680px;">
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
@endsection
