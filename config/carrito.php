<?php
declare(strict_types=1);
if(session_status()!==PHP_SESSION_ACTIVE) session_start();
function initCarrito(): void { if(!isset($_SESSION['carrito'])) $_SESSION['carrito']=[]; }
function agregar(int $id): void { initCarrito(); $_SESSION['carrito'][$id]=($_SESSION['carrito'][$id]??0)+1; }
function quitar(int $id): void { initCarrito(); unset($_SESSION['carrito'][$id]); }
function actualizar(array $cantidades): void { initCarrito(); foreach($cantidades as $id=>$cant){$id=(int)$id;$cant=(int)$cant;if($cant<=0)unset($_SESSION['carrito'][$id]);else $_SESSION['carrito'][$id]=$cant;} }
function vaciar(): void { $_SESSION['carrito']=[]; }
function cantidadCarrito(): int { initCarrito(); return array_sum($_SESSION['carrito']); }
?>
