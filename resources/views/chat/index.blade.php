@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1 class="view-title">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8z"></path>
                </svg>
                Chat
            </h1>
            <p>Conversaciones con alumnos y grupos.</p>
        </div>
        @if (auth()->user()->role === \App\Models\User::ROLE_TEACHER)
            <div>
                <button class="btn btn-action" type="button" onclick="document.getElementById('directModal').showModal()">Nuevo chat</button>
                <button class="btn btn-action" type="button" onclick="document.getElementById('groupModal').showModal()">Nuevo grupo</button>
            </div>
        @endif
    </div>

    @php
        $currentUser = auth()->user();
    @endphp

    <div class="chat-layout">
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
                        <div class="chat-avatar">
                            @if ($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="Avatar de {{ $title ?? 'usuario' }}">
                            @else
                                {{ $avatarInitial }}
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
        <div class="chat-window">
            @if (! $activeConversation)
                <div>
                    <h3>Selecciona una conversacion</h3>
                    <p class="subtle">Elegi un chat para ver los mensajes.</p>
                </div>
            @else
                @php
                    $activeTitle = $activeConversation->type === \App\Models\Conversation::TYPE_GROUP
                        ? ($activeConversation->title ?: 'Grupo sin titulo')
                        : $activeConversation->participants->where('id', '!=', $currentUser->id)->first()?->name;
                @endphp
                <div class="actions actions-tight">
                    <div>
                        <h3>{{ $activeTitle }}</h3>
                        <p class="subtle">{{ $activeConversation->participants->pluck('name')->join(', ') }}</p>
                    </div>
                    <a class="btn btn-secondary" href="{{ route('chat.index') }}">Cerrar</a>
                </div>
                <div class="messages">
                    @if ($activeConversation->messages->isEmpty())
                        <p class="subtle">No hay mensajes todavia.</p>
                    @else
                        @php
                            $otherParticipant = $activeConversation->participants->where('id', '!=', $currentUser->id)->first();
                            $otherLastRead = $otherParticipant?->pivot?->last_read_at;
                        @endphp
                        @foreach ($activeConversation->messages as $message)
                            <div class="bubble {{ $message->user_id === $currentUser->id ? 'me' : 'other' }}">
                                {{ $message->body }}
                                <small class="bubble-meta">
                                    {{ $message->created_at->format('H:i') }}
                                    @if ($message->user_id === $currentUser->id && $activeConversation->type === \App\Models\Conversation::TYPE_DIRECT)
                                        @if ($otherLastRead && $message->created_at->lte($otherLastRead))
                                            · Visto ✓✓
                                        @endif
                                    @endif
                                </small>
                            </div>
                        @endforeach
                    @endif
                </div>
                <form method="POST" action="{{ route('chat.messages.store', $activeConversation) }}" class="form-grid chat-input" data-chat-form>
                    @csrf
                    <div>
                        <label for="body">Mensaje</label>
                        <textarea id="body" name="body" required></textarea>
                    </div>
                    <button class="btn" type="submit">
                        <span>✈️</span>
                        <span>Enviar</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <script>
        (function () {
            var form = document.querySelector('[data-chat-form]');
            if (!form) {
                return;
            }
            var textarea = form.querySelector('textarea');
            if (!textarea) {
                return;
            }
            textarea.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' && !event.shiftKey) {
                    event.preventDefault();
                    form.submit();
                }
            });
        })();
    </script>

    @if (auth()->user()->role === \App\Models\User::ROLE_TEACHER)
        <dialog id="directModal">
            <div class="modal-header">
                <strong>Nuevo chat con alumno</strong>
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('directModal').close()">Cerrar</button>
            </div>
            <form method="POST" action="{{ route('chat.direct.store') }}">
                @csrf
                <div class="modal-body form-grid">
                    <div>
                        <label for="student_id">Alumno</label>
                        <select id="student_id" name="student_id" required>
                            <option value="">Selecciona un alumno</option>
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
                        <label>Alumnos</label>
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
