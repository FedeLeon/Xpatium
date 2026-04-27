<header class="nav nav--violet">
  <div class="wrap nav__row">
    <a class="brand" href="{{ url('/') }}">
      <img src="{{ asset('landing/assets/img/logo.png') }}" alt="CristoApp">
      <strong>CristoApp</strong>
    </a>

    <div class="nav__actions">
      <a class="btn btn--ghost" href="{{ route('login') }}">Ingresar</a>
      <a class="btn btn--solid-pink" href="{{ route('register') }}">Probar gratis</a>
    </div>
  </div>
</header>
