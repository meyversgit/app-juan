<?php $titulo='Contacto - GameZone Store';$base='';$enviado=false;if($_SERVER['REQUEST_METHOD']==='POST')$enviado=true;require 'includes/header.php'; ?>
<main class="main contacto-page">
  <div class="catalogo-heading"><span>GAMEZONE STORE</span><h1>Contáctanos</h1><p>¿Tienes una pregunta? Estamos para ayudarte.</p></div>
  <?php if($enviado): ?><div class="alert-php ok-local">Mensaje enviado correctamente.</div><?php endif; ?>
  <section class="contacto-layout">
    <div class="div-contacto contacto-form">
      <h3>Envíanos un mensaje</h3><p class="texto-suave">Cuéntanos qué necesitas y te responderemos lo antes posible.</p>
      <form method="post">
        <label>Nombre</label><input type="text" placeholder="Tu nombre" class="input-contacto" name="nombre" required>
        <label>Email</label><input type="email" placeholder="tu@email.com" class="input-contacto" name="email" required>
        <label>Mensaje</label><textarea placeholder="Escribe tu mensaje..." class="input-contacto" name="mensaje" required></textarea>
        <button type="submit">Enviar mensaje</button>
      </form>
    </div>
    <div class="div-contacto contacto-info-card">
      <span class="carrito-kicker">SOPORTE</span><h3>Estamos aquí</h3><p class="texto-suave">También puedes encontrarnos por estos medios.</p>
      <div class="contacto-info-list">
        <div class="contacto-info-item"><strong>Email</strong>info@gamezonestore.com</div>
        <div class="contacto-info-item"><strong>Teléfono</strong>+1 234 567 890</div>
        <div class="contacto-info-item"><strong>Horario</strong>Lunes a viernes · 10:00 - 20:00</div>
      </div>
    </div>
  </section>
</main>
<?php require 'includes/footer.php'; ?>
