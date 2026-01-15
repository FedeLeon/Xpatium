<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Xpatium — Enseñá y vende online</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('landing/assets/img/favicon.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('landing/assets/img/favicon.png') }}">
  <link rel="shortcut icon" href="{{ asset('landing/assets/img/favicon.png') }}">

  <!-- Tipografía divertida (opcional) -->
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('landing/assets/css/landing.css') }}" />
</head>

<body class="theme-two">
  @include('landing.blocks.nav')
  @include('landing.blocks.hero')
  @include('landing.blocks.features')
  @include('landing.blocks.team')
  @include('landing.blocks.cta')
  @include('landing.blocks.footer')
</body>

</html>
