@extends('layouts.app')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/chat.css') }}">
    @endpush

    @php
        $currentUser = auth()->user();
    @endphp

    <div class="chat-layout">
        <div class="chat-sidebar">
            <div class="chat-sidebar__header">
                <h1 class="view-title">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8z"></path>
                    </svg>
                    Chat
                </h1>
                <p>Conversaciones con usuarios y grupos.</p>
                @if (auth()->user()->role === \App\Models\User::ROLE_TEACHER)
                    <div class="chat-sidebar__actions">
                        <button class="btn btn-action" type="button" onclick="document.getElementById('directModal').showModal()">Nuevo chat</button>
                        <button class="btn btn-action" type="button" onclick="document.getElementById('groupModal').showModal()">Nuevo grupo</button>
                    </div>
                @endif
            </div>
            <div class="chat-list">
            @if ($conversations->isEmpty())
                <p class="subtle">No hay conversaciones todavia.</p>
            @else
                @foreach ($conversations as $conversation)
                    @php
                        $lastMessage = $conversation->messages->first();
                        $otherParticipant = $conversation->participants->where('id', '!=', $currentUser->id)->first();
                        $title = $conversation->type === \App\Models\Conversation::TYPE_GROUP
                            ? ($conversation->title ?: 'Grupo sin titulo')
                            : $otherParticipant?->name;
                        $avatarUrl = $conversation->type === \App\Models\Conversation::TYPE_GROUP
                            ? null
                            : ($otherParticipant?->avatar_path ? asset('storage/' . $otherParticipant->avatar_path) : null);
                        $avatarInitial = strtoupper(substr($title ?? 'C', 0, 1));
                    @endphp
                    <a class="chat-item {{ $activeConversation && $activeConversation->id === $conversation->id ? 'chat-item--active' : '' }}" href="{{ route('chat.show', $conversation) }}">
                        <div class="chat-avatar-wrap">
                            <div class="chat-avatar">
                            @if ($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="Avatar de {{ $title ?? 'usuario' }}">
                            @else
                                {{ $avatarInitial }}
                            @endif
                            </div>
                            @if ($conversation->type === \App\Models\Conversation::TYPE_DIRECT && $otherParticipant)
                                <span class="online-dot" data-online-user-id="{{ $otherParticipant->id }}"></span>
                            @endif
                        </div>
                        <div class="chat-item__content">
                            <strong>{{ $title }}</strong>
                            @if ($lastMessage)
                                <small>{{ $lastMessage->body }}</small>
                            @else
                                <small>Sin mensajes</small>
                            @endif
                        </div>
                    </a>
                @endforeach
            @endif
            </div>
        </div>
        <div class="chat-column">
            @if (! $activeConversation)
                <div class="chat-panel chat-panel--header">
                    <div>
                        <h3>Selecciona una conversacion</h3>
                        <p class="subtle">Elegi un chat para ver los mensajes.</p>
                    </div>
                </div>
            @else
                @php
                    $activeTitle = $activeConversation->type === \App\Models\Conversation::TYPE_GROUP
                        ? ($activeConversation->title ?: 'Grupo sin titulo')
                        : $activeConversation->participants->where('id', '!=', $currentUser->id)->first()?->name;
                @endphp
                <div class="chat-panel chat-panel--header">
                    <div class="actions actions-tight chat-header">
                        <div>
                            <h3>{{ $activeTitle }}</h3>
                            <p class="subtle">{{ $activeConversation->participants->pluck('name')->join(', ') }}</p>
                        </div>
                        <a class="btn btn-secondary" href="{{ route('chat.index') }}">Cerrar</a>
                    </div>
                </div>
                <div class="chat-panel chat-panel--messages">
                    <div
                        class="messages"
                        data-chat-messages
                        data-conversation-id="{{ $activeConversation->id }}"
                        data-current-user="{{ $currentUser->id }}"
                        data-current-avatar="{{ $currentUser->avatar_path ? asset('storage/' . $currentUser->avatar_path) : '' }}"
                        data-current-initial="{{ strtoupper(substr($currentUser->name ?? 'A', 0, 1)) }}"
                    >
                        @if ($activeConversation->messages->isEmpty())
                            <p class="subtle">No hay mensajes todavia.</p>
                        @else
                            @php
                                $otherParticipant = $activeConversation->participants->where('id', '!=', $currentUser->id)->first();
                                $otherLastRead = $otherParticipant?->pivot?->last_read_at;
                            @endphp
                            @foreach ($activeConversation->messages as $message)
                                @php
                                    $messageUser = $message->user;
                                    $messageAvatar = $messageUser?->avatar_path ? asset('storage/' . $messageUser->avatar_path) : null;
                                    $messageInitial = strtoupper(substr($messageUser?->name ?? 'A', 0, 1));
                                    $isMine = $message->user_id === $currentUser->id;
                                @endphp
                                <div class="message-row {{ $isMine ? 'message-row--me' : '' }}">
                                    <span class="message-avatar">
                                        @if ($messageAvatar)
                                            <img src="{{ $messageAvatar }}" alt="Avatar de {{ $messageUser?->name ?? 'usuario' }}">
                                        @else
                                            {{ $messageInitial }}
                                        @endif
                                    </span>
                                    <div class="bubble {{ $isMine ? 'me' : 'other' }}">
                                        {{ $message->body }}
                                        <small class="bubble-meta">
                                            {{ $message->created_at->format('H:i') }}
                                            @if ($isMine && $activeConversation->type === \App\Models\Conversation::TYPE_DIRECT)
                                                @if ($otherLastRead && $message->created_at->lte($otherLastRead))
                                                    · Visto ✓✓
                                                @endif
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="chat-panel chat-panel--input">
                    <form method="POST" action="{{ route('chat.messages.store', $activeConversation) }}" class="form-grid chat-input" data-chat-form>
                        @csrf
                        <div>
                            <label for="body">Mensaje</label>
                            <textarea id="body" name="body" required></textarea>
                        </div>
                        <button class="btn btn-icon" type="submit">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M22 2L11 13"></path>
                                <path d="M22 2L15 22l-4-9-9-4z"></path>
                            </svg>
                            <span>Enviar</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <script>
        (function () {
            var form = document.querySelector('[data-chat-form]');
            if (!form) {
                return;
            }
            var messagesEl = document.querySelector('[data-chat-messages]');
            if (messagesEl) {
                messagesEl.scrollTop = messagesEl.scrollHeight;
            }
            var textarea = form.querySelector('textarea');
            if (!textarea) {
                return;
            }
            textarea.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' && !event.shiftKey) {
                    event.preventDefault();
                    form.requestSubmit();
                }
            });
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                if (form.dataset.sending === 'true') {
                    return;
                }
                var formData = new FormData(form);
                var bodyValue = String(formData.get('body') || '').trim();
                if (!bodyValue) {
                    return;
                }
                form.dataset.sending = 'true';
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                }).then(function (response) {
                    if (!response.ok) {
                        throw new Error('request_failed');
                    }
                    return response.json();
                }).then(function (payload) {
                    if (!messagesEl || !payload || !payload.message) {
                        return;
                    }
                    var emptyState = messagesEl.querySelector('.subtle');
                    if (emptyState) {
                        emptyState.remove();
                    }
                    var row = document.createElement('div');
                    row.className = 'message-row message-row--me';
                    var avatar = document.createElement('span');
                    avatar.className = 'message-avatar';
                    var avatarUrl = payload.message.user && payload.message.user.avatar_url;
                    var initial = payload.message.user && payload.message.user.initial;
                    if (avatarUrl) {
                        var img = document.createElement('img');
                        img.src = avatarUrl;
                        img.alt = 'Avatar';
                        avatar.appendChild(img);
                    } else if (initial) {
                        avatar.textContent = initial;
                    }
                    var bubble = document.createElement('div');
                    bubble.className = 'bubble me';
                    var text = document.createElement('span');
                    text.textContent = payload.message.body;
                    var meta = document.createElement('small');
                    meta.className = 'bubble-meta';
                    meta.textContent = payload.message.time;
                    bubble.appendChild(text);
                    bubble.appendChild(meta);
                    row.appendChild(avatar);
                    row.appendChild(bubble);
                    messagesEl.appendChild(row);
                    messagesEl.scrollTop = messagesEl.scrollHeight;
                    textarea.value = '';
                }).finally(function () {
                    form.dataset.sending = 'false';
                });
            });
        })();
    </script>

    @if (auth()->user()->role === \App\Models\User::ROLE_TEACHER)
        <dialog id="directModal">
            <div class="modal-header">
                <strong>Nuevo chat con usuario</strong>
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('directModal').close()">Cerrar</button>
            </div>
            <form method="POST" action="{{ route('chat.direct.store') }}">
                @csrf
                <div class="modal-body form-grid">
                    <div>
                        <label for="student_id">Usuario</label>
                        <select id="student_id" name="student_id" required>
                            <option value="">Selecciona un usuario</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} {{ $student->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" onclick="document.getElementById('directModal').close()">Cancelar</button>
                    <button class="btn" type="submit">Crear</button>
                </div>
            </form>
        </dialog>

        <dialog id="groupModal">
            <div class="modal-header">
                <strong>Nuevo grupo</strong>
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('groupModal').close()">Cerrar</button>
            </div>
            <form method="POST" action="{{ route('chat.group.store') }}">
                @csrf
                <div class="modal-body form-grid">
                    <div>
                        <label for="title">Nombre del grupo</label>
                        <input id="title" name="title" type="text" required>
                    </div>
                    <div>
                        <label>Usuarios</label>
                        <div class="asset-list">
                            @foreach ($students as $student)
                                <label class="asset-item">
                                    <input type="checkbox" name="students[]" value="{{ $student->id }}">
                                    <span>{{ $student->name }} {{ $student->last_name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" onclick="document.getElementById('groupModal').close()">Cancelar</button>
                    <button class="btn" type="submit">Crear grupo</button>
                </div>
            </form>
        </dialog>
    @endif
@endsection
