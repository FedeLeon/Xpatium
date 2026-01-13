@extends('layouts.app')

@section('content')
    <h1>Panel de alumno</h1>
    <p>Accede a tus cursos, clases en vivo y materiales.</p>

    <div class="card card-spaced">
        <h3>Anuncios</h3>
        @if ($announcements->isEmpty())
            <p class="subtle">No hay anuncios disponibles.</p>
        @else
            <div class="list">
                @foreach ($announcements as $announcement)
                    <div class="card">
                        <div>
                            <h3>{{ $announcement->title }}</h3>
                            <p>{{ $announcement->body }}</p>
                            @if ($announcement->starts_at || $announcement->ends_at)
                                <p class="subtle">
                                    @if ($announcement->starts_at)
                                        Desde {{ $announcement->starts_at->format('d/m/Y') }}
                                    @endif
                                    @if ($announcement->ends_at)
                                        hasta {{ $announcement->ends_at->format('d/m/Y') }}
                                    @endif
                                </p>
                            @endif
                        </div>
                        @if ($announcement->image_path)
                            <img class="announcement-image" src="{{ asset('storage/' . $announcement->image_path) }}" alt="Imagen del anuncio">
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
