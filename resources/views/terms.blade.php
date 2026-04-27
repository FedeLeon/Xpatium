<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Terminos y condiciones | CristoApp</title>
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('landing/assets/img/favicon.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('landing/assets/img/favicon.png') }}">
        <link rel="shortcut icon" href="{{ asset('landing/assets/img/favicon.png') }}">
        <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('landing/assets/css/landing.css') }}" />
        <link rel="stylesheet" href="{{ asset('css/course-landing.css') }}" />
    </head>
    <body class="theme-two">
        @include('landing.blocks.nav')

        <section class="sec sec--violet">
            <div class="wrap">
                <h1 class="course-section-title">Terminos y condiciones</h1>
                <div class="course-card">
                    <p>Este es un resumen inicial. Completa esta pagina con tus politicas legales antes de activar pagos en produccion.</p>
                    <ul class="terms-list">
                        <li>Los contenidos de ayuda se venden como acceso digital y no se comparten fuera de la plataforma.</li>
                        <li>Las politicas de reembolso se definen por el lider y se informan antes de pagar.</li>
                        <li>El usuario se compromete a un uso responsable del contenido y a respetar derechos de autor.</li>
                        <li>Los medios de pago aceptados pueden variar segun el pais.</li>
                    </ul>
                </div>
            </div>
        </section>

        @include('landing.blocks.footer')
    </body>
</html>
