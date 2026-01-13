@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1>{{ $lesson->title }}</h1>
            <p class="subtle">{{ $course->name }}</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('teacher.courses.modules.index', $course) }}">Volver</a>
    </div>

    <div class="actions">
        <button class="btn" type="button" onclick="document.getElementById('textModal').showModal()">Texto</button>
        <button class="btn" type="button" onclick="document.getElementById('imageModal').showModal()">Imagen</button>
        <button class="btn" type="button" onclick="document.getElementById('videoModal').showModal()">Video</button>
        <button class="btn" type="button" onclick="document.getElementById('fileModal').showModal()">Archivo</button>
    </div>

    <div class="card">
        <h3>Contenido</h3>
        @if ($lesson->contents->isEmpty())
            <p class="subtle">Todavia no hay contenido cargado.</p>
        @else
            <div class="list">
                @foreach ($lesson->contents as $content)
                    <div class="card">
                        <div class="badge">{{ strtoupper($content->type) }}</div>
                        @if ($content->type === 'text')
                            <p>{{ $content->text_content }}</p>
                        @elseif ($content->asset)
                            <p>{{ $content->asset->original_name }}</p>
                            <a href="{{ asset('storage/' . $content->asset->path) }}" target="_blank" rel="noopener">Ver archivo</a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <dialog id="textModal">
        <div class="modal-header">
            <strong>Agregar texto</strong>
            <button class="btn btn-secondary" type="button" onclick="document.getElementById('textModal').close()">Cerrar</button>
        </div>
        <form method="POST" action="{{ route('teacher.lessons.content.store', $lesson) }}">
            @csrf
            <input type="hidden" name="type" value="text">
            <div class="modal-body form-grid">
                <div>
                    <label for="text_content">Texto</label>
                    <textarea id="text_content" name="text_content" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('textModal').close()">Cancelar</button>
                <button class="btn" type="submit">Guardar</button>
            </div>
        </form>
    </dialog>

    <dialog id="imageModal">
        <div class="modal-header">
            <strong>Agregar imagen</strong>
            <button class="btn btn-secondary" type="button" onclick="document.getElementById('imageModal').close()">Cerrar</button>
        </div>
        <form method="POST" action="{{ route('teacher.lessons.content.store', $lesson) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" value="image">
            <div class="modal-body form-grid">
                <div>
                    <label>Imagenes ya subidas</label>
                    <div class="asset-list">
                        @forelse (($assets['image'] ?? collect()) as $asset)
                            <label class="asset-item">
                                <input type="radio" name="asset_id" value="{{ $asset->id }}">
                                <span>{{ $asset->original_name }}</span>
                            </label>
                        @empty
                            <p class="subtle">Sin imagenes.</p>
                        @endforelse
                    </div>
                </div>
                <div>
                    <label for="image_file">Subir nueva imagen</label>
                    <input id="image_file" name="file" type="file" accept="image/*">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('imageModal').close()">Cancelar</button>
                <button class="btn" type="submit">Guardar</button>
            </div>
        </form>
    </dialog>

    <dialog id="videoModal">
        <div class="modal-header">
            <strong>Agregar video</strong>
            <button class="btn btn-secondary" type="button" onclick="document.getElementById('videoModal').close()">Cerrar</button>
        </div>
        <form method="POST" action="{{ route('teacher.lessons.content.store', $lesson) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" value="video">
            <div class="modal-body form-grid">
                <div>
                    <label>Videos ya subidos</label>
                    <div class="asset-list">
                        @forelse (($assets['video'] ?? collect()) as $asset)
                            <label class="asset-item">
                                <input type="radio" name="asset_id" value="{{ $asset->id }}">
                                <span>{{ $asset->original_name }}</span>
                            </label>
                        @empty
                            <p class="subtle">Sin videos.</p>
                        @endforelse
                    </div>
                </div>
                <div>
                    <label for="video_file">Subir nuevo video</label>
                    <input id="video_file" name="file" type="file" accept="video/*">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('videoModal').close()">Cancelar</button>
                <button class="btn" type="submit">Guardar</button>
            </div>
        </form>
    </dialog>

    <dialog id="fileModal">
        <div class="modal-header">
            <strong>Agregar archivo</strong>
            <button class="btn btn-secondary" type="button" onclick="document.getElementById('fileModal').close()">Cerrar</button>
        </div>
        <form method="POST" action="{{ route('teacher.lessons.content.store', $lesson) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" value="file">
            <div class="modal-body form-grid">
                <div>
                    <label>Archivos ya subidos</label>
                    <div class="asset-list">
                        @forelse (($assets['file'] ?? collect()) as $asset)
                            <label class="asset-item">
                                <input type="radio" name="asset_id" value="{{ $asset->id }}">
                                <span>{{ $asset->original_name }}</span>
                            </label>
                        @empty
                            <p class="subtle">Sin archivos.</p>
                        @endforelse
                    </div>
                </div>
                <div>
                    <label for="generic_file">Subir nuevo archivo</label>
                    <input id="generic_file" name="file" type="file">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('fileModal').close()">Cancelar</button>
                <button class="btn" type="submit">Guardar</button>
            </div>
        </form>
    </dialog>

    <div class="card card-spaced">
        <h3>Comentarios</h3>
        <form method="POST" action="{{ route('lessons.comments.store', $lesson) }}" class="form-grid">
            @csrf
            <div>
                <label for="comment-body">Deja un comentario</label>
                <textarea id="comment-body" name="body" required></textarea>
            </div>
            <button class="btn" type="submit">Publicar</button>
        </form>

        @if ($comments->isEmpty())
            <p class="subtle">Sin comentarios.</p>
        @else
            <div class="list">
                @foreach ($comments as $comment)
                    @include('lessons.comment', ['comment' => $comment, 'level' => 0])
                @endforeach
            </div>
        @endif
    </div>
@endsection
