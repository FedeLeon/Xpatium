@extends('layouts.app')

@section('content')
    @push('styles')
        <style>
            .task-detail-list {
                display: grid;
                gap: 16px;
            }

            .task-question {
                display: grid;
                gap: 10px;
            }

            .option-list {
                display: grid;
                gap: 8px;
            }

            .option-row {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 8px 12px;
                border-radius: 10px;
                border: 1px solid rgba(148, 163, 184, 0.25);
                background: rgba(15, 23, 42, 0.55);
            }

            .option-row.is-correct {
                border-color: rgba(34, 197, 94, 0.6);
                box-shadow: 0 10px 18px rgba(34, 197, 94, 0.15);
                background: rgba(22, 163, 74, 0.12);
            }

            .option-letter {
                width: 26px;
                height: 26px;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 12px;
                color: #e2e8f0;
                background: rgba(148, 163, 184, 0.18);
            }

            .option-row.is-correct .option-letter {
                background: rgba(34, 197, 94, 0.3);
                color: #bbf7d0;
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
                Detalle de tarea
            </h1>
            <p>{{ $task->title }}</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('teacher.tasks.index') }}">Volver</a>
    </div>

    <div class="widget widget-wide">
        <div class="widget-header">
            <h3>Contenido</h3>
        </div>
        @if ($task->items->isEmpty())
            <p class="subtle">No hay contenido cargado.</p>
        @else
            @php
                $typeLabels = [
                    'file_delivery' => 'Entrega de archivo',
                    'mixed' => 'Mixta',
                    'open_question' => 'Pregunta abierta',
                    'multiple_choice' => 'Multiple choice',
                ];
            @endphp
            <div class="task-detail-list">
                @foreach ($task->items as $item)
                    @php
                        $typeLabel = $typeLabels[$item->type] ?? $item->type;
                        $content = $item->content ?? [];
                    @endphp
                    <div class="card">
                        <div class="actions actions-tight">
                            <strong>{{ $typeLabel }}</strong>
                        </div>

                        @if ($item->type === 'file_delivery')
                            <div class="task-question">
                                <strong>Instrucciones</strong>
                                <p>{{ $content['instructions'] ?? '—' }}</p>
                                <p class="subtle">El alumno sube un archivo como entrega.</p>
                            </div>
                        @elseif ($item->type === 'mixed')
                            @php
                                $items = $content['items'] ?? [];
                            @endphp
                            @forelse ($items as $index => $mixedItem)
                                @php
                                    $mixedType = $mixedItem['type'] ?? 'open_question';
                                    $mixedLabel = $typeLabels[$mixedType] ?? $mixedType;
                                @endphp
                                <details class="task-question">
                                    <summary class="actions actions-tight">
                                        <strong>Item {{ $index + 1 }}</strong>
                                        <span class="subtle">{{ $mixedLabel }}</span>
                                        <span class="menu-chevron" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M6 9l6 6 6-6"></path>
                                            </svg>
                                        </span>
                                    </summary>
                                    <p>{{ $mixedItem['question'] ?? '—' }}</p>
                                    @if ($mixedType === 'multiple_choice')
                                        @php
                                            $options = $mixedItem['options'] ?? [];
                                            $correctIndex = (int) ($mixedItem['correct_option'] ?? -1);
                                        @endphp
                                        <div class="option-list">
                                            @foreach ($options as $optionIndex => $option)
                                                <div class="option-row {{ $optionIndex === $correctIndex ? 'is-correct' : '' }}">
                                                    <span class="option-letter">{{ chr(65 + $optionIndex) }}</span>
                                                    <span>{{ $option }}</span>
                                                    @if ($optionIndex === $correctIndex)
                                                        <span class="badge">Correcta</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </details>
                            @empty
                                <p class="subtle">No hay items cargados.</p>
                            @endforelse
                        @elseif ($item->type === 'review')
                            <p class="subtle">Este tipo ya no se encuentra disponible.</p>
                        @else
                            <p class="subtle">Este tipo ya no se encuentra disponible.</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
