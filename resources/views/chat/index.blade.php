@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1>Chat</h1>
            <p>Conversaciones con alumnos y grupos.</p>
        </div>
        @if (auth()->user()->role === \App\Models\User::ROLE_TEACHER)
            <div>
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('directModal').showModal()">Nuevo chat</button>
                <button class="btn" type="button" onclick="document.getElementById('groupModal').showModal()">Nuevo grupo</button>
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
                        $title = $conversation->type === \App\Models\Conversation::TYPE_GROUP
                            ? ($conversation->title ?: 'Grupo sin titulo')
                            : $conversation->participants->where('id', '!=', $currentUser->id)->first()?->name;
                    @endphp
                    <a class="chat-item" href="{{ route('chat.show', $conversation) }}">
                        <strong>{{ $title }}</strong><br>
                        @if ($lastMessage)
                            <small>{{ $lastMessage->body }}</small>
                        @else
                            <small>Sin mensajes</small>
                        @endif
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
                <div class="actions" style="margin-bottom: 0;">
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
                        @foreach ($activeConversation->messages as $message)
                            <div class="bubble {{ $message->user_id === $currentUser->id ? 'me' : 'other' }}">
                                {{ $message->body }}
                                <small>{{ $message->user->name }}</small>
                            </div>
                        @endforeach
                    @endif
                </div>
                <form method="POST" action="{{ route('chat.messages.store', $activeConversation) }}" class="form-grid">
                    @csrf
                    <div>
                        <label for="body">Mensaje</label>
                        <textarea id="body" name="body" required></textarea>
                    </div>
                    <button class="btn" type="submit">Enviar</button>
                </form>
            @endif
        </div>
    </div>

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
