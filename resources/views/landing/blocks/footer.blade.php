<footer class="footer sec sec--violet">
  <div class="wrap center footer__row">
    <img src="{{ asset('landing/assets/img/brand/xpatium-mark.png') }}" alt="X" class="footer__mark">
    <span>© {{ date('Y') }} Xpatium — Todos los derechos reservados</span>
  </div>
</footer>

<!-- Easter Egg Modal -->
<div id="easterEggModal" class="egg-modal">
  <div class="egg-content">
    <span class="egg-close">&times;</span>
    <h2>¡Descubriste el secreto de XPATIUM 🎉!</h2>

    <!-- Video del gatito bailando -->
    <video controls autoplay loop muted playsinline>
      <source src="{{ asset('landing/assets/video/gatito.mp4') }}" type="video/mp4">
      Tu navegador no soporta la etiqueta de video.
    </video>
  </div>
</div>

<script>
  const secretWord = "xpatium";
  let buffer = "";

  document.addEventListener("keydown", function(e) {
    buffer += e.key.toLowerCase();

    // Mantener el buffer del mismo largo que la palabra secreta
    if (buffer.length > secretWord.length) {
      buffer = buffer.slice(-secretWord.length);
    }

    // Si coincide, mostramos modal
    if (buffer === secretWord) {
      document.getElementById("easterEggModal").style.display = "flex";
      buffer = ""; // reseteo para volver a usarlo
    }
  });

  // Cerrar modal con botón X
  document.querySelector(".egg-close").onclick = function(){
    document.getElementById("easterEggModal").style.display = "none";
  }

  // Cerrar modal clickeando afuera
  window.onclick = function(e){
    if (e.target.id === "easterEggModal") {
      document.getElementById("easterEggModal").style.display = "none";
    }
  }
</script>
