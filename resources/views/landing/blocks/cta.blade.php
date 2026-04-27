<section id="cta" class="sec sec--pink">
  <!-- Drip hacia sección rosa -->
  <div class="drip drip--pink" aria-hidden="true">
    <svg viewBox="0 0 1440 140" preserveAspectRatio="none">
      <path d="M0,0 C120,110 220,30 320,80 C420,130 560,20 640,80 C720,140 860,60 980,100 C1100,140 1300,40 1440,70 L1440,140 L0,140 Z"></path>
    </svg>
  </div>

  <div class="wrap center">
    <!-- Bloque de contacto para lideres -->
    <div class="cta-box">
      <img src="{{ asset('landing/assets/img/logo.png') }}" alt="Logo CristoApp" class="cta-logo">
      <h3>¿Sos lider o lideresa? Empeza hoy</h3>
      <p>Dejanos tu contacto y te ayudamos a implementar CristoApp en tu iglesia.</p>

      <form class="cta-form">
        <input type="text" name="name" placeholder="Tu nombre" required>
        <input type="email" name="email" placeholder="Tu email" required>
        <textarea name="message" placeholder="Tu consulta" rows="4"></textarea>
        <button type="submit" class="btn btn--solid-pink">Enviar</button>
      </form>
    </div>

    <!-- CTA final -->
    <h2 class="h2">CristoApp — organizacion y formacion para tu iglesia</h2>
    <p class="muted">Empeza hoy a liderar y acompanar a tu comunidad.</p>
    <div class="row-gap center">
      <a class="btn btn--solid-violet" href="{{ route('register') }}">Probar gratis</a>
      <a class="btn btn--ghost" href="{{ route('login') }}">Ingresar</a>
    </div>

  </div>
</section>
