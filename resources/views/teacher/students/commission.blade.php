@extends('layouts.app')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/teacher-students.css') }}">
    @endpush

    <div class="actions">
        <div>
            <h1 class="view-title">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M17 21v-1a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v1"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-1a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Comision
            </h1>
            <p>Administra las comisiones de alumnos.</p>
            <button class="btn btn-action" type="button" onclick="document.getElementById('commissionModal').showModal()">
                Crear comision
            </button>
        </div>
    </div>

    @if ($commissions->isEmpty())
        <p class="subtle">No hay comisiones creadas.</p>
    @else
        <table class="commission-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripcion</th>
                    <th>Alumnos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($commissions as $commission)
                    <tr>
                        <td>{{ $commission->name }}</td>
                        <td class="commission-description">
                            {{ $commission->description ?: '—' }}
                        </td>
                        <td>
                            <button class="commission-count" type="button" onclick="document.getElementById('commissionStudentsModal-{{ $commission->id }}').showModal()">
                                {{ $commission->students_count }}
                            </button>
                        </td>
                        <td>
                            <div class="commission-actions">
                                <button class="btn btn-secondary btn-icon-only" type="button" aria-label="Editar alumnos" onclick="document.getElementById('commissionEditModal-{{ $commission->id }}').showModal()">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M17 21v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-1a4 4 0 00-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 010 7.75"></path>
                                    </svg>
                                </button>
                                <form method="POST" action="{{ route('teacher.students.commission.destroy', $commission) }}" data-confirm="Se eliminara la comision.">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-icon-only" type="submit" aria-label="Eliminar comision">
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M3 6h18"></path>
                                            <path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2"></path>
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                            <path d="M10 11v6"></path>
                                            <path d="M14 11v6"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <dialog id="commissionModal">
        <div class="modal-header">
            <strong>Crear comision</strong>
            <button class="btn btn-secondary" type="button" onclick="document.getElementById('commissionModal').close()">Cerrar</button>
        </div>
        <form method="POST" action="{{ route('teacher.students.commission.store') }}">
            @csrf
            <div class="modal-body form-grid">
                <div>
                    <label for="commission-name">Nombre</label>
                    <input id="commission-name" name="name" type="text" value="{{ old('name') }}" class="{{ $errors->has('name') ? 'input-error' : '' }}" required>
                    @error('name')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="commission-description">Descripcion</label>
                    <textarea id="commission-description" name="description" rows="4" class="{{ $errors->has('description') ? 'input-error' : '' }}">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>
                <div class="commission-students">
                    <label>Alumnos</label>
                    @if ($students->isEmpty())
                        <p class="subtle">No hay alumnos disponibles para asignar.</p>
                    @else
                        <div class="commission-students__list {{ $errors->has('students') || $errors->has('students.*') ? 'input-error' : '' }}">
                            @foreach ($students as $record)
                                @php
                                    $studentName = trim($record->student->name . ' ' . $record->student->last_name);
                                @endphp
                                <label class="commission-students__item">
                                    <input type="checkbox" name="students[]" value="{{ $record->student_id }}" {{ in_array($record->student_id, old('students', [])) ? 'checked' : '' }}>
                                    <span>{{ $studentName }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('students')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                        @error('students.*')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('commissionModal').close()">Cancelar</button>
                <button class="btn" type="submit">Guardar</button>
            </div>
        </form>
    </dialog>

    @foreach ($commissions as $commission)
        <dialog id="commissionStudentsModal-{{ $commission->id }}">
            <div class="modal-header">
                <strong>Alumnos en {{ $commission->name }}</strong>
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('commissionStudentsModal-{{ $commission->id }}').close()">Cerrar</button>
            </div>
            <div class="modal-body">
                @if ($commission->students->isEmpty())
                    <p class="subtle">No hay alumnos en esta comision.</p>
                @else
                    <div class="commission-students__list">
                        @foreach ($commission->students as $student)
                            <div class="commission-students__item">
                                {{ trim($student->name . ' ' . $student->last_name) }}
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </dialog>

        <dialog id="commissionEditModal-{{ $commission->id }}">
            <div class="modal-header">
                <strong>Editar alumnos</strong>
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('commissionEditModal-{{ $commission->id }}').close()">Cerrar</button>
            </div>
            <form method="POST" action="{{ route('teacher.students.commission.students.update', $commission) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="commission-students">
                        <label>Alumnos</label>
                        @if ($students->isEmpty())
                            <p class="subtle">No hay alumnos disponibles para asignar.</p>
                        @else
                            <div class="commission-students__list">
                                @foreach ($students as $record)
                                    @php
                                        $studentName = trim($record->student->name . ' ' . $record->student->last_name);
                                        $isAssigned = $commission->students->contains('id', $record->student_id);
                                    @endphp
                                    <label class="commission-students__item">
                                        <input type="checkbox" name="students[]" value="{{ $record->student_id }}" {{ $isAssigned ? 'checked' : '' }}>
                                        <span>{{ $studentName }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" onclick="document.getElementById('commissionEditModal-{{ $commission->id }}').close()">Cancelar</button>
                    <button class="btn" type="submit">Guardar</button>
                </div>
            </form>
        </dialog>
    @endforeach

    @if (old('name') !== null || old('description') !== null)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var modal = document.getElementById('commissionModal');
                if (modal) {
                    modal.showModal();
                }
            });
        </script>
    @endif
@endsection
