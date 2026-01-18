@extends('layouts.app')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/lessons-shared.css') }}">
        <link rel="stylesheet" href="{{ asset('css/teacher-lessons.css') }}">
    @endpush

    <div class="actions">
        <div>
            <h1>{{ $lesson->title }}</h1>
            <p class="subtle">{{ $course->name }}</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('teacher.courses.modules.index', $course) }}">Volver</a>
    </div>

    @php
        $preview = $lesson->contents->groupBy('type');
    @endphp

    <div class="teacher-lesson-layout">
        <aside class="teacher-lesson-tools card">
            <h3>Agregar contenido</h3>
            <div class="tool-grid">
                <button class="tool-btn" type="button" onclick="document.getElementById('textModal').showModal()">
                    <img class="tool-icon" src="{{ asset('icons/tools/text.svg') }}" alt="Texto">
                    <span>Texto</span>
                </button>
                <button class="tool-btn" type="button" onclick="document.getElementById('imageModal').showModal()">
                    <img class="tool-icon" src="{{ asset('icons/tools/image.svg') }}" alt="Imagen">
                    <span>Imagen</span>
                </button>
                <button class="tool-btn" type="button" onclick="document.getElementById('videoModal').showModal()">
                    <img class="tool-icon" src="{{ asset('icons/tools/video.svg') }}" alt="Video">
                    <span>Video</span>
                </button>
                <button class="tool-btn" type="button" onclick="document.getElementById('fileModal').showModal()">
                    <img class="tool-icon" src="{{ asset('icons/tools/file.svg') }}" alt="Archivo">
                    <span>Archivo</span>
                </button>
                <button
                    class="tool-btn"
                    type="button"
                    onclick="document.getElementById('{{ $lesson->quiz ? 'addQuestionModal' : 'quizModal' }}').showModal()"
                >
                    <img class="tool-icon" src="{{ asset('icons/tools/quiz.svg') }}" alt="Cuestionario">
                    <span>Cuestionario</span>
                </button>
            </div>
        </aside>

        <section class="teacher-lesson-preview">
            <div class="card preview-card">
                <div class="preview-header">
                    <h3>Vista previa de la leccion</h3>
                    <span class="badge">Asi lo ve el alumno</span>
                </div>

                <div class="list">
                    <div class="card">
                        <h3>Video</h3>
                        @if (($preview['video'] ?? collect())->isEmpty())
                            <img class="lesson-video-placeholder" src="{{ asset('default-video.png') }}" alt="Video disponible pronto">
                        @else
                            @foreach ($preview['video'] as $content)
                                @if ($content->asset)
                                    <div class="lesson-preview-item">
                                        <video class="lesson-video" controls>
                                            <source src="{{ asset('storage/' . $content->asset->path) }}" type="{{ $content->asset->mime }}">
                                        </video>
                                        <form class="content-remove-form" method="POST" action="{{ route('teacher.lessons.content.destroy', $content) }}" data-confirm="Se quitara el contenido de la leccion.">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" type="submit">Quitar</button>
                                        </form>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>

                    <div class="card">
                        <h3>Imagenes</h3>
                        @if (($preview['image'] ?? collect())->isEmpty())
                            <p class="subtle">No hay imagenes.</p>
                        @else
                            <div class="grid">
                                @foreach ($preview['image'] as $content)
                                    @if ($content->asset)
                                        <div class="lesson-preview-item">
                                            <a class="content-image-link" href="{{ asset('storage/' . $content->asset->path) }}" target="_blank" rel="noopener">
                                                <img class="content-image" src="{{ asset('storage/' . $content->asset->path) }}" alt="Imagen de leccion">
                                            </a>
                                            <form class="content-remove-form" method="POST" action="{{ route('teacher.lessons.content.destroy', $content) }}" data-confirm="Se quitara el contenido de la leccion.">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger" type="submit">Quitar</button>
                                            </form>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="card">
                        <h3>Archivos</h3>
                        @if (($preview['file'] ?? collect())->isEmpty())
                            <p class="subtle">No hay archivos.</p>
                        @else
                            <div class="asset-list preview-file-list">
                                @foreach ($preview['file'] as $content)
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
                                        <form class="content-remove-form" method="POST" action="{{ route('teacher.lessons.content.destroy', $content) }}" data-confirm="Se quitara el contenido de la leccion.">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" type="submit">Quitar</button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                        @endif
                    </div>

                    <div class="card">
                        <h3>Textos</h3>
                        @if (($preview['text'] ?? collect())->isEmpty())
                            <p class="subtle">No hay textos.</p>
                        @else
                            <div class="list">
                                @foreach ($preview['text'] as $content)
                                    <div class="lesson-preview-item">
                                        <p>{{ $content->text_content }}</p>
                                        <div class="actions inline-actions">
                                            <button class="btn btn-secondary" type="button" onclick="document.getElementById('editTextModal-{{ $content->id }}').showModal()">Editar</button>
                                            <form method="POST" action="{{ route('teacher.lessons.content.destroy', $content) }}" data-confirm="Se quitara el contenido de la leccion.">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger" type="submit">Quitar</button>
                                            </form>
                                        </div>
                                    </div>
                                    <dialog id="editTextModal-{{ $content->id }}">
                                        <div class="modal-header">
                                            <strong>Editar texto</strong>
                                            <button class="btn btn-secondary" type="button" onclick="document.getElementById('editTextModal-{{ $content->id }}').close()">Cerrar</button>
                                        </div>
                                        <form method="POST" action="{{ route('teacher.lessons.content.update', $content) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body form-grid">
                                                <div>
                                                    <label for="text_content_{{ $content->id }}">Texto</label>
                                                    <textarea id="text_content_{{ $content->id }}" name="text_content" required>{{ $content->text_content }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-secondary" type="button" onclick="document.getElementById('editTextModal-{{ $content->id }}').close()">Cancelar</button>
                                                <button class="btn" type="submit">Guardar cambios</button>
                                            </div>
                                        </form>
                                    </dialog>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @if (! $lesson->quiz)
                        <div class="card">
                            <h3>Cuestionario</h3>
                            <p class="subtle">Crea un cuestionario desde la botonera de herramientas.</p>
                        </div>
                    @else
                        <div class="card">
                            <div class="quiz-header">
                                <div>
                                    <h3>Cuestionario</h3>
                                    <p class="subtle">{{ $lesson->quiz->title }}</p>
                                    @if ($lesson->quiz->description)
                                        <p>{{ $lesson->quiz->description }}</p>
                                    @endif
                                </div>
                                <div class="inline-actions">
                                    <button class="btn btn-secondary" type="button" onclick="document.getElementById('addQuestionModal').showModal()">Agregar pregunta</button>
                                    <form method="POST" action="{{ route('teacher.lessons.quiz.destroy', $lesson) }}" data-confirm="Se eliminara el cuestionario y sus preguntas.">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" type="submit">Eliminar</button>
                                    </form>
                                </div>
                            </div>

                            @if ($lesson->quiz->questions->isEmpty())
                                <p class="subtle">Todavia no hay preguntas.</p>
                            @else
                                <div class="list">
                                    @foreach ($lesson->quiz->questions as $question)
                                        <div class="card">
                                            <div class="actions inline-actions">
                                                <strong>{{ $loop->iteration }}. {{ $question->prompt }}</strong>
                                                <div class="actions inline-actions">
                                                    <button class="btn btn-secondary" type="button" onclick="document.getElementById('editQuestionModal-{{ $question->id }}').showModal()">Editar</button>
                                                    <form method="POST" action="{{ route('teacher.lessons.quiz.questions.destroy', $question) }}" data-confirm="Se eliminara la pregunta.">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-danger" type="submit">Eliminar</button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="list">
                                                @foreach ($question->options as $option)
                                                    <div>
                                                        <span>{{ $option->option_text }}</span>
                                                        @if ($option->is_correct)
                                                            <span class="badge">Correcta</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @php
                                            $optionsByPosition = $question->options->keyBy('position');
                                        @endphp
                                        <dialog id="editQuestionModal-{{ $question->id }}">
                                            <div class="modal-header">
                                                <strong>Editar pregunta</strong>
                                                <button class="btn btn-secondary" type="button" onclick="document.getElementById('editQuestionModal-{{ $question->id }}').close()">Cerrar</button>
                                            </div>
                                            <form method="POST" action="{{ route('teacher.lessons.quiz.questions.update', $question) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body form-grid">
                                                    <div>
                                                        <label for="question_prompt_{{ $question->id }}">Pregunta</label>
                                                        <textarea id="question_prompt_{{ $question->id }}" name="prompt" required>{{ $question->prompt }}</textarea>
                                                    </div>
                                                    <div>
                                                        <label for="question_option_1_{{ $question->id }}">Opcion 1</label>
                                                        <input id="question_option_1_{{ $question->id }}" name="option_1" type="text" required value="{{ $optionsByPosition->get(1)?->option_text }}">
                                                    </div>
                                                    <div>
                                                        <label for="question_option_2_{{ $question->id }}">Opcion 2</label>
                                                        <input id="question_option_2_{{ $question->id }}" name="option_2" type="text" required value="{{ $optionsByPosition->get(2)?->option_text }}">
                                                    </div>
                                                    <div>
                                                        <label for="question_option_3_{{ $question->id }}">Opcion 3 (opcional)</label>
                                                        <input id="question_option_3_{{ $question->id }}" name="option_3" type="text" value="{{ $optionsByPosition->get(3)?->option_text }}">
                                                    </div>
                                                    <div>
                                                        <label for="question_option_4_{{ $question->id }}">Opcion 4 (opcional)</label>
                                                        <input id="question_option_4_{{ $question->id }}" name="option_4" type="text" value="{{ $optionsByPosition->get(4)?->option_text }}">
                                                    </div>
                                                    <div>
                                                        <label for="question_correct_{{ $question->id }}">Opcion correcta</label>
                                                        <select id="question_correct_{{ $question->id }}" name="correct_option" required>
                                                            <option value="1" {{ $optionsByPosition->get(1)?->is_correct ? 'selected' : '' }}>Opcion 1</option>
                                                            <option value="2" {{ $optionsByPosition->get(2)?->is_correct ? 'selected' : '' }}>Opcion 2</option>
                                                            <option value="3" {{ $optionsByPosition->get(3)?->is_correct ? 'selected' : '' }}>Opcion 3</option>
                                                            <option value="4" {{ $optionsByPosition->get(4)?->is_correct ? 'selected' : '' }}>Opcion 4</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-secondary" type="button" onclick="document.getElementById('editQuestionModal-{{ $question->id }}').close()">Cancelar</button>
                                                    <button class="btn" type="submit">Guardar cambios</button>
                                                </div>
                                            </form>
                                        </dialog>
                                    @endforeach
                                </div>
                            @endif

                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>

    <dialog id="textModal">
        <div class="modal-header">
            <strong>Agregar texto</strong>
            <button class="btn btn-secondary" type="button" onclick="document.getElementById('textModal').close()">Cerrar</button>
        </div>
        <form method="POST" action="{{ route('teacher.lessons.content.store', $lesson) }}">
            @csrf
            <input type="hidden" name="type" value="text">
            <div class="modal-body form-grid">
                <div>
                    <label for="text_content">Texto</label>
                    <textarea id="text_content" name="text_content" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('textModal').close()">Cancelar</button>
                <button class="btn" type="submit">Guardar</button>
            </div>
        </form>
    </dialog>

    <dialog id="imageModal">
        <div class="modal-header">
            <strong>Agregar imagen</strong>
            <button class="btn btn-secondary" type="button" onclick="document.getElementById('imageModal').close()">Cerrar</button>
        </div>
        <form method="POST" action="{{ route('teacher.lessons.content.store', $lesson) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" value="image">
            <div class="modal-body form-grid">
                <div>
                    <label>Imagenes ya subidas</label>
                    <div class="asset-list asset-grid">
                        @forelse (($assets['image'] ?? collect()) as $asset)
                            <label class="asset-item asset-item--grid">
                                <input type="radio" name="asset_id" value="{{ $asset->id }}">
                                <img class="asset-thumb" src="{{ asset('storage/' . ($asset->thumb_path ?: $asset->path)) }}" alt="Miniatura de imagen">
                                <span class="asset-item__name">{{ $asset->original_name }}</span>
                            </label>
                        @empty
                            <p class="subtle">Sin imagenes.</p>
                        @endforelse
                    </div>
                </div>
                <div>
                    <label for="image_file">Subir nueva imagen</label>
                    <input id="image_file" name="file" type="file" accept="image/*">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('imageModal').close()">Cancelar</button>
                <button class="btn" type="submit">Guardar</button>
            </div>
        </form>
    </dialog>

    <dialog id="videoModal">
        <div class="modal-header">
            <strong>Agregar video</strong>
            <button class="btn btn-secondary" type="button" onclick="document.getElementById('videoModal').close()">Cerrar</button>
        </div>
        <form method="POST" action="{{ route('teacher.lessons.content.store', $lesson) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" value="video">
            <div class="modal-body form-grid">
                <div>
                    <label>Videos ya subidos</label>
                    <div class="asset-list asset-grid">
                        @forelse (($assets['video'] ?? collect()) as $asset)
                            <label class="asset-item asset-item--grid">
                                <input type="radio" name="asset_id" value="{{ $asset->id }}">
                                @if ($asset->thumb_path)
                                    <img class="asset-thumb" src="{{ asset('storage/' . $asset->thumb_path) }}" alt="Miniatura de video">
                                @else
                                    <img class="asset-thumb" src="{{ asset('default-video.png') }}" alt="Miniatura de video">
                                @endif
                                <span class="asset-item__name">{{ $asset->original_name }}</span>
                            </label>
                        @empty
                            <p class="subtle">Sin videos.</p>
                        @endforelse
                    </div>
                </div>
                <div>
                    <label for="video_file">Subir nuevo video</label>
                    <input id="video_file" name="file" type="file" accept="video/*">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('videoModal').close()">Cancelar</button>
                <button class="btn" type="submit">Guardar</button>
            </div>
        </form>
    </dialog>

    <dialog id="fileModal">
        <div class="modal-header">
            <strong>Agregar archivo</strong>
            <button class="btn btn-secondary" type="button" onclick="document.getElementById('fileModal').close()">Cerrar</button>
        </div>
        <form method="POST" action="{{ route('teacher.lessons.content.store', $lesson) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" value="file">
            <div class="modal-body form-grid">
                <div>
                    <label>Archivos ya subidos</label>
                    <div class="asset-list asset-grid">
                        @forelse (($assets['file'] ?? collect()) as $asset)
                            <label class="asset-item asset-item--grid">
                                <input type="radio" name="asset_id" value="{{ $asset->id }}">
                                @php
                                    $ext = strtolower(pathinfo($asset->original_name, PATHINFO_EXTENSION));
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
                                <img class="file-icon-img" src="{{ asset('icons/filetypes/' . $iconName . '.svg') }}" alt="{{ $extLabel }}">
                                <span class="asset-item__name">{{ $asset->original_name }}</span>
                            </label>
                        @empty
                            <p class="subtle">Sin archivos.</p>
                        @endforelse
                    </div>
                </div>
                <div>
                    <label for="generic_file">Subir nuevo archivo</label>
                    <input id="generic_file" name="file" type="file">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('fileModal').close()">Cancelar</button>
                <button class="btn" type="submit">Guardar</button>
            </div>
        </form>
    </dialog>

    @if (! $lesson->quiz)
        <dialog id="quizModal">
            <div class="modal-header">
                <strong>Crear cuestionario</strong>
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('quizModal').close()">Cerrar</button>
            </div>
            <form method="POST" action="{{ route('teacher.lessons.quiz.store', $lesson) }}">
                @csrf
                <div class="modal-body form-grid" id="quiz-step-1">
                    <div>
                        <label for="quiz_title">Titulo</label>
                        <input id="quiz_title" name="title" type="text" required>
                    </div>
                    <div>
                        <label for="quiz_description">Descripcion</label>
                        <textarea id="quiz_description" name="description"></textarea>
                    </div>
                </div>
                <div class="modal-body form-grid" id="quiz-step-2" style="display: none;">
                    <div>
                        <label for="quiz_prompt">Pregunta</label>
                        <textarea id="quiz_prompt" name="prompt" required></textarea>
                    </div>
                    <div>
                        <label for="quiz_option_1">Opcion 1</label>
                        <input id="quiz_option_1" name="option_1" type="text" required>
                    </div>
                    <div>
                        <label for="quiz_option_2">Opcion 2</label>
                        <input id="quiz_option_2" name="option_2" type="text" required>
                    </div>
                    <div>
                        <label for="quiz_option_3">Opcion 3 (opcional)</label>
                        <input id="quiz_option_3" name="option_3" type="text">
                    </div>
                    <div>
                        <label for="quiz_option_4">Opcion 4 (opcional)</label>
                        <input id="quiz_option_4" name="option_4" type="text">
                    </div>
                    <div>
                        <label for="quiz_correct_option">Opcion correcta</label>
                        <select id="quiz_correct_option" name="correct_option" required>
                            <option value="1">Opcion 1</option>
                            <option value="2">Opcion 2</option>
                            <option value="3">Opcion 3</option>
                            <option value="4">Opcion 4</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" onclick="document.getElementById('quizModal').close()">Cancelar</button>
                    <button class="btn btn-secondary" type="button" id="quiz-next-step">Siguiente</button>
                    <button class="btn btn-secondary" type="button" id="quiz-prev-step" style="display: none;">Volver</button>
                    <button class="btn" type="submit" id="quiz-save" style="display: none;">Crear cuestionario</button>
                </div>
            </form>
        </dialog>
        <script>
            (() => {
                const modal = document.getElementById('quizModal');
                if (!modal) {
                    return;
                }
                const step1 = document.getElementById('quiz-step-1');
                const step2 = document.getElementById('quiz-step-2');
                const nextBtn = document.getElementById('quiz-next-step');
                const prevBtn = document.getElementById('quiz-prev-step');
                const saveBtn = document.getElementById('quiz-save');

                const showStep1 = () => {
                    step1.style.display = 'grid';
                    step2.style.display = 'none';
                    nextBtn.style.display = 'inline-flex';
                    prevBtn.style.display = 'none';
                    saveBtn.style.display = 'none';
                };

                const showStep2 = () => {
                    step1.style.display = 'none';
                    step2.style.display = 'grid';
                    nextBtn.style.display = 'none';
                    prevBtn.style.display = 'inline-flex';
                    saveBtn.style.display = 'inline-flex';
                };

                nextBtn.addEventListener('click', showStep2);
                prevBtn.addEventListener('click', showStep1);
                modal.addEventListener('close', showStep1);
            })();
        </script>
    @else

        <dialog id="addQuestionModal">
            <div class="modal-header">
                <strong>Agregar pregunta</strong>
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('addQuestionModal').close()">Cerrar</button>
            </div>
            <form method="POST" action="{{ route('teacher.lessons.quiz.questions.store', $lesson) }}">
                @csrf
                <div class="modal-body form-grid">
                    <div>
                        <label for="question_prompt">Pregunta</label>
                        <textarea id="question_prompt" name="prompt" required></textarea>
                    </div>
                    <div>
                        <label for="option_1">Opcion 1</label>
                        <input id="option_1" name="option_1" type="text" required>
                    </div>
                    <div>
                        <label for="option_2">Opcion 2</label>
                        <input id="option_2" name="option_2" type="text" required>
                    </div>
                    <div>
                        <label for="option_3">Opcion 3 (opcional)</label>
                        <input id="option_3" name="option_3" type="text">
                    </div>
                    <div>
                        <label for="option_4">Opcion 4 (opcional)</label>
                        <input id="option_4" name="option_4" type="text">
                    </div>
                    <div>
                        <label for="correct_option">Opcion correcta</label>
                        <select id="correct_option" name="correct_option" required>
                            <option value="1">Opcion 1</option>
                            <option value="2">Opcion 2</option>
                            <option value="3">Opcion 3</option>
                            <option value="4">Opcion 4</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" onclick="document.getElementById('addQuestionModal').close()">Cancelar</button>
                    <button class="btn" type="submit">Agregar pregunta</button>
                </div>
            </form>
        </dialog>
    @endif

    <div class="card card-spaced">
        <h3>Comentarios</h3>
        <form method="POST" action="{{ route('lessons.comments.store', $lesson) }}" class="form-grid">
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
@endsection
