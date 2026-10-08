<?php require_once 'config/db.php'; require_once 'config/carrito.php'; require_once 'config/auth.php'; initCarrito(); $ids=array_keys($_SESSION['carrito']); $items=[];$total=0; if($ids){$q=implode(',',array_fill(0,count($ids),'?'));$st=$pdo->prepare("SELECT * FROM juegos WHERE id IN ($q)");$st->execute($ids);foreach($st->fetchAll() as $j){$cant=(int)$_SESSION['carrito'][$j['id']];$sub=$cant*(float)$j['precio'];$total+=$sub;$items[]=['j'=>$j,'cant'=>$cant,'sub'=>$sub];}} $titulo='Carrito - GameZone Store';$base='';require 'includes/header.php'; ?>
<main class="carrito-pagina">
  <div class="carrito-hero"><span class="carrito-kicker">GAMEZONE STORE</span><h1>Tu carrito</h1><p>Revisa tus juegos antes de continuar.</p></div>
<?php if(!$items): ?><div class="vacio-php">
  <div class="vacio-carrito-icono"><img src="assets/icons/shopping-cart.webp" alt="Carrito"></div>
  Tu carrito está vacío.<br>
  <a href="productos.php" class="boton-php">Explorar productos</a>
</div><?php else: ?>
  <form id="carritoForm" action="acciones_carrito.php" method="post">
    <input type="hidden" name="accion" value="actualizar">
    <div class="carrito-contenido">
      <div class="carrito-listado-php">
      <?php foreach($items as $it): $j=$it['j']; ?>
        <div class="carrito-fila">
          <img src="<?=htmlspecialchars($j['imagen'])?>" alt="<?=htmlspecialchars($j['nombre'])?>">
          <div class="nombre"><strong><?=htmlspecialchars($j['nombre'])?></strong><small>US$<?=number_format((float)$j['precio'],2)?> por unidad</small></div>
          <input type="number" min="0" name="cantidades[<?=$j['id']?>]" value="<?=$it['cant']?>" aria-label="Cantidad de <?=htmlspecialchars($j['nombre'])?>" data-auto-update>
          <strong>US$<?=number_format($it['sub'],2)?></strong>
          <a class="carrito-eliminar" href="acciones_carrito.php?accion=quitar&id=<?=$j['id']?>" aria-label="Eliminar producto">×</a>
        </div>
      <?php endforeach; ?>
      </div>
      <aside class="carrito-resumen-php"><span class="resumen-kicker">RESUMEN</span><div class="carrito-total-php">US$<?=number_format($total,2)?></div><div class="carrito-resumen-accion"><a class="boton-php" href="acciones_carrito.php?accion=vaciar">Vaciar carrito</a></div><div class="carrito-resumen-accion"><a class="boton-php" href="productos.php">Seguir comprando</a></div></aside>
    </div>
  </form>
<?php endif; ?>
</main>
<script>
document.querySelectorAll('[data-auto-update]').forEach(input => {
  input.addEventListener('change', () => {
    const form = document.getElementById('carritoForm');
    if (form) form.requestSubmit();
  });
});
</script>
<?php require 'includes/footer.php'; ?>
