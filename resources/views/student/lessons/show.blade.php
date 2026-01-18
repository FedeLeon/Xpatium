@extends('layouts.app')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/lessons-shared.css') }}">
    @endpush

    <div class="lesson-layout">
        <aside class="lesson-sidebar">
            <div class="lesson-sidebar__lesson">
                <h1>{{ $lesson->title }}</h1>
                <p class="subtle">{{ $course->name }}</p>
            </div>
            <div class="lesson-sidebar__module">
                <div class="lesson-sidebar__header">
                    <span class="subtle">Modulo</span>
                    <h3>{{ $module?->title }}</h3>
                </div>
                @if ($module)
                    <div class="course-progress lesson-sidebar__progress">
                        <div class="progress-meta" data-module-progress>
                            <span>Progreso del modulo</span>
                            <span>{{ $moduleProgress }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-bar__fill" style="width: {{ $moduleProgress }}%" data-module-progress-bar></div>
                        </div>
                        <span class="progress-count" data-module-progress-count>{{ $moduleCompleted }} / {{ $moduleTotal }} lecciones</span>
                    </div>
                @endif
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
            </div>
            <a class="btn btn-secondary lesson-sidebar__back" href="{{ route('student.courses.show', $course) }}">Volver</a>
        </aside>

        <div class="lesson-main">
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
                        @php
                            $quizResult = session('quiz_result');
                            $quizFeedback = $quizResult['feedback'] ?? [];
                        @endphp
                        <p class="subtle">{{ $quiz->title }}</p>
                        @if ($quizResult)
                            <div class="quiz-result-banner {{ $quizResult['score'] === $quizResult['total'] ? 'is-correct' : 'is-wrong' }}">
                                Resultado: {{ $quizResult['score'] }} / {{ $quizResult['total'] }}
                            </div>
                        @endif
                        @if ($latestAttempt)
                            <p class="subtle">Ultimo intento: {{ $latestAttempt->score }} / {{ $latestAttempt->total_questions }}</p>
                        @endif
                        <form method="POST" action="{{ route('student.lessons.quiz.store', $lesson) }}" class="form-grid" id="quiz-form">
                            @csrf
                            @foreach ($quiz->questions as $question)
                                @php
                                    $feedback = $quizFeedback[$question->id] ?? null;
                                    $cardState = $feedback
                                        ? ($feedback['is_correct'] ? 'is-correct' : 'is-wrong')
                                        : '';
                                @endphp
                                <div class="card quiz-feedback-card {{ $cardState }}" data-question-id="{{ $question->id }}">
                                    <strong>{{ $loop->iteration }}. {{ $question->prompt }}</strong>
                                    <input type="hidden" name="answers[{{ $question->id }}]" value="{{ $feedback['selected'] ?? '' }}" data-answer-input="{{ $question->id }}">
                                    <div class="list">
                                        @foreach ($question->options as $option)
                                            @php
                                                $isSelected = $feedback && $feedback['selected'] === $option->id;
                                                $isCorrect = $feedback && $feedback['correct'] === $option->id;
                                                $optionState = $isCorrect ? 'is-correct' : ($isSelected ? 'is-wrong' : '');
                                            @endphp
                                            <button
                                                class="quiz-option {{ $optionState }} {{ $isSelected ? 'is-selected' : '' }}"
                                                type="button"
                                                data-option-id="{{ $option->id }}"
                                                data-question-id="{{ $question->id }}"
                                                {{ $quizResult ? 'disabled' : '' }}
                                            >
                                                <span>{{ $option->option_text }}</span>
                                                @if ($isCorrect)
                                                    <span class="quiz-option__badge">Correcta</span>
                                                @elseif ($isSelected)
                                                    <span class="quiz-option__badge">Incorrecta</span>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            <button class="btn" type="submit" {{ $quizResult ? 'disabled' : '' }}>Enviar cuestionario</button>
                        </form>
                    @endif
                </div>

                <div class="card lesson-completion-card">
                    <form method="POST" action="{{ route('student.lessons.toggleCompletion', $lesson) }}" class="lesson-completion">
                        @csrf
                        <label class="completion-toggle" data-completion-toggle>
                            <input
                                type="checkbox"
                                name="completed"
                                {{ $isCompleted ? 'checked' : '' }}
                            >
                            <span class="completion-toggle__box"></span>
                            <span class="completion-toggle__label" data-completion-label>
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

                    <details class="comments-panel">
                        <summary>
                            <span>Comentarios</span>
                            <span class="comments-meta">{{ $comments->count() }} mensajes</span>
                            <span class="comments-arrow">▼</span>
                        </summary>
                        <div class="comments-body">
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
                    </details>
                </div>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const form = document.getElementById('quiz-form');
            if (!form) {
                return;
            }
            const submitBtn = form.querySelector('button[type="submit"]');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const answerInputs = Array.from(form.querySelectorAll('[data-answer-input]'));

            const applyFeedback = (feedback) => {
                Object.entries(feedback).forEach(([questionId, data]) => {
                    const card = form.querySelector(`[data-question-id="${questionId}"]`);
                    if (!card) {
                        return;
                    }
                    card.classList.add('quiz-feedback-card', data.is_correct ? 'is-correct' : 'is-wrong');
                    const optionButtons = card.querySelectorAll('[data-option-id]');
                    optionButtons.forEach((button) => {
                        const optionId = Number(button.getAttribute('data-option-id'));
                        button.classList.remove('is-correct', 'is-wrong', 'is-selected');
                        const existingBadge = button.querySelector('.quiz-option__badge');
                        if (existingBadge) {
                            existingBadge.remove();
                        }
                        if (optionId === data.correct) {
                            button.classList.add('is-correct');
                            const badge = document.createElement('span');
                            badge.className = 'quiz-option__badge';
                            badge.textContent = 'Correcta';
                            button.appendChild(badge);
                        } else if (optionId === data.selected) {
                            button.classList.add('is-wrong', 'is-selected');
                            const badge = document.createElement('span');
                            badge.className = 'quiz-option__badge';
                            badge.textContent = 'Incorrecta';
                            button.appendChild(badge);
                        }
                    });
                });
            };

            const allAnswered = () => answerInputs.every((input) => input.value);

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                if (submitBtn?.disabled) {
                    return;
                }
                if (!allAnswered()) {
                    if (window.Swal) {
                        window.Swal.fire({
                            icon: 'info',
                            title: 'Responde todas las preguntas',
                            text: 'Selecciona una opcion en cada pregunta para enviar.',
                        });
                    }
                    return;
                }
                const payload = new FormData(form);
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrf || '',
                            'Accept': 'application/json',
                        },
                        body: payload,
                    });
                    if (!response.ok) {
                        throw new Error('request_failed');
                    }
                    const result = await response.json();
                    const score = result.score ?? 0;
                    const total = result.total ?? 0;
                    applyFeedback(result.feedback || {});
                    form.querySelectorAll('.quiz-option').forEach((button) => {
                        button.disabled = true;
                    });
                    if (submitBtn) {
                        submitBtn.disabled = true;
                    }
                    if (window.Swal) {
                        window.Swal.fire({
                            icon: score === total ? 'success' : 'error',
                            title: 'Resultado del cuestionario',
                            text: score === total
                                ? `Obtuviste ${score} de ${total}.`
                                : `Obtuviste ${score} de ${total}. La proxima vez lo haras mejor.`,
                        });
                    }
                } catch (error) {
                    if (window.Swal) {
                        window.Swal.fire({
                            icon: 'error',
                            title: 'No se pudo enviar',
                            text: 'Intenta nuevamente en unos segundos.',
                        });
                    }
                }
            });

            form.querySelectorAll('.quiz-option').forEach((button) => {
                button.addEventListener('click', () => {
                    if (button.disabled) {
                        return;
                    }
                    const questionId = button.getAttribute('data-question-id');
                    const optionId = button.getAttribute('data-option-id');
                    const input = form.querySelector(`[data-answer-input="${questionId}"]`);
                    if (input) {
                        input.value = optionId;
                    }
                    const siblings = form.querySelectorAll(`.quiz-option[data-question-id="${questionId}"]`);
                    siblings.forEach((sibling) => sibling.classList.remove('is-selected'));
                    button.classList.add('is-selected');
                    if (allAnswered()) {
                        form.requestSubmit();
                    }
                });
            });
        })();
    </script>
    <script>
        (() => {
            const toggle = document.querySelector('[data-completion-toggle]');
            if (!toggle) {
                return;
            }
            const checkbox = toggle.querySelector('input[type="checkbox"]');
            if (!checkbox) {
                return;
            }
            const form = toggle.closest('form');
            const progressMeta = document.querySelector('[data-module-progress]');
            const progressBar = document.querySelector('[data-module-progress-bar]');
            const progressCount = document.querySelector('[data-module-progress-count]');
            const label = toggle.querySelector('[data-completion-label]');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const setProgress = (progress) => {
                if (progressMeta) {
                    const value = progressMeta.querySelector('span:last-child');
                    if (value) {
                        value.textContent = `${progress}%`;
                    }
                }
                if (progressBar) {
                    progressBar.style.width = `${progress}%`;
                }
            };

            checkbox.addEventListener('change', async () => {
                if (!form) {
                    return;
                }
                const previousState = !checkbox.checked;
                toggle.classList.add('is-loading');
                checkbox.disabled = true;
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrf || '',
                            'Accept': 'application/json',
                        },
                    });
                    if (!response.ok) {
                        throw new Error('request_failed');
                    }
                    const result = await response.json();
                    checkbox.checked = !!result.is_completed;
                    if (label) {
                        label.textContent = result.is_completed
                            ? 'Leccion completada'
                            : 'Marcar como completada';
                    }
                    if (typeof result.module_progress === 'number') {
                        setProgress(result.module_progress);
                    }
                    if (progressCount) {
                        progressCount.textContent = `${result.module_completed} / ${result.module_total} lecciones`;
                    }
                } catch (error) {
                    checkbox.checked = previousState;
                    if (window.Swal) {
                        window.Swal.fire({
                            icon: 'error',
                            title: 'No se pudo actualizar',
                            text: 'Intenta nuevamente en unos segundos.',
                        });
                    }
                } finally {
                    toggle.classList.remove('is-loading');
                    checkbox.disabled = false;
                }
            });
        })();
    </script>
@endsection
