<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $course->name }} | Xpatium</title>
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('landing/assets/img/favicon.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('landing/assets/img/favicon.png') }}">
        <link rel="shortcut icon" href="{{ asset('landing/assets/img/favicon.png') }}">
        <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/course-landing.css') }}">
    </head>
    <body>
        @php
            $totalLessons = $course->modules->sum(fn ($module) => $module->lessons->count());
            $teacherName = trim(($course->teacher?->name ?? '') . ' ' . ($course->teacher?->last_name ?? ''));
            $teacherName = $teacherName !== '' ? $teacherName : 'Equipo docente';
            $teacherAvatar = $course->teacher?->avatar_path ? asset('storage/' . $course->teacher->avatar_path) : asset('landing/assets/img/logo.png');
            $videoUrl = $course->landing_video_url;
            $videoEmbedUrl = null;
            if ($videoUrl) {
                if (preg_match('~youtu\.be/([\w-]+)~', $videoUrl, $matches)) {
                    $videoEmbedUrl = 'https://www.youtube-nocookie.com/embed/' . $matches[1];
                } elseif (preg_match('~v=([\w-]+)~', $videoUrl, $matches)) {
                    $videoEmbedUrl = 'https://www.youtube-nocookie.com/embed/' . $matches[1];
                } else {
                    $videoEmbedUrl = $videoUrl;
                }
            }
        @endphp

        <section class="curso-hero">
            <div class="container">
                <div class="curso-hero__row">
                    <div class="hero-copy">
                        <a href="{{ route('home') }}">
                            <img class="logo-curso" src="{{ asset('landing/assets/img/logo.png') }}" alt="Xpatium">
                        </a>
                        <span class="tipo-curso">Inicio: {{ $course->start_date?->format('d/m/Y') ?? 'A confirmar' }}</span>
                        <h1 class="titulo-curso">{{ $course->name }}</h1>
                        @if ($course->subtitle)
                            <p class="descripcion-curso">{{ $course->subtitle }}</p>
                        @endif
                        <a class="levantar-btn" href="{{ $course->payment_link ?: '#cta-compra' }}" {{ $course->payment_link ? 'target=_blank rel=noopener' : '' }}>
                            Accede ahora →
                        </a>
                    </div>
                </div>
            </div>
            <div class="onda-inferior">
                <svg class="d-block w-100 h-100" preserveAspectRatio="none" viewBox="0 0 120 6">
                    <path class="onda-fill" d="M0 0 V0 Q 60 6 120 0 V6 H0 Z"></path>
                </svg>
            </div>
        </section>

        <section class="curso-detalle">
            <div class="container">
                <div class="detalle-grid">
                    <div>
                        <div class="preview-wrapper">
                            <div class="video-wrapper">
                                @if ($videoEmbedUrl)
                                    <iframe
                                        src="{{ $videoEmbedUrl }}"
                                        title="{{ $course->name }}"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen
                                        loading="lazy">
                                    </iframe>
                                @else
                                    <div class="video-placeholder">
                                        <span>Video disponible pronto</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="horas-badge">
                            <div>
                                <strong>{{ $totalLessons }}</strong> lecciones
                                <br>
                                <small>en {{ $course->modules->count() }} modulos</small>
                            </div>
                        </div>
                        <div class="instructor-card">
                            <div class="card-body d-flex align-items-center">
                                <img class="avatar-instructor" src="{{ $teacherAvatar }}" alt="{{ $teacherName }}">
                                <div>
                                    <h5 class="mb-1">{{ $teacherName }}</h5>
                                    <p class="mb-0 small">Instructor del curso</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-beneficios">
                        <span class="badge">CUANDO QUIERAS Y DONDE QUIERAS</span>
                        <h2 class="h4 fw-normal mb-3">Tendras acceso completo durante todo el proceso de entrenamiento*</h2>
                        <h3 class="h5 fw-semibold mb-3">Temas</h3>
                        <ul class="listado-beneficios">
                            @forelse ($course->modules as $module)
                                <li>{{ $module->title }}</li>
                            @empty
                                <li>Contenido cargandose.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="curso-contenido" id="contenido-curso">
            <div class="container">
                <h2 class="text-center mb-5">Contenido del entrenamiento</h2>
                @forelse ($course->modules as $module)
                    <div class="mod-card">
                        <div class="card-body d-flex gap-3 align-items-start">
                            <span class="circle-status flex-shrink-0 mt-1"></span>
                            <div class="flex-grow-1">
                                <strong>{{ $module->title }}</strong>
                                @if ($module->subtitle)
                                    <small class="d-block text-muted">{{ $module->subtitle }}</small>
                                @endif
                                @if ($module->description)
                                    <p class="mb-0 mt-2">{{ $module->description }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center">Este curso aun no tiene modulos publicados.</p>
                @endforelse
            </div>
        </section>

        <section class="curso-mensaje">
            <div class="container">
                <h2 class="text-center mb-4">Mensaje del profesor</h2>
                <div class="mensaje-card">
                    <div>
                        @if ($course->landing_custom_text)
                            <p>{{ $course->landing_custom_text }}</p>
                        @else
                            <p>El profesor sumara aqui su mensaje personalizado.</p>
                        @endif
                    </div>
                    @if ($course->landing_custom_image_path)
                        <img src="{{ asset('storage/' . $course->landing_custom_image_path) }}" alt="Imagen del profesor">
                    @endif
                </div>
            </div>
        </section>

        <section class="faq-curso" id="preguntas-frecuentes">
            <div class="container">
                <div class="row g-5">
                    <div class="col-lg-5">
                        <h2 class="titulo-faq mb-3">Preguntas frecuentes</h2>
                        <p class="texto-faq mb-4">Resolvemos las dudas mas comunes sobre el curso y el acceso.</p>
                    </div>
                    <div class="col-lg-7">
                        <div class="faq-box">
                            <h4>¿Como accedo a las clases una vez que compro el curso?</h4>
                            <p>Recibiras un correo con el acceso al campus virtual para empezar de inmediato.</p>
                        </div>
                        <div class="faq-box">
                            <h4>¿Puedo ver las lecciones desde mi celular?</h4>
                            <p>Si, la plataforma es responsive y funciona en celular, tablet y computadora.</p>
                        </div>
                        <div class="faq-box">
                            <h4>¿Tendre soporte si tengo preguntas?</h4>
                            <p>Podras comunicarte con el profesor desde la plataforma para resolver dudas.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="curso-cta-final" id="cta-compra">
            <div class="container">
                <div class="cta-grid">
                    <div class="cta-main">
                        <h2 class="fw-bold texto-cta mb-4">¿Preparado para avanzar en tu camino de aprendizaje?</h2>
                        <h5 class="fw-semibold mb-3">¿Que incluye?</h5>
                        <span class="badge bg-light text-dark mb-4 d-inline-block">* Acceso al contenido en la plataforma.</span>
                        <ul class="lista-incluye">
                            @forelse ($course->modules as $module)
                                <li>{{ $module->title }}</li>
                            @empty
                                <li>Contenido disponible pronto.</li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="cta-price">
                        <h2 class="h4 fw-normal">Duracion: acceso flexible</h2>
                        <span class="badge bg-light text-dark">INVERSION</span>
                        <h3 class="display-6 fw-bold">
                            <span class="precio-pill">ARS {{ number_format($course->price_ars, 2) }}</span>
                        </h3>
                        <p>USD {{ number_format($course->price_usd, 2) }}</p>
                        <div class="payment-logos payment-logos--inline payment-logos--left">
                            <div class="payment-card">
                                <img src="{{ asset('landing/assets/img/payments/visa.svg') }}" alt="Visa">
                            </div>
                            <div class="payment-card">
                                <img src="{{ asset('landing/assets/img/payments/mastercard.svg') }}" alt="Mastercard">
                            </div>
                            <div class="payment-card">
                                <img src="{{ asset('landing/assets/img/payments/mercadopago.svg') }}" alt="Mercado Pago">
                            </div>
                        </div>
                        @if ($course->payment_link)
                            <a class="levantar-btn" href="{{ $course->payment_link }}" target="_blank" rel="noopener">Comprar ahora →</a>
                        @else
                            <span class="levantar-btn">Pago disponible pronto</span>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <footer class="footer">
            <div class="container">
                <span>© {{ date('Y') }} Xpatium — Todos los derechos reservados</span>
            </div>
        </footer>
    </body>
</html>
