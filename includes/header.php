<?php
require_once __DIR__.'/../config/auth.php';
require_once __DIR__.'/../config/carrito.php';
require_once __DIR__.'/../config/db.php';
$u=usuarioActual();
$sidebarItems=[]; $sidebarTotal=0.0;
if (logueado() && correoVerificado()) {
    initCarrito();
    $ids=array_keys($_SESSION['carrito']);
    if($ids){
        $marks=implode(',',array_fill(0,count($ids),'?'));
        $q=$pdo->prepare("SELECT id,nombre,precio,imagen FROM juegos WHERE id IN ($marks)");
        $q->execute($ids);
        foreach($q->fetchAll() as $sj){
            $cant=(int)($_SESSION['carrito'][$sj['id']]??0);
            if($cant<=0) continue;
            $sub=$cant*(float)$sj['precio']; $sidebarTotal+=$sub;
            $sidebarItems[]=['j'=>$sj,'cant'=>$cant,'sub'=>$sub];
        }
    }
}
if ($u && !correoVerificado()) {
    $permitidas=['login.php','logout.php'];
    $actual=basename($_SERVER['PHP_SELF']??'');
    if(!in_array($actual,$permitidas,true)) { header('Location: '.($base??'').'login.php?verify=1'); exit; }
}
$flash=obtenerFlash();
$actualRuta=$_SERVER['PHP_SELF']??'';
$esInicio=str_ends_with($actualRuta,'/index.php') && !str_contains($actualRuta,'/admin/');
$esProductos=str_ends_with($actualRuta,'/productos.php') || str_ends_with($actualRuta,'/producto.php');
$esContacto=str_ends_with($actualRuta,'/contacto.php');
$esAdmin=str_contains($actualRuta,'/admin/');
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title><?=htmlspecialchars($titulo??'GameZone Store')?></title><link rel="stylesheet" href="<?=$base??''?>css/style.css"><link rel="shortcut icon" href="<?=$base??''?>assets/logo.webp">
<style id="header-admin-fix">
/* HEADER ADMIN ACTIVO FIX */
header .div-elemento-header.activo,
.div-elemento-header.activo{background:#fff!important;color:#111!important;border:1px solid #fff!important;box-shadow:none!important;outline:none!important}
header .div-elemento-header.activo a,
header .div-elemento-header.activo .link-header,
.div-elemento-header.activo a,
.div-elemento-header.activo .link-header{background:transparent!important;color:#111!important;border:none!important;box-shadow:none!important;text-decoration:none!important}
</style>
</head><body>
<header>
  <div class="header-col header-left"><a class="logo" href="<?=$base??''?>index.php"><img src="<?=$base??''?>assets/logo.webp" alt="GameZone Store"></a></div>
  <nav class="header-nav"><ul>
    <li class="div-elemento-header <?= $esInicio?'activo':'' ?>"><a class="link-header" href="<?=$base??''?>index.php">Inicio</a></li>
    <li class="div-elemento-header <?= $esProductos?'activo':'' ?>"><a class="link-header" href="<?=$base??''?>productos.php">Productos</a></li>
    <li class="div-elemento-header <?= $esContacto?'activo':'' ?>"><a class="link-header" href="<?=$base??''?>contacto.php">Contacto</a></li>
    <li class="div-elemento-header <?= (basename($_SERVER['PHP_SELF']??'')==='carrito.php')?'activo':'' ?>"><a class="link-header" href="<?=$base??''?>carrito.php">Carrito</a></li>
    <?php if(admin()): ?><li class="div-elemento-header <?= $esAdmin?'activo':'' ?>"><a class="link-header" href="<?=$base??''?>admin/index.php">Admin</a></li><?php endif; ?>
  </ul></nav>
  <div class="header-col header-right">
    <?php if(logueado()): ?>
      <a class="perfil-header" href="<?=$base??''?>perfil.php">
        <?php if(!empty($u['foto_perfil'])): ?><img class="perfil-header-foto" src="<?=$base??''?><?=htmlspecialchars($u['foto_perfil'])?>" alt="Foto de perfil"><?php else: ?><span class="perfil-header-fallback"><?=htmlspecialchars(strtoupper(mb_substr($u['nombre'],0,1)))?></span><?php endif; ?>
        <span class="perfil-header-texto"><span class="perfil-header-nombre"><?=htmlspecialchars($u['nombre'])?></span><span class="perfil-header-rol"><?=htmlspecialchars($u['rol'])?></span></span>
      </a>
    <?php else: ?><a class="link-login <?=basename($_SERVER['PHP_SELF']??'')==='login.php'?'activo-login':''?>" href="<?=$base??''?>login.php">Login</a><?php endif; ?>
    <button class="carrito-icono" type="button" data-cart-open aria-label="Abrir carrito"><img src="<?=$base??''?>assets/icons/shopping-cart.webp" alt="Carrito"><span class="carrito-contador"><?=cantidadCarrito()?></span></button>
  </div>
</header>
<div class="carrito-overlay" data-cart-close></div>
<aside class="carrito-aside" id="carritoSidebar" aria-hidden="true">
  <div class="carrito-sidebar-head">
    <div><span class="carrito-sidebar-kicker">GAMEZONE STORE</span><h2>Tu carrito</h2></div>
    <button class="carrito-cerrar" type="button" data-cart-close aria-label="Cerrar carrito">×</button>
  </div>
  <?php if(!logueado()): ?>
    <div class="carrito-sidebar-vacio"><div class="carrito-sidebar-icon"><img src="<?=$base??''?>assets/icons/shopping-cart.webp" alt="Carrito"></div><h3>Inicia sesión</h3><p>Necesitas iniciar sesión para agregar juegos a tu carrito.</p><a class="carrito-sidebar-btn" href="<?=$base??''?>login.php">Iniciar sesión</a></div>
  <?php elseif(!$sidebarItems): ?>
    <div class="carrito-sidebar-vacio"><div class="carrito-sidebar-icon"><img src="<?=$base??''?>assets/icons/shopping-cart.webp" alt="Carrito"></div><h3>Tu carrito está vacío</h3><p>Agrega un juego y aparecerá aquí.</p><a class="carrito-sidebar-btn" href="<?=$base??''?>productos.php">Explorar productos</a></div>
  <?php else: ?>
    <div class="carrito-items">
      <?php foreach($sidebarItems as $si): $sj=$si['j']; ?>
      <div class="carrito-item">
        <img class="carrito-item-img" src="<?=$base??''?><?=htmlspecialchars($sj['imagen'])?>" alt="<?=htmlspecialchars($sj['nombre'])?>">
        <div class="carrito-item-info"><strong class="carrito-item-nombre"><?=htmlspecialchars($sj['nombre'])?></strong><span class="carrito-item-precio">US$<?=number_format((float)$sj['precio'],2)?></span><span class="carrito-item-cantidad">Cantidad: <?=$si['cant']?></span></div>
        <a class="carrito-item-eliminar" href="<?=$base??''?>acciones_carrito.php?accion=quitar&id=<?=$sj['id']?>" aria-label="Eliminar <?=htmlspecialchars($sj['nombre'])?>">×</a>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="carrito-sidebar-resumen"><span>Total</span><strong>US$<?=number_format($sidebarTotal,2)?></strong></div>
    <div class="carrito-sidebar-acciones"><a class="carrito-sidebar-btn" href="<?=$base??''?>carrito.php">Ver carrito completo</a><a class="carrito-sidebar-link" href="<?=$base??''?>acciones_carrito.php?accion=vaciar">Vaciar carrito</a></div>
  <?php endif; ?>
</aside>
<script>
(()=>{const aside=document.getElementById('carritoSidebar');const open=document.querySelector('[data-cart-open]');const closes=document.querySelectorAll('[data-cart-close]');if(!aside||!open)return;const setOpen=(v)=>{aside.classList.toggle('abierto',v);document.body.classList.toggle('carrito-bloqueado',v);aside.setAttribute('aria-hidden',v?'false':'true')};open.addEventListener('click',()=>setOpen(true));closes.forEach(el=>el.addEventListener('click',()=>setOpen(false)));document.addEventListener('keydown',e=>{if(e.key==='Escape')setOpen(false)});})();
</script>
<?php if($flash): ?><div class="flash-php <?=htmlspecialchars($flash['tipo'])?>" role="alert"><?=htmlspecialchars($flash['mensaje'])?></div><?php endif; ?>
