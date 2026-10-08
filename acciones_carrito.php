<?php
require_once 'config/auth.php';
require_once 'config/carrito.php';
$accion=$_POST['accion']??$_GET['accion']??'';
if($accion==='agregar') {
    if(!logueado()) { flash('error','Debes iniciar sesión para agregar productos al carrito.'); header('Location: login.php'); exit; }
    if(!correoVerificado()) { flash('error','Verifica tu correo antes de usar el carrito.'); header('Location: login.php?verify=1'); exit; }
}
$id=(int)($_POST['id']??$_GET['id']??0);
if($accion==='agregar') agregar($id);
elseif($accion==='quitar') quitar($id);
elseif($accion==='actualizar') actualizar($_POST['cantidades']??[]);
elseif($accion==='vaciar') vaciar();
header('Location: '.($_SERVER['HTTP_REFERER']??'productos.php')); exit;
?>
