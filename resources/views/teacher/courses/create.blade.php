@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1>Crear curso</h1>
            <p>Completa los datos para publicar un curso.</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('teacher.courses.index') }}">Volver</a>
    </div>

    @if ($errors->any())
        <div class="alert">
            <p>Revisa los datos del formulario.</p>
        </div>
    @endif

    <form class="form-grid" method="POST" action="{{ route('teacher.courses.store') }}" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="name">Nombre del curso</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required>
            @error('name')
                <div class="hint">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="subtitle">Subtitulo</label>
            <input id="subtitle" name="subtitle" type="text" value="{{ old('subtitle') }}">
            @error('subtitle')
                <div class="hint">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="description">Descripcion</label>
            <textarea id="description" name="description" required>{{ old('description') }}</textarea>
            @error('description')
                <div class="hint">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="price_ars">Precio ARS</label>
            <input id="price_ars" name="price_ars" type="number" step="0.01" min="0" value="{{ old('price_ars', '0.00') }}" required>
            @error('price_ars')
                <div class="hint">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="price_usd">Precio USD</label>
            <input id="price_usd" name="price_usd" type="number" step="0.01" min="0" value="{{ old('price_usd', '0.00') }}" required>
            @error('price_usd')
                <div class="hint">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="start_date">Fecha de inicio</label>
            <input id="start_date" name="start_date" type="date" value="{{ old('start_date') }}">
            @error('start_date')
                <div class="hint">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="image">Imagen del curso</label>
            <input id="image" name="image" type="file" accept="image/*">
            @error('image')
                <div class="hint">{{ $message }}</div>
            @enderror
        </div>
        <button class="btn" type="submit">Guardar curso</button>
    </form>
@endsection
