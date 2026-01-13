@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1>Editar curso</h1>
            <p>Actualiza la informacion principal del curso.</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('teacher.courses.show', $course) }}">Volver</a>
    </div>

    @if ($errors->any())
        <div class="alert">
            <p>Revisa los datos del formulario.</p>
        </div>
    @endif

    <form class="form-grid" method="POST" action="{{ route('teacher.courses.update', $course) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div>
            <label for="name">Nombre del curso</label>
            <input id="name" name="name" type="text" value="{{ old('name', $course->name) }}" required>
            @error('name')
                <div class="hint">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="subtitle">Subtitulo</label>
            <input id="subtitle" name="subtitle" type="text" value="{{ old('subtitle', $course->subtitle) }}">
            @error('subtitle')
                <div class="hint">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="description">Descripcion</label>
            <textarea id="description" name="description" required>{{ old('description', $course->description) }}</textarea>
            @error('description')
                <div class="hint">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="price_ars">Precio ARS</label>
            <input id="price_ars" name="price_ars" type="number" step="0.01" min="0" value="{{ old('price_ars', $course->price_ars) }}" required>
            @error('price_ars')
                <div class="hint">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="price_usd">Precio USD</label>
            <input id="price_usd" name="price_usd" type="number" step="0.01" min="0" value="{{ old('price_usd', $course->price_usd) }}" required>
            @error('price_usd')
                <div class="hint">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="start_date">Fecha de inicio</label>
            <input id="start_date" name="start_date" type="date" value="{{ old('start_date', optional($course->start_date)->format('Y-m-d')) }}">
            @error('start_date')
                <div class="hint">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="image">Imagen del curso</label>
            <input id="image" name="image" type="file" accept="image/*">
            @if ($course->image_path)
                <div class="hint">Imagen actual: {{ $course->image_path }}</div>
            @endif
            @error('image')
                <div class="hint">{{ $message }}</div>
            @enderror
        </div>
        <button class="btn" type="submit">Guardar cambios</button>
    </form>
@endsection
