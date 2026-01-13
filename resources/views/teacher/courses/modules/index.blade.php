@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1>Modulos y lecciones</h1>
            <p class="subtle">{{ $course->name }}</p>
        </div>
        <div>
            <a class="btn btn-secondary" href="{{ route('teacher.courses.show', $course) }}">Volver</a>
            <button class="btn" type="button" onclick="document.getElementById('moduleModal').showModal()">Nuevo modulo</button>
        </div>
    </div>

    @if ($course->modules->isEmpty())
        <p>No hay modulos creados.</p>
    @endif

    <div class="list">
        @foreach ($course->modules as $module)
            <div class="card">
                <div>
                    <h3>{{ $module->title }}</h3>
                    <p>{{ $module->subtitle }}</p>
                    <p class="subtle">{{ $module->description }}</p>
                </div>
                <div class="badge">{{ $module->lessons->count() }} lecciones</div>

                <div class="form-grid" style="max-width: 100%;">
                    <form method="POST" action="{{ route('teacher.courses.lessons.store', $module) }}">
                        @csrf
                        <div>
                            <label for="title-{{ $module->id }}">Nueva leccion</label>
                            <input id="title-{{ $module->id }}" name="title" type="text" placeholder="Titulo de la leccion" required>
                        </div>
                        <div>
                            <label for="description-{{ $module->id }}">Descripcion</label>
                            <textarea id="description-{{ $module->id }}" name="description" placeholder="Que se vera en esta leccion"></textarea>
                        </div>
                        <button class="btn" type="submit">Agregar leccion</button>
                    </form>
                </div>

                @if ($module->lessons->isNotEmpty())
                    <div class="list">
                        @foreach ($module->lessons as $lesson)
                            <div>
                                <strong>{{ $lesson->title }}</strong>
                                @if ($lesson->description)
                                    <div class="subtle">{{ $lesson->description }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
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
