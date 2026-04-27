@extends('layouts.app')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/course-form.css') }}">
    @endpush
    <div class="actions">
        <div>
            <h1>Crear contenido de ayuda</h1>
            <p>Completa los datos para publicar un contenido de ayuda.</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('teacher.courses.index') }}">Volver</a>
    </div>

    @if ($errors->any())
        <div class="alert">
            <p>Revisa los datos del formulario.</p>
        </div>
    @endif

    <form class="course-form" method="POST" action="{{ route('teacher.courses.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="course-form__grid">
            <div class="card course-form__main">
                <h2>Datos del contenido de ayuda</h2>
                <div class="form-grid form-grid-full">
                    <div>
                        <label for="name">
                            Nombre del contenido de ayuda
                            <span class="info-tooltip" data-tooltip="Titulo principal que veran los usuarios.">i</span>
                        </label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="hint">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="subtitle">
                            Subtitulo
                            <span class="info-tooltip" data-tooltip="Frase corta para resumir el contenido de ayuda.">i</span>
                        </label>
                        <input id="subtitle" name="subtitle" type="text" value="{{ old('subtitle') }}" required>
                        @error('subtitle')
                            <div class="hint">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="description">
                            Descripcion
                            <span class="info-tooltip" data-tooltip="Detalle del contenido y objetivos del contenido de ayuda.">i</span>
                        </label>
                        <textarea id="description" name="description" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="hint">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="price_ars">
                            Precio ARS
                            <span class="info-tooltip" data-tooltip="Precio en pesos argentinos.">i</span>
                        </label>
                        <input id="price_ars" name="price_ars" type="number" step="0.01" min="0" value="{{ old('price_ars', '0.00') }}" required>
                        @error('price_ars')
                            <div class="hint">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="price_usd">
                            Precio USD
                            <span class="info-tooltip" data-tooltip="Precio en dolares para usuarios internacionales.">i</span>
                        </label>
                        <input id="price_usd" name="price_usd" type="number" step="0.01" min="0" value="{{ old('price_usd') }}">
                        @error('price_usd')
                            <div class="hint">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="start_date">
                            Fecha de inicio
                            <span class="info-tooltip" data-tooltip="Fecha estimada de inicio del contenido de ayuda.">i</span>
                        </label>
                        <input id="start_date" name="start_date" type="date" value="{{ old('start_date') }}">
                        @error('start_date')
                            <div class="hint">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="duration">
                            Duracion del contenido de ayuda
                            <span class="info-tooltip" data-tooltip="Ejemplo: 8 semanas, 20 horas, acceso permanente.">i</span>
                        </label>
                        <input id="duration" name="duration" type="text" value="{{ old('duration') }}">
                        @error('duration')
                            <div class="hint">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="level">
                            Nivel
                            <span class="info-tooltip" data-tooltip="Selecciona el nivel sugerido del contenido de ayuda.">i</span>
                        </label>
                        <select id="level" name="level">
                            <option value="">Selecciona un nivel</option>
                            <option value="principiante" @selected(old('level') === 'principiante')>Principiante</option>
                            <option value="intermedio" @selected(old('level') === 'intermedio')>Intermedio</option>
                            <option value="avanzado" @selected(old('level') === 'avanzado')>Avanzado</option>
                        </select>
                        @error('level')
                            <div class="hint">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="image">
                            Imagen del contenido de ayuda
                            <span class="info-tooltip" data-tooltip="Imagen principal que se muestra en el marketplace.">i</span>
                        </label>
                        <input id="image" name="image" type="file" accept="image/*">
                        @error('image')
                            <div class="hint">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="image-preview" id="course-image-preview">
                    <span class="image-preview__label">Vista previa</span>
                    <img class="image-preview__img" id="course-image-preview-img" alt="Vista previa de imagen">
                </div>
            </div>

            <div class="card course-form__side">
                <h2>Landing del contenido de ayuda</h2>
                <p class="subtle">Estos campos son opcionales y pueden completarse despues de crear el contenido de ayuda.</p>
                <div class="form-grid form-grid-full">
                    <div>
                        <label for="landing_custom_text">
                            Texto destacado para la landing
                            <span class="info-tooltip" data-tooltip="Mensaje del lider que aparece en la landing.">i</span>
                        </label>
                        <textarea id="landing_custom_text" name="landing_custom_text" placeholder="Comparte un mensaje del lider.">{{ old('landing_custom_text') }}</textarea>
                        @error('landing_custom_text')
                            <div class="hint">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="payment_link">
                            Link de pago
                            <span class="info-tooltip" data-tooltip="URL del checkout o enlace de Mercado Pago.">i</span>
                        </label>
                        <input id="payment_link" name="payment_link" type="url" placeholder="https://mpago.la/..." value="{{ old('payment_link') }}">
                        @error('payment_link')
                            <div class="hint">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="landing_video_url">
                            Video para la landing (YouTube)
                            <span class="info-tooltip" data-tooltip="Link de YouTube para el video de presentacion.">i</span>
                        </label>
                        <input id="landing_video_url" name="landing_video_url" type="url" placeholder="https://www.youtube.com/watch?v=..." value="{{ old('landing_video_url') }}">
                        @error('landing_video_url')
                            <div class="hint">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="landing_custom_image">
                            Imagen para la landing
                            <span class="info-tooltip" data-tooltip="Imagen opcional para el bloque del lider.">i</span>
                        </label>
                        <input id="landing_custom_image" name="landing_custom_image" type="file" accept="image/*">
                        @error('landing_custom_image')
                            <div class="hint">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="image-preview" id="landing-image-preview">
                    <span class="image-preview__label">Vista previa landing</span>
                    <img class="image-preview__img" id="landing-image-preview-img" alt="Vista previa landing">
                </div>
            </div>
        </div>

        <div class="actions course-form__actions">
            <div></div>
            <button class="btn btn-primary" type="submit">Guardar contenido de ayuda</button>
        </div>
    </form>

    <script>
        (function () {
            var input = document.getElementById('image');
            var preview = document.getElementById('course-image-preview');
            var previewImg = document.getElementById('course-image-preview-img');
            var landingInput = document.getElementById('landing_custom_image');
            var landingPreview = document.getElementById('landing-image-preview');
            var landingPreviewImg = document.getElementById('landing-image-preview-img');
            if (!input || !preview || !previewImg) {
                return;
            }
            input.addEventListener('change', function (event) {
                var file = event.target.files && event.target.files[0];
                if (!file) {
                    preview.classList.remove('image-preview--visible');
                    previewImg.removeAttribute('src');
                    return;
                }
                var url = URL.createObjectURL(file);
                previewImg.onload = function () {
                    URL.revokeObjectURL(url);
                };
                previewImg.src = url;
                preview.classList.add('image-preview--visible');
            });
            if (!landingInput || !landingPreview || !landingPreviewImg) {
                return;
            }
            landingInput.addEventListener('change', function (event) {
                var file = event.target.files && event.target.files[0];
                if (!file) {
                    landingPreview.classList.remove('image-preview--visible');
                    landingPreviewImg.removeAttribute('src');
                    return;
                }
                var url = URL.createObjectURL(file);
                landingPreviewImg.onload = function () {
                    URL.revokeObjectURL(url);
                };
                landingPreviewImg.src = url;
                landingPreview.classList.add('image-preview--visible');
            });
        })();
    </script>
@endsection
