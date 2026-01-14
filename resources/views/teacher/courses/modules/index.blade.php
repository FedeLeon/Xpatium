@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1>Modulos y lecciones</h1>
            <p class="subtle">{{ $course->name }}</p>
            <button class="btn btn-secondary btn-icon" type="button" onclick="document.getElementById('moduleModal').showModal()">
                <span>➕</span>
                <span>Nuevo modulo</span>
            </button>
        </div>
        <div>
            <a class="btn btn-secondary" href="{{ route('teacher.courses.show', $course) }}">Volver</a>
        </div>
    </div>

    <div class="info-bubble">
        Organiza tu curso en modulos y agrega lecciones debajo. Los alumnos veran el contenido en este orden.
    </div>

    @if ($course->modules->isEmpty())
        <p>No hay modulos creados.</p>
    @endif

    <div class="list">
        @foreach ($course->modules as $module)
            <details class="module-accordion">
                <summary class="module-summary">
                    <div>
                        <h3>{{ $module->title }}</h3>
                        <p>{{ $module->subtitle }}</p>
                    </div>
                    <div class="module-actions">
                        <span class="badge">{{ $module->lessons->count() }} lecciones</span>
                        <button class="btn btn-secondary" type="button" onclick="event.stopPropagation(); document.getElementById('lessonModal-{{ $module->id }}').showModal();">Agregar leccion</button>
                        <button class="btn btn-secondary" type="button" onclick="event.stopPropagation(); document.getElementById('editModuleModal-{{ $module->id }}').showModal();">Editar modulo</button>
                        <form method="POST" action="{{ route('teacher.courses.modules.destroy', $module) }}" data-confirm="Se eliminara el modulo y sus lecciones." onclick="event.stopPropagation();">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-secondary" type="submit">Eliminar</button>
                        </form>
                    </div>
                </summary>
                <div class="module-body">
                    @if ($module->description)
                        <p class="subtle">{{ $module->description }}</p>
                    @endif
                    @if ($module->lessons->isNotEmpty())
                        <div class="list lesson-list">
                            @foreach ($module->lessons as $lesson)
                                <div class="asset-item">
                                    <strong>
                                        <a href="{{ route('teacher.lessons.show', $lesson) }}">{{ $lesson->title }}</a>
                                    </strong>
                                    <form method="POST" action="{{ route('teacher.courses.lessons.destroy', $lesson) }}" data-confirm="Se eliminara la leccion." onclick="event.stopPropagation();">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-secondary" type="submit">Eliminar</button>
                                    </form>
                                </div>
                                @if ($lesson->description)
                                    <div class="subtle">{{ $lesson->description }}</div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <p class="subtle">No hay lecciones todavia.</p>
                    @endif
                </div>
            </details>

            <dialog id="lessonModal-{{ $module->id }}">
                <div class="modal-header">
                    <strong>Nueva leccion</strong>
                    <button class="btn btn-secondary" type="button" onclick="document.getElementById('lessonModal-{{ $module->id }}').close()">Cerrar</button>
                </div>
                <form method="POST" action="{{ route('teacher.courses.lessons.store', $module) }}">
                    @csrf
                    <div class="modal-body form-grid">
                        <div>
                            <label for="title-{{ $module->id }}">Titulo</label>
                            <input id="title-{{ $module->id }}" name="title" type="text" required>
                        </div>
                        <div>
                            <label for="description-{{ $module->id }}">Descripcion</label>
                            <textarea id="description-{{ $module->id }}" name="description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" onclick="document.getElementById('lessonModal-{{ $module->id }}').close()">Cancelar</button>
                        <button class="btn" type="submit">Guardar leccion</button>
                    </div>
                </form>
            </dialog>

            <dialog id="editModuleModal-{{ $module->id }}">
                <div class="modal-header">
                    <strong>Editar modulo</strong>
                    <button class="btn btn-secondary" type="button" onclick="document.getElementById('editModuleModal-{{ $module->id }}').close()">Cerrar</button>
                </div>
                <form method="POST" action="{{ route('teacher.courses.modules.update', $module) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body form-grid">
                        <div>
                            <label for="edit-title-{{ $module->id }}">Titulo</label>
                            <input id="edit-title-{{ $module->id }}" name="title" type="text" value="{{ $module->title }}" required>
                        </div>
                        <div>
                            <label for="edit-subtitle-{{ $module->id }}">Subtitulo</label>
                            <input id="edit-subtitle-{{ $module->id }}" name="subtitle" type="text" value="{{ $module->subtitle }}">
                        </div>
                        <div>
                            <label for="edit-description-{{ $module->id }}">Descripcion</label>
                            <textarea id="edit-description-{{ $module->id }}" name="description">{{ $module->description }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" onclick="document.getElementById('editModuleModal-{{ $module->id }}').close()">Cancelar</button>
                        <button class="btn" type="submit">Guardar cambios</button>
                    </div>
                </form>
            </dialog>
        @endforeach
    </div>

    <dialog id="moduleModal">
        <div class="modal-header">
            <strong>Nuevo modulo</strong>
            <button class="btn btn-secondary" type="button" onclick="document.getElementById('moduleModal').close()">Cerrar</button>
        </div>
        <form method="POST" action="{{ route('teacher.courses.modules.store', $course) }}">
            @csrf
            <div class="modal-body form-grid">
                <div>
                    <label for="title">Titulo</label>
                    <input id="title" name="title" type="text" required>
                </div>
                <div>
                    <label for="subtitle">Subtitulo</label>
                    <input id="subtitle" name="subtitle" type="text">
                </div>
                <div>
                    <label for="description">Descripcion</label>
                    <textarea id="description" name="description"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('moduleModal').close()">Cancelar</button>
                <button class="btn" type="submit">Guardar modulo</button>
            </div>
        </form>
    </dialog>
@endsection
