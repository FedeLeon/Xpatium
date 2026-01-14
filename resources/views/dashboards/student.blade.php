@extends('layouts.app')

@section('content')
    <h1>Bienvenido, {{ auth()->user()->name }} {{ auth()->user()->last_name }}</h1>
    <p>Este es tu espacio de aprendizaje. Continua con tus cursos activos.</p>

    @php
        $hasUnreadAnnouncements = $unreadAnnouncementsCount > 0;
    @endphp

    <div class="dashboard-card-grid">
        <div class="card dashboard-card">
            <div class="section-head">
                <div>
                    <h3>Mis cursos</h3>
                    <p class="subtle">Total de cursos habilitados.</p>
                </div>
                <a class="btn btn-secondary" href="{{ route('student.courses.index') }}">Ver cursos activos</a>
            </div>

            <div class="stat-card">
                <div>
                    <span class="stat-label">Cursos activos</span>
                    <span class="stat-value">{{ $courses->count() }}</span>
                </div>
            </div>
        </div>

        <div class="card dashboard-card {{ $hasUnreadAnnouncements ? 'dashboard-card--alert' : '' }}">
            <div>
                <h3>Anuncios</h3>
                <p class="subtle">Avisos publicados por tu profesor.</p>
            </div>

            <div class="announcement-status">
                <span class="announcement-count">{{ $unreadAnnouncementsCount }}</span>
                @if ($announcements->isEmpty())
                    <span class="subtle">No hay anuncios disponibles.</span>
                @elseif ($hasUnreadAnnouncements)
                    <span class="subtle">Tenes anuncios sin leer.</span>
                @else
                    <span class="subtle">No hay anuncios pendientes.</span>
                @endif
            </div>

            <button class="btn btn-secondary" type="button" onclick="document.getElementById('studentAnnouncementModal').showModal()">
                Ver anuncios
            </button>
        </div>
    </div>

    <dialog id="studentAnnouncementModal">
        <div class="modal-header">
            <strong>Anuncios</strong>
            <button class="btn btn-secondary" type="button" onclick="document.getElementById('studentAnnouncementModal').close()">Cerrar</button>
        </div>
        <div class="modal-body">
            @if ($announcements->isEmpty())
                <p class="subtle">No hay anuncios disponibles.</p>
            @else
                <div class="list announcement-list">
                    @foreach ($announcements as $announcement)
                        @php
                            $isRead = in_array($announcement->id, $readAnnouncementIds, true);
                        @endphp
                        <details class="announcement-item {{ $isRead ? '' : 'announcement-item--unread' }}">
                            <summary class="announcement-summary">
                                <div>
                                    <h3>{{ $announcement->title }}</h3>
                                    <p class="subtle">
                                        {{ $announcement->teacher->name ?? 'Profesor' }}
                                        @if ($announcement->starts_at || $announcement->ends_at)
                                            ·
                                            @if ($announcement->starts_at)
                                                Desde {{ $announcement->starts_at->format('d/m/Y') }}
                                            @endif
                                            @if ($announcement->ends_at)
                                                hasta {{ $announcement->ends_at->format('d/m/Y') }}
                                            @endif
                                        @endif
                                    </p>
                                </div>
                                @if (! $isRead)
                                    <span class="badge badge-warn">Nuevo</span>
                                @else
                                    <span class="badge">Leido</span>
                                @endif
                            </summary>
                            <div class="announcement-body">
                                <p>{{ $announcement->body }}</p>
                                @if ($announcement->image_path)
                                    <img class="announcement-image" src="{{ asset('storage/' . $announcement->image_path) }}" alt="Imagen del anuncio">
                                @endif
                                <div class="announcement-item__actions">
                                    @if (! $isRead)
                                        <form method="POST" action="{{ route('student.announcements.read', $announcement) }}">
                                            @csrf
                                            <button class="btn btn-secondary" type="submit">Marcar como leido</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </details>
                    @endforeach
                </div>
            @endif
        </div>
    </dialog>
@endsection
