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
        <a class="btn btn-secondary" href="{{ route('teacher.tasks.edit', $task) }}">Volver</a>
    </div>

    <div class="widget widget-wide">
        <div class="widget-header">
            <h3>{{ $task->title }}</h3>
            <span class="subtle">Tipo: {{ $typeLabel }}</span>
        </div>

        <form method="POST" action="{{ route('teacher.tasks.builder.store', $task) }}" class="form-grid form-grid-full" id="task-builder-form">
            @csrf

            @if ($task->task_type === 'mixed')
                <div>
                    <label>Items</label>
                    <div id="mixed-items"></div>
                    <button class="btn btn-secondary" id="mixed-add-item" type="button">Agregar item</button>
                </div>
                <template id="mixed-item-template">
                    <details class="card mixed-item" data-index="__INDEX__">
                        <summary class="actions actions-tight">
                            <strong class="mixed-item-title">Item __NUM__</strong>
                            <span class="subtle">Click para editar</span>
                            <span class="menu-chevron" aria-hidden="true">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M6 9l6 6 6-6"></path>
                                </svg>
                            </span>
                        </summary>
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
                    </details>
                </template>
            @elseif ($task->task_type === 'file_delivery')
                <div>
                    <label for="file-instructions">Instrucciones</label>
                    <textarea id="file-instructions" name="instructions" rows="5" required>{{ old('instructions') }}</textarea>
                </div>
                <p class="subtle">El usuario subira un archivo como entrega.</p>
            @else
                <p class="subtle">Este tipo de tarea ya no se encuentra disponible.</p>
            @endif

            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Guardar contenido</button>
            </div>
        </form>
    </div>

    <script>
        (function () {
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

                var updateMixedTitles = function () {
                    var items = mixedItems.querySelectorAll('.mixed-item');
                    items.forEach(function (itemEl, position) {
                        var titleEl = itemEl.querySelector('.mixed-item-title');
                        var questionInput = itemEl.querySelector('textarea');
                        var base = 'Item ' + (position + 1);
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

                var updateMixedIndices = function () {
                    var items = mixedItems.querySelectorAll('.mixed-item');
                    items.forEach(function (itemEl, position) {
                        itemEl.setAttribute('data-index', String(position));
                        var typeSelect = itemEl.querySelector('.mixed-type');
                        if (typeSelect) {
                            typeSelect.name = 'items[' + position + '][type]';
                        }
                        var questionInput = itemEl.querySelector('textarea');
                        if (questionInput) {
                            questionInput.name = 'items[' + position + '][question]';
                        }
                        var optionInputs = itemEl.querySelectorAll('.mixed-options input[type="text"]');
                        optionInputs.forEach(function (input) {
                            input.name = 'items[' + position + '][options][]';
                        });
                        var radioInputs = itemEl.querySelectorAll('.mixed-options input[type="radio"]');
                        radioInputs.forEach(function (input, optionIndex) {
                            input.name = 'items[' + position + '][correct_option]';
                            input.value = String(optionIndex);
                        });
                    });
                    updateMixedTitles();
                };

                var addMixedItem = function () {
                    var index = mixedItems.querySelectorAll('.mixed-item').length;
                    var html = mixedTemplate.innerHTML
                        .replace(/__INDEX__/g, String(index))
                        .replace(/__NUM__/g, String(index + 1));
                    var wrapper = document.createElement('div');
                    wrapper.innerHTML = html.trim();
                    var itemEl = wrapper.firstElementChild;
                    mixedItems.appendChild(itemEl);

                    var typeSelect = itemEl.querySelector('.mixed-type');
                    var addOptionBtn = itemEl.querySelector('.mixed-add-option');
                    var questionInput = itemEl.querySelector('textarea');

                    addOptionBtn.addEventListener('click', function () {
                        addMixedOption(itemEl);
                    });

                    typeSelect.addEventListener('change', function () {
                        toggleMixedType(itemEl, typeSelect.value);
                    });

                    if (questionInput) {
                        questionInput.addEventListener('input', updateMixedTitles);
                    }

                    toggleMixedType(itemEl, typeSelect.value);
                    updateMixedIndices();
                };

                mixedAdd.addEventListener('click', addMixedItem);
                addMixedItem();
            }
        })();
    </script>
@endsection
