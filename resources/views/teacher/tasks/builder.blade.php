@extends('layouts.app')

@section('content')
    @push('styles')
        <style>
            .mc-option input[type="text"],
            .mixed-option input[type="text"] {
                flex: 1;
                min-width: 180px;
                padding: 8px 10px;
                border-radius: 10px;
                border: 1px solid rgba(148, 163, 184, 0.35);
                background: rgba(15, 23, 42, 0.6);
                color: #e2e8f0;
            }

            .mc-option input[type="text"]::placeholder,
            .mixed-option input[type="text"]::placeholder {
                color: rgba(148, 163, 184, 0.7);
            }
        </style>
    @endpush

    <div class="actions">
        <div>
            <h1 class="view-title">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <path d="M14 2v6h6"></path>
                    <path d="M16 13H8"></path>
                    <path d="M16 17H8"></path>
                    <path d="M10 9H8"></path>
                </svg>
                Builder de tarea
            </h1>
            <p>Tipo seleccionado: {{ $typeLabel }}</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('teacher.tasks.index') }}">Volver</a>
    </div>

    <div class="widget widget-wide">
        <div class="widget-header">
            <h3>{{ $task->title }}</h3>
            <span class="subtle">Tipo: {{ $typeLabel }}</span>
        </div>

        <form method="POST" action="{{ route('teacher.tasks.builder.store', $task) }}" class="form-grid form-grid-full" id="task-builder-form">
            @csrf

            @if ($task->task_type === 'open_question')
                <div>
                    <label>Preguntas</label>
                    <div id="open-questions"></div>
                    <button class="btn btn-secondary" id="open-add-question" type="button">Agregar pregunta</button>
                </div>
                <template id="open-question-template">
                    <details class="card open-question" data-index="__INDEX__">
                        <summary class="actions actions-tight">
                            <strong class="open-question-title">Pregunta __NUM__</strong>
                            <span class="subtle">Click para editar</span>
                            <span class="menu-chevron" aria-hidden="true">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M6 9l6 6 6-6"></path>
                                </svg>
                            </span>
                        </summary>
                        <div class="actions actions-tight">
                            <button class="btn btn-danger btn-icon open-remove-question" type="button">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M3 6h18"></path>
                                    <path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2"></path>
                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                    <path d="M10 11v6"></path>
                                    <path d="M14 11v6"></path>
                                </svg>
                                Eliminar pregunta
                            </button>
                        </div>
                        <div class="form-grid form-grid-full">
                            <div>
                                <label for="open-question-__INDEX__">Pregunta</label>
                                <textarea id="open-question-__INDEX__" name="questions[__INDEX__][question]" rows="4" required></textarea>
                            </div>
                        </div>
                    </details>
                </template>
            @elseif ($task->task_type === 'multiple_choice')
                <div>
                    <label>Preguntas</label>
                    <div id="mc-questions"></div>
                    <button class="btn btn-secondary" id="mc-add-question" type="button">Agregar pregunta</button>
                </div>
                <template id="mc-question-template">
                    <details class="card mc-question" data-index="__INDEX__">
                        <summary class="actions actions-tight">
                            <strong class="mc-question-title">Pregunta __NUM__</strong>
                            <span class="subtle">Click para editar</span>
                            <span class="menu-chevron" aria-hidden="true">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M6 9l6 6 6-6"></path>
                                </svg>
                            </span>
                        </summary>
                        <div class="actions actions-tight">
                            <button class="btn btn-danger btn-icon mc-remove-question" type="button">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M3 6h18"></path>
                                    <path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2"></path>
                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                    <path d="M10 11v6"></path>
                                    <path d="M14 11v6"></path>
                                </svg>
                                Eliminar pregunta
                            </button>
                        </div>
                        <div class="form-grid form-grid-full">
                            <div>
                                <label for="mc-question-__INDEX__">Pregunta</label>
                                <textarea id="mc-question-__INDEX__" name="questions[__INDEX__][question]" rows="4" required></textarea>
                            </div>
                            <div>
                                <label>Opciones</label>
                                <div class="mc-options"></div>
                                <button class="btn btn-secondary mc-add-option" type="button">Agregar opcion</button>
                            </div>
                        </div>
                    </details>
                </template>
            @elseif ($task->task_type === 'file_delivery')
                <div>
                    <label for="file-instructions">Instrucciones</label>
                    <textarea id="file-instructions" name="instructions" rows="5" required>{{ old('instructions') }}</textarea>
                </div>
                <p class="subtle">El alumno subira un archivo como entrega.</p>
            @elseif ($task->task_type === 'mixed')
                <div>
                    <label>Items</label>
                    <div id="mixed-items"></div>
                    <button class="btn btn-secondary" id="mixed-add-item" type="button">Agregar item</button>
                </div>
                <template id="mixed-item-template">
                    <div class="card mixed-item" data-index="__INDEX__">
                        <div class="form-grid form-grid-full">
                            <div>
                                <label for="mixed-type-__INDEX__">Tipo de item</label>
                                <select id="mixed-type-__INDEX__" name="items[__INDEX__][type]" class="mixed-type" required>
                                    <option value="open_question">Pregunta abierta</option>
                                    <option value="multiple_choice">Multiple choice</option>
                                </select>
                            </div>
                            <div>
                                <label for="mixed-question-__INDEX__">Pregunta</label>
                                <textarea id="mixed-question-__INDEX__" name="items[__INDEX__][question]" rows="4" required></textarea>
                            </div>
                            <div class="mixed-options-wrap" hidden>
                                <label>Opciones</label>
                                <div class="mixed-options"></div>
                                <button class="btn btn-secondary mixed-add-option" type="button">Agregar opcion</button>
                            </div>
                        </div>
                    </div>
                </template>
            @elseif ($task->task_type === 'review')
                <p class="subtle">El alumno debera revisar el material antes de continuar.</p>
                <label class="checkbox">
                    <input type="checkbox" name="requires_completion" value="1">
                    El alumno debe marcar como completado
                </label>
            @endif

            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Guardar contenido</button>
            </div>
        </form>
    </div>

    <script>
        (function () {
            var builderForm = document.getElementById('task-builder-form');
            var openAddQuestion = document.getElementById('open-add-question');
            var openQuestionTemplate = document.getElementById('open-question-template');
            var openQuestions = document.getElementById('open-questions');

            if (openAddQuestion && openQuestionTemplate && openQuestions) {
                var updateOpenTitles = function () {
                    var questions = openQuestions.querySelectorAll('.open-question');
                    questions.forEach(function (questionEl, position) {
                        var titleEl = questionEl.querySelector('.open-question-title');
                        var questionInput = questionEl.querySelector('textarea');
                        var base = 'Pregunta ' + (position + 1);
                        if (titleEl && questionInput) {
                            var text = questionInput.value.trim();
                            if (text.length) {
                                var snippet = text.length > 40 ? text.slice(0, 40) + '...' : text;
                                titleEl.textContent = base + ': ' + snippet;
                            } else {
                                titleEl.textContent = base;
                            }
                        }
                    });
                };

                var updateOpenIndices = function () {
                    var questions = openQuestions.querySelectorAll('.open-question');
                    questions.forEach(function (questionEl, position) {
                        questionEl.setAttribute('data-index', String(position));
                        var textarea = questionEl.querySelector('textarea');
                        if (textarea) {
                            textarea.name = 'questions[' + position + '][question]';
                        }
                        var removeBtn = questionEl.querySelector('.open-remove-question');
                        if (removeBtn) {
                            removeBtn.disabled = questions.length <= 1;
                        }
                    });
                    updateOpenTitles();
                };

                var addOpenQuestion = function () {
                    var index = openQuestions.querySelectorAll('.open-question').length;
                    var html = openQuestionTemplate.innerHTML
                        .replace(/__INDEX__/g, String(index))
                        .replace(/__NUM__/g, String(index + 1));
                    var wrapper = document.createElement('div');
                    wrapper.innerHTML = html.trim();
                    var questionEl = wrapper.firstElementChild;
                    openQuestions.appendChild(questionEl);

                    var questionInput = questionEl.querySelector('textarea');
                    var removeBtn = questionEl.querySelector('.open-remove-question');

                    questionInput.addEventListener('input', updateOpenTitles);
                    if (removeBtn) {
                        removeBtn.addEventListener('click', function () {
                            questionEl.remove();
                            updateOpenIndices();
                        });
                    }

                    updateOpenIndices();
                };

                openAddQuestion.addEventListener('click', addOpenQuestion);
                addOpenQuestion();
            }

            if (builderForm && openQuestions) {
                builderForm.addEventListener('submit', function (event) {
                    var questionBlocks = openQuestions.querySelectorAll('.open-question');
                    questionBlocks.forEach(function (block) {
                        var textarea = block.querySelector('textarea');
                        if (textarea && !textarea.value.trim()) {
                            block.remove();
                        }
                    });
                    var remaining = openQuestions.querySelectorAll('.open-question').length;
                    if (!remaining) {
                        event.preventDefault();
                        alert('Agrega al menos una pregunta.');
                        addOpenQuestion();
                    } else {
                        updateOpenIndices();
                    }
                });
            }

            var mcAddQuestion = document.getElementById('mc-add-question');
            var mcQuestionTemplate = document.getElementById('mc-question-template');
            var mcQuestions = document.getElementById('mc-questions');

            if (mcAddQuestion && mcQuestionTemplate && mcQuestions) {
                var addMcOption = function (questionEl) {
                    var index = questionEl.getAttribute('data-index');
                    var optionsWrap = questionEl.querySelector('.mc-options');
                    var optionIndex = optionsWrap.querySelectorAll('.mc-option').length;
                    var option = document.createElement('label');
                    option.className = 'checkbox mc-option';
                    option.innerHTML = ''
                        + '<input type="radio" name="questions[' + index + '][correct_option]" value="' + optionIndex + '">'
                        + '<input type="text" name="questions[' + index + '][options][]" placeholder="Opcion ' + (optionIndex + 1) + '" required>';
                    optionsWrap.appendChild(option);
                };

                var updateMcTitles = function () {
                    var questions = mcQuestions.querySelectorAll('.mc-question');
                    questions.forEach(function (questionEl, position) {
                        var titleEl = questionEl.querySelector('.mc-question-title');
                        var questionInput = questionEl.querySelector('textarea');
                        var base = 'Pregunta ' + (position + 1);
                        if (titleEl && questionInput) {
                            var text = questionInput.value.trim();
                            if (text.length) {
                                var snippet = text.length > 40 ? text.slice(0, 40) + '...' : text;
                                titleEl.textContent = base + ': ' + snippet;
                            } else {
                                titleEl.textContent = base;
                            }
                        }
                    });
                };

                var updateMcIndices = function () {
                    var questions = mcQuestions.querySelectorAll('.mc-question');
                    questions.forEach(function (questionEl, position) {
                        questionEl.setAttribute('data-index', String(position));
                        var textarea = questionEl.querySelector('textarea');
                        if (textarea) {
                            textarea.name = 'questions[' + position + '][question]';
                        }
                        var optionInputs = questionEl.querySelectorAll('.mc-options input[type="text"]');
                        optionInputs.forEach(function (input) {
                            input.name = 'questions[' + position + '][options][]';
                        });
                        var radioInputs = questionEl.querySelectorAll('.mc-options input[type="radio"]');
                        radioInputs.forEach(function (input, optionIndex) {
                            input.name = 'questions[' + position + '][correct_option]';
                            input.value = String(optionIndex);
                        });
                        var removeBtn = questionEl.querySelector('.mc-remove-question');
                        if (removeBtn) {
                            removeBtn.disabled = questions.length <= 1;
                        }
                    });
                    updateMcTitles();
                };

                var addMcQuestion = function () {
                    var index = mcQuestions.querySelectorAll('.mc-question').length;
                    var html = mcQuestionTemplate.innerHTML
                        .replace(/__INDEX__/g, String(index))
                        .replace(/__NUM__/g, String(index + 1));
                    var wrapper = document.createElement('div');
                    wrapper.innerHTML = html.trim();
                    var questionEl = wrapper.firstElementChild;
                    mcQuestions.appendChild(questionEl);

                    var addOptionBtn = questionEl.querySelector('.mc-add-option');
                    var questionInput = questionEl.querySelector('textarea');
                    var removeBtn = questionEl.querySelector('.mc-remove-question');

                    addOptionBtn.addEventListener('click', function () {
                        addMcOption(questionEl);
                    });

                    questionInput.addEventListener('input', updateMcTitles);
                    if (removeBtn) {
                        removeBtn.addEventListener('click', function () {
                            questionEl.remove();
                            updateMcIndices();
                        });
                    }

                    addMcOption(questionEl);
                    addMcOption(questionEl);
                    updateMcIndices();
                };

                mcAddQuestion.addEventListener('click', addMcQuestion);
                addMcQuestion();
            }

            var mixedItems = document.getElementById('mixed-items');
            var mixedAdd = document.getElementById('mixed-add-item');
            var mixedTemplate = document.getElementById('mixed-item-template');

            if (mixedItems && mixedAdd && mixedTemplate) {
                var addMixedOption = function (itemEl) {
                    var index = itemEl.getAttribute('data-index');
                    var optionsWrap = itemEl.querySelector('.mixed-options');
                    var optionIndex = optionsWrap.querySelectorAll('.mixed-option').length;
                    var option = document.createElement('label');
                    option.className = 'checkbox mixed-option';
                    option.innerHTML = ''
                        + '<input type="radio" name="items[' + index + '][correct_option]" value="' + optionIndex + '">'
                        + '<input type="text" name="items[' + index + '][options][]" placeholder="Opcion ' + (optionIndex + 1) + '">';
                    optionsWrap.appendChild(option);
                };

                var toggleMixedType = function (itemEl, type) {
                    var optionsWrap = itemEl.querySelector('.mixed-options-wrap');
                    var addOptionBtn = itemEl.querySelector('.mixed-add-option');
                    var inputs = optionsWrap.querySelectorAll('input');
                    var isMultiple = type === 'multiple_choice';
                    optionsWrap.hidden = !isMultiple;
                    inputs.forEach(function (input) {
                        input.disabled = !isMultiple;
                        if (!isMultiple) {
                            input.checked = false;
                            input.value = '';
                        }
                    });
                    addOptionBtn.disabled = !isMultiple;
                    if (isMultiple && !optionsWrap.querySelectorAll('.mixed-option').length) {
                        addMixedOption(itemEl);
                        addMixedOption(itemEl);
                    }
                };

                var addMixedItem = function () {
                    var index = mixedItems.querySelectorAll('.mixed-item').length;
                    var html = mixedTemplate.innerHTML.replace(/__INDEX__/g, String(index));
                    var wrapper = document.createElement('div');
                    wrapper.innerHTML = html.trim();
                    var itemEl = wrapper.firstElementChild;
                    mixedItems.appendChild(itemEl);

                    var typeSelect = itemEl.querySelector('.mixed-type');
                    var addOptionBtn = itemEl.querySelector('.mixed-add-option');

                    addOptionBtn.addEventListener('click', function () {
                        addMixedOption(itemEl);
                    });

                    typeSelect.addEventListener('change', function () {
                        toggleMixedType(itemEl, typeSelect.value);
                    });

                    toggleMixedType(itemEl, typeSelect.value);
                };

                mixedAdd.addEventListener('click', addMixedItem);
                addMixedItem();
            }
        })();
    </script>
@endsection
