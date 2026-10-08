<?php
require_once '../config/db.php';require_once '../config/auth.php';soloAdmin();
$juegos=$pdo->query('SELECT * FROM juegos ORDER BY id DESC')->fetchAll();
$titulo='Administración - GameZone Store';$base='../';require '../includes/header.php'; ?>
<main class="admin-page">
<div class="admin-top"><div><span class="admin-kicker">PANEL</span><h1>Administración</h1><p>Gestiona el catálogo de GameZone Store.</p></div><div class="acciones-admin"><a class="boton-php" href="agregar.php">+ Agregar juego</a><a class="boton-php" href="usuarios.php">Usuarios</a></div></div>
<div class="admin-grid">
<?php foreach($juegos as $j): ?><article class="admin-product-card">
  <div class="admin-product-img"><img src="../<?=htmlspecialchars($j['imagen'])?>" alt="<?=htmlspecialchars($j['nombre'])?>"></div>
  <div class="admin-product-info"><span class="admin-cat"><?=htmlspecialchars($j['categoria'])?></span><h2><?=htmlspecialchars($j['nombre'])?></h2><strong>US$<?=number_format((float)$j['precio'],2)?></strong></div>
  <div class="admin-product-actions">
    <a class="admin-icon-btn editar" href="editar.php?id=<?=$j['id']?>" title="Editar" aria-label="Editar"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20h4L19 9l-4-4L4 16v4Zm10-13 4 4"/></svg></a>
    <a class="admin-icon-btn eliminar" href="eliminar.php?id=<?=$j['id']?>" title="Eliminar" aria-label="Eliminar" onclick="return confirm('¿Eliminar este juego?')"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 7h14M9 7V4h6v3m-8 0 1 13h6l1-13M10 11v5m4-5v5"/></svg></a>
  </div>
</article><?php endforeach; ?></div></main>
<?php require '../includes/footer.php'; ?>