@extends('layouts.app')

@section('content')
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

        <form method="POST" action="{{ route('teacher.tasks.builder.store', $task) }}" class="form-grid form-grid-full">
            @csrf

            @if ($task->task_type === 'open_question')
                <div>
                    <label for="open-question">Pregunta</label>
                    <textarea id="open-question" name="question" rows="5" required>{{ old('question') }}</textarea>
                </div>
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
                    var titleEl = questionEl.querySelector('.mc-question-title');

                    var updateTitle = function () {
                        var base = 'Pregunta ' + (index + 1);
                        if (!questionInput || !titleEl) {
                            return;
                        }
                        var text = questionInput.value.trim();
                        if (text.length) {
                            var snippet = text.length > 40 ? text.slice(0, 40) + '...' : text;
                            titleEl.textContent = base + ': ' + snippet;
                        } else {
                            titleEl.textContent = base;
                        }
                    };

                    addOptionBtn.addEventListener('click', function () {
                        addMcOption(questionEl);
                    });

                    questionInput.addEventListener('input', updateTitle);
                    updateTitle();

                    addMcOption(questionEl);
                    addMcOption(questionEl);
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
