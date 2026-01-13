<div class="card" style="margin-left: {{ $level * 20 }}px;">
    <div>
        <strong>{{ $comment->user->name }}</strong>
        <p>{{ $comment->body }}</p>
    </div>
    <form method="POST" action="{{ route('lessons.comments.store', $comment->lesson_id) }}" class="form-grid">
        @csrf
        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
        <div>
            <label>Responder</label>
            <textarea name="body" required></textarea>
        </div>
        <button class="btn btn-secondary" type="submit">Responder</button>
    </form>

    @foreach ($comment->replies as $reply)
        @include('lessons.comment', ['comment' => $reply, 'level' => $level + 1])
    @endforeach
</div>
