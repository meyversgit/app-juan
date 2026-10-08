<?php
require_once '../config/db.php';require_once '../config/auth.php';require_once '../config/uploads.php';
soloAdmin();
$id=(int)($_GET['id']??0);
$st=$pdo->prepare('SELECT imagen FROM juegos WHERE id=?');$st->execute([$id]);$j=$st->fetch();
if($j){$pdo->prepare('DELETE FROM juegos WHERE id=?')->execute([$id]);eliminarImagenSubida($j['imagen']);}
header('Location: index.php');exit;
?>
