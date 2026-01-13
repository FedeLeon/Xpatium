@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1>{{ $lesson->title }}</h1>
            <p class="subtle">{{ $course->name }}</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('student.courses.show', $course) }}">Volver</a>
    </div>

    <div class="card">
        <h3>Video</h3>
        @if (($contents['video'] ?? collect())->isEmpty())
            <p class="subtle">No hay video.</p>
        @else
            @foreach ($contents['video'] as $content)
                @if ($content->asset)
                    <video controls style="width: 100%; border-radius: 8px;">
                        <source src="{{ asset('storage/' . $content->asset->path) }}" type="{{ $content->asset->mime }}">
                    </video>
                @endif
            @endforeach
        @endif
    </div>

    <div class="card">
        <h3>Imagenes</h3>
        @if (($contents['image'] ?? collect())->isEmpty())
            <p class="subtle">No hay imagenes.</p>
        @else
            <div class="grid">
                @foreach ($contents['image'] as $content)
                    @if ($content->asset)
                        <img src="{{ asset('storage/' . $content->asset->path) }}" alt="Imagen de leccion">
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    <div class="card">
        <h3>Archivos</h3>
        @if (($contents['file'] ?? collect())->isEmpty())
            <p class="subtle">No hay archivos.</p>
        @else
            <div class="asset-list">
                @foreach ($contents['file'] as $content)
                    @if ($content->asset)
                        <div class="asset-item">
                            <span>{{ $content->asset->original_name }}</span>
                            <a href="{{ asset('storage/' . $content->asset->path) }}" target="_blank" rel="noopener">Ver archivo</a>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    <div class="card">
        <h3>Textos</h3>
        @if (($contents['text'] ?? collect())->isEmpty())
            <p class="subtle">No hay textos.</p>
        @else
            <div class="list">
                @foreach ($contents['text'] as $content)
                    <p>{{ $content->text_content }}</p>
                @endforeach
            </div>
        @endif
    </div>
@endsection
