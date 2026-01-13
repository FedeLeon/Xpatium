@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1>Gestor de contenidos</h1>
            <p>Archivos subidos por tu cuenta.</p>
        </div>
        <form method="POST" action="{{ route('teacher.assets.store') }}" enctype="multipart/form-data">
            @csrf
            <select name="type" required>
                <option value="file">Archivo</option>
                <option value="image">Imagen</option>
                <option value="video">Video</option>
            </select>
            <input type="file" name="file" required>
            <button class="btn" type="submit">Subir archivo</button>
        </form>
    </div>

    @if ($assets->isEmpty())
        <p>No hay archivos cargados.</p>
    @else
        @foreach (['video' => 'Videos', 'image' => 'Imagenes', 'file' => 'Archivos'] as $type => $label)
            <div class="card">
                <h3>{{ $label }}</h3>
                @php
                    $items = $assets->get($type, collect());
                @endphp
                @if ($items->isEmpty())
                    <p class="subtle">Sin archivos.</p>
                @else
                    <div class="asset-list">
                        @foreach ($items as $asset)
                            <div class="asset-item">
                                <span>{{ $asset->original_name }}</span>
                                <a href="{{ asset('storage/' . $asset->path) }}" target="_blank" rel="noopener">Ver</a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    @endif
@endsection
