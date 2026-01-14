@php
    $indent = $level > 5 ? 5 : $level;
    $initial = strtoupper(substr($comment->user->name ?? 'A', 0, 1));
@endphp
<div id="comment-{{ $comment->id }}" class="comment-thread comment-level-{{ $indent }}">
    <div class="comment-card">
        <div class="comment-avatar">{{ $initial }}</div>
        <div class="comment-body">
            <div class="comment-bubble">
                <strong>{{ $comment->user->name }}</strong>
                <p>{{ $comment->body }}</p>
            </div>
            <div class="comment-actions">
                <details class="comment-reply">
                    <summary>Responder</summary>
                    <form method="POST" action="{{ route('lessons.comments.store', $comment->lesson_id) }}" class="comment-reply__form">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                        <textarea name="body" required placeholder="Escribe una respuesta..."></textarea>
                        <button class="btn btn-secondary" type="submit">Publicar</button>
                    </form>
                </details>
                <form method="POST" action="{{ route('lessons.comments.destroy', $comment) }}" data-confirm="Se eliminara el comentario y sus respuestas.">
                    @csrf
                    @method('DELETE')
                    <button class="comment-delete" type="submit">Eliminar</button>
                </form>
            </div>
        </div>
    </div>

    @if ($comment->replies->isNotEmpty())
        <details class="comment-replies">
            <summary>Ver respuestas ({{ $comment->replies->count() }})</summary>
            <div class="comment-replies__list">
                @foreach ($comment->replies as $reply)
                    @include('lessons.comment', ['comment' => $reply, 'level' => $level + 1])
                @endforeach
            </div>
        </details>
    @endif
</div>
