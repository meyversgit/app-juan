<?php require_once 'config/db.php'; $slug=$_GET['slug']??''; $st=$pdo->prepare('SELECT * FROM juegos WHERE slug=? LIMIT 1'); $st->execute([$slug]); $j=$st->fetch(); if(!$j){header('Location: productos.php');exit;} $titulo=$j['nombre'].' - GameZone Store'; $base=''; require 'includes/header.php'; $desc=preg_replace('/\s+/',' ',trim($j['descripcion'])); $limite=260; $corta=mb_strlen($desc,'UTF-8')>$limite; $descCorta=$corta?mb_substr($desc,0,$limite,'UTF-8'):$desc; $descCorta=rtrim($descCorta," ,.;:-"); ?>
<link rel="stylesheet" href="<?=$base?>css/style-eldenring.css">
<main class="producto-detalle">
  <a class="detalle-volver" href="productos.php">← Volver a productos</a>
  <section class="detalle-grid">
    <div class="detalle-imagen"><img src="<?=htmlspecialchars($j['imagen'])?>" alt="<?=htmlspecialchars($j['nombre'])?>"></div>
    <div class="detalle-compra">
      <span class="detalle-categoria"><?=htmlspecialchars($j['categoria'])?></span>
      <h1><?=htmlspecialchars($j['nombre'])?></h1>
      <p class="detalle-descripcion-corta"><?=htmlspecialchars($descCorta)?></p><?php if($corta): ?><a class="detalle-ver-mas" href="#detalle-completo">Ver descripción completa</a><?php endif; ?>
      <div class="detalle-precio">US$<?=number_format((float)$j['precio'],2)?></div>
      <div class="detalle-envio">Envío gratis · Sin cargos de importación a República Dominicana</div>
      <?php if(logueado() && correoVerificado()): ?><form action="acciones_carrito.php" method="post" class="detalle-form"><input type="hidden" name="accion" value="agregar"><input type="hidden" name="id" value="<?=$j['id']?>"><button class="detalle-boton-principal" type="submit">Añadir al carrito</button></form><?php else: ?><a class="detalle-boton-principal" href="login.php">Inicia sesión para comprar</a><?php endif; ?>
      <a class="detalle-boton-secundario" href="carrito.php">Ver carrito</a>
    </div>
  </section>
  <section class="detalle-info" id="detalle-completo"><span>DETALLES</span><h2>Sobre este juego</h2><p><?=htmlspecialchars($desc)?></p></section>
</main>
<?php require 'includes/footer.php'; ?>
