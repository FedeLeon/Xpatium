@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1>Alumnos</h1>
            <p>Gestiona los alumnos registrados en tu academia.</p>
        </div>
        <button class="btn" type="button" onclick="document.getElementById('studentModal').showModal()">Agregar alumno</button>
    </div>

    @if ($students->isEmpty())
        <p>No hay alumnos registrados.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Nombre y apellido</th>
                    <th>Email</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $record)
                    <tr>
                        <td>{{ $record->student->name }} {{ $record->student->last_name }}</td>
                        <td>{{ $record->student->email }}</td>
                        <td>{{ $record->student->status }}</td>
                        <td>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <form method="POST" action="{{ route('teacher.students.status', $record) }}">
                                    @csrf
                                    @method('PUT')
                                    @if ($record->student->status === 'activo')
                                        <input type="hidden" name="status" value="bloqueado">
                                        <button class="btn btn-secondary" type="submit">Bloquear</button>
                                    @else
                                        <input type="hidden" name="status" value="activo">
                                        <button class="btn btn-secondary" type="submit">Activar</button>
                                    @endif
                                </form>
                                <button class="btn btn-secondary" type="button" onclick="document.getElementById('coursesModal-{{ $record->id }}').showModal()">Cursos</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @foreach ($students as $record)
        <dialog id="coursesModal-{{ $record->id }}">
            <div class="modal-header">
                <strong>Acceso a cursos</strong>
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('coursesModal-{{ $record->id }}').close()">Cerrar</button>
            </div>
            <div class="modal-body">
                <div class="list">
                    @forelse ($courses as $course)
                        @php
                            $hasAccess = $enrolledMap[$record->student_id][$course->id] ?? false;
                        @endphp
                        <div class="asset-item">
                            <span>{{ $course->name }}</span>
                            <form method="POST" action="{{ route('teacher.students.courses.access', $record) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="course_id" value="{{ $course->id }}">
                                @if ($hasAccess)
                                    <input type="hidden" name="action" value="revoke">
                                    <button class="btn btn-secondary" type="submit">Quitar</button>
                                @else
                                    <input type="hidden" name="action" value="grant">
                                    <button class="btn" type="submit">Dar acceso</button>
                                @endif
                            </form>
                        </div>
                    @empty
                        <p class="subtle">No hay cursos creados.</p>
                    @endforelse
                </div>
            </div>
        </dialog>
    @endforeach

    <dialog id="studentModal">
        <div class="modal-header">
            <strong>Agregar alumno</strong>
            <button class="btn btn-secondary" type="button" onclick="document.getElementById('studentModal').close()">Cerrar</button>
        </div>
        <form method="POST" action="{{ route('teacher.students.store') }}">
            @csrf
            <div class="modal-body form-grid">
                <div>
                    <label for="name">Nombre</label>
                    <input id="name" name="name" type="text" required>
                </div>
                <div>
                    <label for="last_name">Apellido</label>
                    <input id="last_name" name="last_name" type="text" required>
                </div>
                <div>
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" required>
                </div>
                <div>
                    <label for="password">Contrasena</label>
                    <input id="password" name="password" type="password" required>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('studentModal').close()">Cancelar</button>
                <button class="btn" type="submit">Guardar</button>
            </div>
        </form>
    </dialog>
@endsection
