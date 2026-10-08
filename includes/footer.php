<footer class="site-footer">
  <div class="footer-inner">
    <div class="footer-brand"><a href="<?=$base??''?>index.php"><img src="<?=$base??''?>assets/logo.webp" alt="GameZone Store"><span>GameZone Store</span></a><p>Tu espacio para descubrir y gestionar tus juegos favoritos.</p></div>
    <div class="footer-col"><h3>Tienda</h3><a href="<?=$base??''?>index.php">Inicio</a><a href="<?=$base??''?>productos.php">Productos</a><a href="<?=$base??''?>carrito.php">Carrito</a></div>
    <div class="footer-col"><h3>Cuenta</h3><?php if(logueado()): ?><a href="<?=$base??''?>perfil.php">Mi perfil</a><?php else: ?><a href="<?=$base??''?>login.php">Iniciar sesión</a><a href="<?=$base??''?>registro.php">Crear cuenta</a><?php endif; ?></div>
    <div class="footer-col"><h3>Ayuda</h3><a href="<?=$base??''?>contacto.php">Contacto</a><span>Verificación por correo</span><span>Soporte de pedidos</span></div>
  </div>
  <div class="footer-bottom"><span>© <?=date('Y')?> GameZone Store</span><span>PHP · MySQL · HTML · CSS</span></div>
</footer></body></html>
