@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1>Anuncios</h1>
            <p>Publica avisos para tus alumnos.</p>
        </div>
        <button class="btn" type="button" onclick="document.getElementById('announcementModal').showModal()">Nuevo anuncio</button>
    </div>

    @if ($announcements->isEmpty())
        <p>No hay anuncios creados.</p>
    @else
        <div class="list">
            @foreach ($announcements as $announcement)
                <div class="card">
                    <div>
                        <div class="actions actions-tight">
                            <h3>{{ $announcement->title }}</h3>
                            <span class="badge">{{ $announcement->status }}</span>
                        </div>
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
                    <div class="student-actions">
                        <form method="POST" action="{{ route('teacher.announcements.status', $announcement) }}">
                            @csrf
                            @method('PUT')
                            @if ($announcement->status === 'activo')
                                <input type="hidden" name="status" value="inactivo">
                                <button class="btn btn-secondary" type="submit">Pausar</button>
                            @else
                                <input type="hidden" name="status" value="activo">
                                <button class="btn" type="submit">Activar</button>
                            @endif
                        </form>
                        <form method="POST" action="{{ route('teacher.announcements.destroy', $announcement) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-secondary" type="submit">Eliminar</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <dialog id="announcementModal">
        <div class="modal-header">
            <strong>Nuevo anuncio</strong>
            <button class="btn btn-secondary" type="button" onclick="document.getElementById('announcementModal').close()">Cerrar</button>
        </div>
        <form method="POST" action="{{ route('teacher.announcements.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-body form-grid">
                <div>
                    <label for="title">Titulo</label>
                    <input id="title" name="title" type="text" required>
                </div>
                <div>
                    <label for="body">Texto</label>
                    <textarea id="body" name="body" required></textarea>
                </div>
                <div>
                    <label for="image">Imagen (opcional)</label>
                    <input id="image" name="image" type="file" accept="image/*">
                </div>
                <div>
                    <label for="starts_at">Fecha de inicio</label>
                    <input id="starts_at" name="starts_at" type="date">
                </div>
                <div>
                    <label for="ends_at">Fecha de fin</label>
                    <input id="ends_at" name="ends_at" type="date">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('announcementModal').close()">Cancelar</button>
                <button class="btn" type="submit">Publicar</button>
            </div>
        </form>
    </dialog>
@endsection
