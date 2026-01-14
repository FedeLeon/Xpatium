@extends('layouts.app')

@section('content')
    <div class="lesson-layout">
        <aside class="lesson-sidebar">
            <div class="lesson-sidebar__header">
                <span class="subtle">Modulo</span>
                <h3>{{ $module?->title }}</h3>
            </div>
            <div class="lesson-sidebar__list">
                @foreach ($moduleLessons as $moduleLesson)
                    <a
                        class="lesson-link {{ $moduleLesson->id === $lesson->id ? 'is-active' : '' }}"
                        href="{{ route('student.lessons.show', $moduleLesson) }}"
                    >
                        <span>{{ $moduleLesson->title }}</span>
                    </a>
                @endforeach
            </div>
        </aside>

        <div class="lesson-main">
            <div class="actions">
                <div>
                    <h1>{{ $lesson->title }}</h1>
                    <p class="subtle">{{ $course->name }}</p>
                </div>
                <a class="btn btn-secondary" href="{{ route('student.courses.show', $course) }}">Volver</a>
            </div>

            <div class="lesson-stack">
                <div class="card">
                    <h3>Video</h3>
                    @if (($contents['video'] ?? collect())->isEmpty())
                        <p class="subtle">No hay video.</p>
                    @else
                        @foreach ($contents['video'] as $content)
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
                    @if (($contents['image'] ?? collect())->isEmpty())
                        <p class="subtle">No hay imagenes.</p>
                    @else
                        <div class="grid">
                            @foreach ($contents['image'] as $content)
                                @if ($content->asset)
                                    <img src="{{ asset('storage/' . $content->asset->path) }}" alt="Imagen de leccion">
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="card">
                    <h3>Archivos</h3>
                    @if (($contents['file'] ?? collect())->isEmpty())
                        <p class="subtle">No hay archivos.</p>
                    @else
                        <div class="asset-list">
                            @foreach ($contents['file'] as $content)
                                @if ($content->asset)
                                    <div class="asset-item">
                                        <span>{{ $content->asset->original_name }}</span>
                                        <a href="{{ asset('storage/' . $content->asset->path) }}" target="_blank" rel="noopener">Ver archivo</a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="card">
                    <h3>Textos</h3>
                    @if (($contents['text'] ?? collect())->isEmpty())
                        <p class="subtle">No hay textos.</p>
                    @else
                        <div class="list">
                            @foreach ($contents['text'] as $content)
                                <p>{{ $content->text_content }}</p>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="card lesson-completion-card">
                    <form method="POST" action="{{ route('student.lessons.toggleCompletion', $lesson) }}" class="lesson-completion">
                        @csrf
                        <label class="completion-toggle">
                            <input
                                type="checkbox"
                                name="completed"
                                {{ $isCompleted ? 'checked' : '' }}
                                onchange="this.form.submit()"
                            >
                            <span class="completion-toggle__box"></span>
                            <span class="completion-toggle__label">
                                {{ $isCompleted ? 'Leccion completada' : 'Marcar como completada' }}
                            </span>
                        </label>
                    </form>
                </div>

                <div class="card">
                    <div class="lesson-nav">
                        @if ($previousLesson)
                            <a class="btn btn-secondary" href="{{ route('student.lessons.show', $previousLesson) }}">← Anterior</a>
                        @else
                            <span class="btn btn-secondary btn-disabled">← Anterior</span>
                        @endif
                        @if ($nextLesson)
                            <a class="btn btn-secondary" href="{{ route('student.lessons.show', $nextLesson) }}">Siguiente →</a>
                        @else
                            <span class="btn btn-secondary btn-disabled">Siguiente →</span>
                        @endif
                    </div>

                    <h3>Comentarios</h3>
                    <form method="POST" action="{{ route('lessons.comments.store', $lesson) }}" class="form-grid comment-form">
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
            </div>
        </div>
    </div>
@endsection
