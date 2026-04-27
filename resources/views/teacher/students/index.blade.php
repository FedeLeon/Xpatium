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
                Usuarios
            </h1>
            <p>Gestiona los usuarios registrados en tu comunidad.</p>
            <button class="btn btn-action" type="button" onclick="document.getElementById('studentModal').showModal()">
                Agregar usuario
            </button>
        </div>
    </div>

    @if ($students->isEmpty())
        <p>No hay usuarios registrados.</p>
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
                        <td>
                            @php
                                $studentAvatarUrl = $record->student->avatar_path ? asset('storage/' . $record->student->avatar_path) : null;
                                $studentInitials = strtoupper(substr($record->student->name ?? 'A', 0, 1));
                            @endphp
                            <div class="student-cell">
                                <span class="student-avatar-wrap">
                                    <span class="student-avatar">
                                        @if ($studentAvatarUrl)
                                            <img src="{{ $studentAvatarUrl }}" alt="Foto de {{ $record->student->name }}">
                                        @else
                                            {{ $studentInitials }}
                                        @endif
                                    </span>
                                    <span class="online-dot" data-online-user-id="{{ $record->student->id }}"></span>
                                </span>
                                <span>{{ $record->student->name }} {{ $record->student->last_name }}</span>
                            </div>
                        </td>
                        <td>{{ $record->student->email }}</td>
                        <td>
                            @php
                                $statusValue = strtolower((string) $record->student->status);
                                $isActive = $statusValue === 'activo';
                            @endphp
                            <span class="status-pill {{ $isActive ? 'status-pill--active' : 'status-pill--inactive' }}">
                                {{ $isActive ? 'ACTIVO' : 'INACTIVO' }}
                            </span>
                        </td>
                        <td>
                            <div class="student-actions">
                                <button class="btn btn-secondary btn-icon" type="button" onclick="document.getElementById('coursesModal-{{ $record->id }}').showModal()">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                    </svg>
                                    Contenidos de ayuda
                                </button>
                                <button class="btn btn-secondary btn-icon" type="button" onclick="document.getElementById('pastoralModal-{{ $record->id }}').showModal()">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M12 21s-6-4.35-6-9a4 4 0 0 1 7-2.65A4 4 0 0 1 20 12c0 4.65-6 9-6 9z"></path>
                                    </svg>
                                    Ficha
                                </button>
                                <div class="student-actions__final">
                                    <form method="POST" action="{{ route('teacher.students.status', $record) }}">
                                        @csrf
                                        @method('PUT')
                                        @if ($record->student->status === 'activo')
                                            <input type="hidden" name="status" value="bloqueado">
                                            <button class="btn btn-secondary btn-icon" type="submit">
                                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                                    <rect x="3" y="11" width="18" height="11" rx="2"></rect>
                                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                                </svg>
                                                Bloquear
                                            </button>
                                        @else
                                            <input type="hidden" name="status" value="activo">
                                            <button class="btn btn-secondary btn-icon" type="submit">
                                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                                    <circle cx="12" cy="12" r="9"></circle>
                                                    <path d="M9 12l2 2 4-4"></path>
                                                </svg>
                                                Activar
                                            </button>
                                        @endif
                                    </form>
                                    <form method="POST" action="{{ route('teacher.students.destroy', $record) }}" data-confirm="Se eliminara el usuario y su acceso.">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-icon" type="submit">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M3 6h18"></path>
                                                <path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2"></path>
                                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                                <path d="M10 11v6"></path>
                                                <path d="M14 11v6"></path>
                                            </svg>
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
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
                <strong>Acceso a contenidos de ayuda</strong>
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('coursesModal-{{ $record->id }}').close()">Cerrar</button>
            </div>
            <div class="modal-body">
                <div class="course-access-list">
                    @forelse ($courses as $course)
                        @php
                            $hasAccess = $enrolledMap[$record->student_id][$course->id] ?? false;
                        @endphp
                        <div class="course-access-item">
                            <div class="course-access-item__media">
                                @if ($course->image_path)
                                    <img src="{{ asset('storage/' . $course->image_path) }}" alt="Miniatura de {{ $course->name }}">
                                @else
                                    <span class="course-access-item__fallback">{{ strtoupper(substr($course->name, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="course-access-item__name">{{ $course->name }}</div>
                            <form class="course-access-item__form" method="POST" action="{{ route('teacher.students.courses.access', $record) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="course_id" value="{{ $course->id }}">
                                <input class="course-access-action" type="hidden" name="action" value="{{ $hasAccess ? 'revoke' : 'grant' }}">
                                <label class="switch">
                                    <input
                                        class="switch-input"
                                        type="checkbox"
                                        {{ $hasAccess ? 'checked' : '' }}
                                        aria-label="Acceso al contenido de ayuda {{ $course->name }}"
                                        data-course="{{ $course->name }}"
                                        data-student="{{ $record->student->name }} {{ $record->student->last_name }}"
                                    >
                                    <span class="switch-slider" aria-hidden="true"></span>
                                </label>
                            </form>
                        </div>
                    @empty
                        <p class="subtle">No hay contenidos de ayuda creados.</p>
                    @endforelse
                </div>
            </div>
        </dialog>

        <dialog id="pastoralModal-{{ $record->id }}">
            <div class="modal-header">
                <strong>Ficha de acompanamiento</strong>
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('pastoralModal-{{ $record->id }}').close()">Cerrar</button>
            </div>
            <form method="POST" action="{{ route('teacher.students.pastoral.update', $record) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="pastoral_profile_id" value="{{ $record->id }}">
                <div class="modal-body form-grid">
                    <div>
                        <label for="sentimental-status-{{ $record->id }}">Situacion sentimental</label>
                        <select id="sentimental-status-{{ $record->id }}" name="sentimental_status">
                            <option value="" {{ old('pastoral_profile_id') == $record->id && old('sentimental_status', $record->sentimental_status) === '' ? 'selected' : '' }}>Sin definir</option>
                            @foreach (['soltero/a', 'en pareja', 'casado/a', 'separado/a', 'divorciado/a', 'viudo/a'] as $status)
                                <option value="{{ $status }}" {{ old('pastoral_profile_id') == $record->id ? (old('sentimental_status') === $status ? 'selected' : '') : ($record->sentimental_status === $status ? 'selected' : '') }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="has-children-{{ $record->id }}">Tiene hijos</label>
                        @php
                            $hasChildrenValue = old('pastoral_profile_id') == $record->id
                                ? old('has_children', $record->has_children ? '1' : '0')
                                : ($record->has_children ? '1' : '0');
                        @endphp
                        <select id="has-children-{{ $record->id }}" name="has_children">
                            <option value="0" {{ (string) $hasChildrenValue === '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ (string) $hasChildrenValue === '1' ? 'selected' : '' }}>Si</option>
                        </select>
                    </div>
                    <div>
                        <label for="children-count-{{ $record->id }}">Cantidad de hijos</label>
                        <input
                            id="children-count-{{ $record->id }}"
                            name="children_count"
                            type="number"
                            min="0"
                            max="20"
                            value="{{ old('pastoral_profile_id') == $record->id ? old('children_count', $record->children_count) : $record->children_count }}"
                        >
                    </div>
                    <div class="form-grid__full">
                        <label for="family-situation-{{ $record->id }}">Situacion familiar</label>
                        <textarea id="family-situation-{{ $record->id }}" name="family_situation" rows="3">{{ old('pastoral_profile_id') == $record->id ? old('family_situation', $record->family_situation) : $record->family_situation }}</textarea>
                    </div>
                    <div class="form-grid__full">
                        <label for="current-challenges-{{ $record->id }}">Problemas o desafios actuales</label>
                        <textarea id="current-challenges-{{ $record->id }}" name="current_challenges" rows="3">{{ old('pastoral_profile_id') == $record->id ? old('current_challenges', $record->current_challenges) : $record->current_challenges }}</textarea>
                    </div>
                    <div class="form-grid__full">
                        <label for="leader-notes-{{ $record->id }}">Anotaciones del lider</label>
                        <textarea id="leader-notes-{{ $record->id }}" name="leader_notes" rows="4">{{ old('pastoral_profile_id') == $record->id ? old('leader_notes', $record->leader_notes) : $record->leader_notes }}</textarea>
                    </div>
                    @if ($record->pastoral_updated_at)
                        <div class="form-grid__full">
                            <p class="subtle">Ultima actualizacion: {{ $record->pastoral_updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" onclick="document.getElementById('pastoralModal-{{ $record->id }}').close()">Cancelar</button>
                    <button class="btn" type="submit">Guardar ficha</button>
                </div>
            </form>
        </dialog>
    @endforeach

    <dialog id="studentModal">
        <div class="modal-header">
            <strong>Agregar usuario</strong>
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
    <script>
        document.addEventListener('change', function (event) {
            var input = event.target;
            if (!input.classList.contains('switch-input')) {
                return;
            }
            var form = input.closest('form');
            if (!form) {
                return;
            }
            var actionField = form.querySelector('.course-access-action');
            if (actionField) {
                actionField.value = input.checked ? 'grant' : 'revoke';
            }
            var formData = new FormData(form);
            var checkedState = input.checked;
            var url = form.getAttribute('action');
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            }).then(function (response) {
                if (!response.ok) {
                    throw new Error('request_failed');
                }
                var courseName = input.getAttribute('data-course') || 'Contenido de ayuda';
                var studentName = input.getAttribute('data-student') || 'Usuario';
                var title = checkedState ? 'Acceso habilitado' : 'Acceso revocado';
                var type = checkedState ? 'success' : 'error';
                showToast({
                    title: title,
                    subtitle: courseName + ' para ' + studentName,
                    type: type
                });
            }).catch(function () {
                input.checked = !checkedState;
                if (actionField) {
                    actionField.value = input.checked ? 'grant' : 'revoke';
                }
                var courseName = input.getAttribute('data-course') || 'Contenido de ayuda';
                var studentName = input.getAttribute('data-student') || 'Usuario';
                showToast({
                    title: 'Error al actualizar',
                    subtitle: courseName + ' para ' + studentName,
                    type: 'error'
                });
            });
        });

        (function () {
            var openProfileId = @json(old('pastoral_profile_id'));
            if (!openProfileId) {
                return;
            }
            var modal = document.getElementById('pastoralModal-' + openProfileId);
            if (modal && typeof modal.showModal === 'function') {
                modal.showModal();
            }
        })();

        function showToast(options) {
            var toast = document.createElement('div');
            var type = options && options.type ? options.type : 'success';
            toast.className = 'toast toast--' + type;
            toast.setAttribute('role', 'status');
            toast.setAttribute('aria-live', 'polite');
            var icon = document.createElement('span');
            icon.className = 'toast__icon';
            icon.textContent = type === 'error' ? '⛔' : '✅';
            var text = document.createElement('div');
            text.className = 'toast__text';
            var title = document.createElement('div');
            title.className = 'toast__title';
            title.textContent = options && options.title ? options.title : '';
            var subtitle = document.createElement('div');
            subtitle.className = 'toast__subtitle';
            subtitle.textContent = options && options.subtitle ? options.subtitle : '';
            text.appendChild(title);
            text.appendChild(subtitle);
            toast.appendChild(icon);
            toast.appendChild(text);
            document.body.appendChild(toast);
            requestAnimationFrame(function () {
                toast.classList.add('toast--visible');
            });
            setTimeout(function () {
                toast.classList.remove('toast--visible');
                setTimeout(function () {
                    toast.remove();
                }, 200);
            }, 2600);
        }
    </script>
@endsection
