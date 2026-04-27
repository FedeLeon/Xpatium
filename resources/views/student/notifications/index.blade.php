@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1 class="view-title">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 7h18s-3 0-3-7"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
                Notificaciones
            </h1>
            <p>Mensajes recientes sobre tus contenidos de ayuda.</p>
        </div>
        <div class="section-actions">
            @if (!empty($notificationUnreadCount))
                <form method="POST" action="{{ route('notifications.read') }}">
                    @csrf
                    <button class="btn btn-secondary" type="submit">Marcar como leidas</button>
                </form>
            @endif
            @if ($notifications->isNotEmpty())
                <form method="POST" action="{{ route('student.notifications.destroyAll') }}">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" type="submit">Borrar todo</button>
                </form>
            @endif
        </div>
    </div>

    @if ($notifications->isEmpty())
        <p class="subtle">No hay notificaciones nuevas.</p>
    @else
        <div class="notification-list">
            @foreach ($notifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $customUrl = $notification->data['url'] ?? null;
                    $courseId = $notification->data['course_id'] ?? null;
                    $lessonId = $notification->data['lesson_id'] ?? null;
                    $commentId = $notification->data['parent_id'] ?? $notification->data['comment_id'] ?? null;
                    $notificationUrl = $customUrl ?: ($lessonId
                        ? route('student.lessons.show', $lessonId).($commentId ? '#comment-'.$commentId : '')
                        : ($courseId ? route('student.courses.show', $courseId) : route('student.notifications.index')));
                @endphp
                <div class="notification-item {{ $isUnread ? 'notification-item--unread' : '' }}">
                    <a class="notification-link" href="{{ $notificationUrl }}">
                        <div>
                            <p class="notification-message">{{ $notification->data['message'] ?? 'Nueva notificacion' }}</p>
                            <span class="notification-meta">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                    <div class="notification-actions">
                        @if ($isUnread)
                            <span class="badge badge-warn">Nuevo</span>
                        @endif
                        <form method="POST" action="{{ route('student.notifications.destroy', $notification->id) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit">Borrar</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
