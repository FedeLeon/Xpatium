@extends('layouts.app')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/lessons-shared.css') }}">
    @endpush

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
                    @if (($contents['file'] ?? collect())->isEmpty())
                        <p class="subtle">No hay archivos.</p>
                    @else
            <div class="asset-list preview-file-list">
                @foreach ($contents['file'] as $content)
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

                <div class="card">
                    <h3>Cuestionario</h3>
                    @if (! $quiz)
                        <p class="subtle">No hay cuestionario disponible.</p>
                    @elseif ($quiz->questions->isEmpty())
                        <p class="subtle">El cuestionario todavia no tiene preguntas.</p>
                    @else
                        <p class="subtle">{{ $quiz->title }}</p>
                        @if ($latestAttempt)
                            <p class="subtle">Ultimo intento: {{ $latestAttempt->score }} / {{ $latestAttempt->total_questions }}</p>
                        @endif
                        <form method="POST" action="{{ route('student.lessons.quiz.store', $lesson) }}" class="form-grid">
                            @csrf
                            @foreach ($quiz->questions as $question)
                                <div class="card">
                                    <strong>{{ $loop->iteration }}. {{ $question->prompt }}</strong>
                                    <div class="list">
                                        @foreach ($question->options as $option)
                                            <label>
                                                <input
                                                    type="radio"
                                                    name="answers[{{ $question->id }}]"
                                                    value="{{ $option->id }}"
                                                    {{ $loop->first ? 'required' : '' }}
                                                >
                                                <span>{{ $option->option_text }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            <button class="btn" type="submit">Enviar cuestionario</button>
                        </form>
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
