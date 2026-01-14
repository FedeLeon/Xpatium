@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1>Gestor de contenidos</h1>
            <p>Subi archivos y usa miniaturas optimizadas en la app.</p>
        </div>
    </div>

    <div class="upload-card">
        <div class="upload-head">
            <h3>Subir archivos</h3>
            <p class="subtle">Arrastra archivos o usa el boton para seleccionar.</p>
        </div>
        <div class="progress-bar">
            <div id="uploadProgress" class="progress-bar__fill">0%</div>
        </div>
        <small id="uploadEta" class="subtle"></small>

        <form action="{{ route('teacher.assets.chunk') }}" class="dropzone" id="dropzoneForm">
            @csrf
            <div class="dz-message" data-dz-message>
                <button type="button" id="selectFilesBtn" class="btn btn-secondary btn-icon">
                    <span>📤</span>
                    <span>Seleccionar archivos</span>
                </button>
                <div class="subtle">O arrastra aqui tus archivos</div>
            </div>
        </form>

        <button id="uploadFilesBtn" class="btn btn-icon">
            <span>⬆️</span>
            <span>Subir</span>
        </button>
    </div>

    <form class="filters" method="GET" action="{{ route('teacher.assets.index') }}">
        <div class="filters__group">
            <label for="type">Tipo</label>
            <select id="type" name="type">
                <option value="all" {{ ($type ?? 'all') === 'all' ? 'selected' : '' }}>Todos</option>
                <option value="image" {{ ($type ?? '') === 'image' ? 'selected' : '' }}>Imagenes</option>
                <option value="video" {{ ($type ?? '') === 'video' ? 'selected' : '' }}>Videos</option>
                <option value="file" {{ ($type ?? '') === 'file' ? 'selected' : '' }}>Archivos</option>
            </select>
        </div>
        <div class="filters__group">
            <label for="sort">Ordenar</label>
            <select id="sort" name="sort">
                <option value="date_desc" {{ ($sort ?? '') === 'date_desc' ? 'selected' : '' }}>Fecha (nuevos)</option>
                <option value="date_asc" {{ ($sort ?? '') === 'date_asc' ? 'selected' : '' }}>Fecha (viejos)</option>
                <option value="name_asc" {{ ($sort ?? '') === 'name_asc' ? 'selected' : '' }}>Nombre (A→Z)</option>
                <option value="name_desc" {{ ($sort ?? '') === 'name_desc' ? 'selected' : '' }}>Nombre (Z→A)</option>
            </select>
        </div>
        <div class="filters__group">
            <label for="from">Desde</label>
            <input id="from" name="from" type="date" value="{{ $from ?? '' }}">
        </div>
        <div class="filters__group">
            <label for="to">Hasta</label>
            <input id="to" name="to" type="date" value="{{ $to ?? '' }}">
        </div>
        <div class="filters__group filters__group--grow">
            <label for="q">Buscar</label>
            <input id="q" name="q" type="search" placeholder="Nombre del archivo" value="{{ $q ?? '' }}">
        </div>
        <div class="filters__actions">
            <button class="btn" type="submit">Aplicar</button>
            <a class="btn btn-secondary" href="{{ route('teacher.assets.index') }}">Limpiar</a>
        </div>
    </form>

    <form id="bulkDeleteForm" method="POST" action="{{ route('teacher.assets.bulkDestroy') }}">
        @csrf
        @method('DELETE')
        <div class="assets-grid">
        @forelse ($assets as $asset)
            <div class="asset-card asset-card--link" data-card>
                <label class="asset-check">
                    <input type="checkbox" name="assets[]" value="{{ $asset->id }}" class="asset-selector">
                    <span></span>
                </label>
                <a class="asset-card__link" href="{{ asset('storage/' . $asset->path) }}" target="_blank" rel="noopener">
                <span class="asset-view-icon">🔍</span>
                @php
                    $ext = strtolower(pathinfo($asset->original_name, PATHINFO_EXTENSION));
                    $fileLabel = match ($ext) {
                        'pdf' => 'PDF',
                        'doc', 'docx' => 'DOC',
                        'xls', 'xlsx' => 'XLS',
                        'ppt', 'pptx' => 'PPT',
                        'zip', 'rar', '7z' => 'ZIP',
                        'mp3', 'wav' => 'AUD',
                        'mp4', 'mov', 'mkv', 'webm' => 'VID',
                        default => strtoupper($ext ?: substr($asset->type, 0, 1)),
                    };
                @endphp
                @if ($asset->thumb_path)
                    <img src="{{ asset('storage/' . $asset->thumb_path) }}" alt="Miniatura" class="asset-thumb">
                @elseif ($asset->type === 'image')
                    <img src="{{ asset('storage/' . $asset->path) }}" alt="Imagen" class="asset-thumb">
                @else
                    <div class="asset-placeholder asset-thumb">
                        <span class="file-icon file-icon--{{ strtolower($fileLabel) }}">{{ $fileLabel }}</span>
                    </div>
                @endif
                @php
                    $size = $asset->size ?? 0;
                    $units = ['B', 'KB', 'MB', 'GB'];
                    $unit = 0;
                    while ($size >= 1024 && $unit < count($units) - 1) {
                        $size /= 1024;
                        $unit++;
                    }
                    $sizeLabel = number_format($size, 1).' '.$units[$unit];
                @endphp
                <div class="asset-meta">
                    <strong>{{ $asset->original_name }}</strong>
                    <small>{{ $sizeLabel }} · {{ $asset->created_at->format('d/m/Y') }}</small>
                    <small>{{ $asset->type }}</small>
                    @if ($asset->type === 'video' && ! $asset->thumb_path)
                        <span class="badge badge-warn">Sin miniatura</span>
                    @endif
                </div>
                </a>
            </div>
        @empty
            <p class="subtle">No hay archivos subidos todavia.</p>
        @endforelse
        </div>
    </form>

    <div class="bulk-actions">
        <button class="btn btn-secondary" type="submit" form="bulkDeleteForm" data-confirm="Se eliminaran los archivos seleccionados.">
            <span>🗑️</span>
            <span>Eliminar seleccionados</span>
        </button>
    </div>

    <div class="pagination-wrapper">
        {{ $assets->links() }}
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <script>
        Dropzone.autoDiscover = false;
        var bar = document.getElementById('uploadProgress');
        var etaEl = document.getElementById('uploadEta');
        var uploadBtn = document.getElementById('uploadFilesBtn');

        var dz = new Dropzone('#dropzoneForm', {
            url: "{{ route('teacher.assets.chunk') }}",
            autoProcessQueue: false,
            chunking: true,
            forceChunking: true,
            chunkSize: 5 * 1024 * 1024,
            timeout: 0,
            parallelChunkUploads: false,
            parallelUploads: 1,
            retryChunks: true,
            retryChunksLimit: 3,
            maxFilesize: 1024,
            clickable: '#selectFilesBtn',
            previewsContainer: null
        });

        dz.on('addedfile', function (file) {
            file.upload.startedAt = Date.now();
        });

        uploadBtn.addEventListener('click', function () {
            dz.processQueue();
        });

        dz.on('uploadprogress', function (file, pct, bytesSent) {
            bar.style.width = pct + '%';
            bar.textContent = Math.round(pct) + '%';
            var elapsed = (Date.now() - file.upload.startedAt) / 1000;
            var speed = bytesSent / elapsed || 1;
            var remaining = (file.upload.total - bytesSent) / speed;
            etaEl.textContent = '≈ ' + human(remaining);
        });

        dz.on('queuecomplete', function () {
            etaEl.textContent = '';
            window.location.reload();
        });

        dz.on('complete', function () {
            if (dz.getQueuedFiles().length > 0 && dz.getUploadingFiles().length === 0) {
                dz.processQueue();
            }
        });

        function human(sec) {
            if (sec <= 0 || !isFinite(sec)) return '0 s';
            var m = Math.floor(sec / 60);
            var s = Math.floor(sec % 60);
            return (m ? m + ' m ' : '') + (s ? s + ' s' : '');
        }
    </script>
@endsection
