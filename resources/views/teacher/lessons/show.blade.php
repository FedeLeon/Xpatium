@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1>{{ $lesson->title }}</h1>
            <p class="subtle">{{ $course->name }}</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('teacher.courses.modules.index', $course) }}">Volver</a>
    </div>

    @php
        $preview = $lesson->contents->groupBy('type');
    @endphp

    <div class="teacher-lesson-layout">
        <aside class="teacher-lesson-tools card">
            <h3>Agregar contenido</h3>
            <div class="tool-grid">
                <button class="tool-btn" type="button" onclick="document.getElementById('textModal').showModal()">
                    <svg class="tool-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M5 6h14M9 6v12m-4 0h10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span>Texto</span>
                </button>
                <button class="tool-btn" type="button" onclick="document.getElementById('imageModal').showModal()">
                    <svg class="tool-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 6h16v12H4zM8 10l3 3 3-4 4 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="9" cy="9" r="1.5" fill="currentColor"/>
                    </svg>
                    <span>Imagen</span>
                </button>
                <button class="tool-btn" type="button" onclick="document.getElementById('videoModal').showModal()">
                    <svg class="tool-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 6h10v12H4zM14 10l6-3v10l-6-3z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    </svg>
                    <span>Video</span>
                </button>
                <button class="tool-btn" type="button" onclick="document.getElementById('fileModal').showModal()">
                    <svg class="tool-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M8 4h6l4 4v12H8zM14 4v4h4" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    </svg>
                    <span>Archivo</span>
                </button>
            </div>
        </aside>

        <section class="teacher-lesson-preview">
            <div class="card preview-card">
                <div class="preview-header">
                    <h3>Vista previa de la leccion</h3>
                    <span class="badge">Asi lo ve el alumno</span>
                </div>

                <div class="list">
                    <div class="card">
                        <h3>Video</h3>
                        @if (($preview['video'] ?? collect())->isEmpty())
                            <p class="subtle">No hay video.</p>
                        @else
                            @foreach ($preview['video'] as $content)
                                @if ($content->asset)
                                    <video class="lesson-video" controls>
                                        <source src="{{ asset('storage/' . $content->asset->path) }}" type="{{ $content->asset->mime }}">
                                    </video>
                                @endif
                            @endforeach
                        @endif
                    </div>

                    <div class="card">
                        <h3>Imagenes</h3>
                        @if (($preview['image'] ?? collect())->isEmpty())
                            <p class="subtle">No hay imagenes.</p>
                        @else
                            <div class="grid">
                                @foreach ($preview['image'] as $content)
                                    @if ($content->asset)
                                        <a class="content-image-link" href="{{ asset('storage/' . $content->asset->path) }}" target="_blank" rel="noopener">
                                            <img class="content-image" src="{{ asset('storage/' . $content->asset->path) }}" alt="Imagen de leccion">
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="card">
                        <h3>Archivos</h3>
                        @if (($preview['file'] ?? collect())->isEmpty())
                            <p class="subtle">No hay archivos.</p>
                        @else
                            <div class="asset-list preview-file-list">
                                @foreach ($preview['file'] as $content)
                                    @if ($content->asset)
                                        @php
                                            $ext = strtolower(pathinfo($content->asset->original_name, PATHINFO_EXTENSION));
                                            $extLabel = $ext ? strtoupper($ext) : 'FILE';
                                            $iconMap = [
                                                'pdf' => 'pdf',
                                                'doc' => 'word',
                                                'docx' => 'word',
                                                'xls' => 'table',
                                                'xlsx' => 'table',
                                                'csv' => 'table',
                                                'ppt' => 'powerpoint',
                                                'pptx' => 'powerpoint',
                                                'zip' => 'zip',
                                                'rar' => 'zip',
                                                '7z' => 'zip',
                                                'mp3' => 'audio',
                                                'wav' => 'audio',
                                                'ogg' => 'audio',
                                                'flac' => 'audio',
                                                'txt' => 'document',
                                                'md' => 'document',
                                                'rtf' => 'document',
                                            ];
                                            $iconName = $iconMap[$ext] ?? 'document';
                                        @endphp
                                        <div class="asset-item preview-file-item">
                                            <img class="file-icon-img preview-file-icon" src="{{ asset('icons/filetypes/' . $iconName . '.svg') }}" alt="{{ $extLabel }}">
                                            <div class="preview-file-meta">
                                                <span class="preview-file-name">{{ $content->asset->original_name }}</span>
                                                <a href="{{ asset('storage/' . $content->asset->path) }}" target="_blank" rel="noopener">Ver archivo</a>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="card">
                        <h3>Textos</h3>
                        @if (($preview['text'] ?? collect())->isEmpty())
                            <p class="subtle">No hay textos.</p>
                        @else
                            <div class="list">
                                @foreach ($preview['text'] as $content)
                                    <p>{{ $content->text_content }}</p>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
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
                    <div class="asset-list asset-grid">
                        @forelse (($assets['image'] ?? collect()) as $asset)
                            <label class="asset-item asset-item--grid">
                                <input type="radio" name="asset_id" value="{{ $asset->id }}">
                                <img class="asset-thumb" src="{{ asset('storage/' . ($asset->thumb_path ?: $asset->path)) }}" alt="Miniatura de imagen">
                                <span class="asset-item__name">{{ $asset->original_name }}</span>
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
                    <div class="asset-list asset-grid">
                        @forelse (($assets['video'] ?? collect()) as $asset)
                            <label class="asset-item asset-item--grid">
                                <input type="radio" name="asset_id" value="{{ $asset->id }}">
                                @if ($asset->thumb_path)
                                    <img class="asset-thumb" src="{{ asset('storage/' . $asset->thumb_path) }}" alt="Miniatura de video">
                                @else
                                    <span class="asset-thumb asset-thumb--fallback">VIDEO</span>
                                @endif
                                <span class="asset-item__name">{{ $asset->original_name }}</span>
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
                    <div class="asset-list asset-grid">
                        @forelse (($assets['file'] ?? collect()) as $asset)
                            <label class="asset-item asset-item--grid">
                                <input type="radio" name="asset_id" value="{{ $asset->id }}">
                                @php
                                    $ext = strtolower(pathinfo($asset->original_name, PATHINFO_EXTENSION));
                                    $extLabel = $ext ? strtoupper($ext) : 'FILE';
                                    $iconMap = [
                                        'pdf' => 'pdf',
                                        'doc' => 'word',
                                        'docx' => 'word',
                                        'xls' => 'table',
                                        'xlsx' => 'table',
                                        'csv' => 'table',
                                        'ppt' => 'powerpoint',
                                        'pptx' => 'powerpoint',
                                        'zip' => 'zip',
                                        'rar' => 'zip',
                                        '7z' => 'zip',
                                        'mp3' => 'audio',
                                        'wav' => 'audio',
                                        'ogg' => 'audio',
                                        'flac' => 'audio',
                                        'txt' => 'document',
                                        'md' => 'document',
                                        'rtf' => 'document',
                                    ];
                                    $iconName = $iconMap[$ext] ?? 'document';
                                @endphp
                                <img class="file-icon-img" src="{{ asset('icons/filetypes/' . $iconName . '.svg') }}" alt="{{ $extLabel }}">
                                <span class="asset-item__name">{{ $asset->original_name }}</span>
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
