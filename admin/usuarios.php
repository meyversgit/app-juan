<?php
require_once '../config/db.php';require_once '../config/auth.php';
soloAdmin();
if($_SERVER['REQUEST_METHOD']==='POST'){
    $id=(int)($_POST['id']??0);$accion=$_POST['accion']??'';
    $st=$pdo->prepare("SELECT id,nombre,rol FROM usuarios WHERE id=?");$st->execute([$id]);$objetivo=$st->fetch();
    if(!$objetivo){flash('error','Usuario no encontrado.');}
    elseif((int)$objetivo['id']===(int)$_SESSION['usuario']['id']){flash('error','No puedes modificar tu propio rol desde aquí.');}
    elseif($objetivo['rol']==='superadmin' || ($objetivo['rol']==='admin' && !superadmin())){flash('error','Solo el superadmin puede modificar o retirar el rol de un administrador.');}
    elseif($accion==='promover'){$pdo->prepare("UPDATE usuarios SET rol='admin' WHERE id=? AND rol='usuario'")->execute([$id]);flash('ok','El usuario ahora es administrador.');}
    elseif($accion==='quitar_admin' && superadmin()){$pdo->prepare("UPDATE usuarios SET rol='usuario' WHERE id=? AND rol='admin'")->execute([$id]);flash('ok','Se retiró el rol de administrador.');}
    else{flash('error','No tienes permisos para esa acción.');}
    header('Location: usuarios.php');exit;
}
$usuarios=$pdo->query("SELECT id,nombre,correo,rol,email_verificado,creado_en FROM usuarios ORDER BY FIELD(rol,'superadmin','admin','usuario'),nombre")->fetchAll();
$titulo='Usuarios - GameZone Store';$base='../';require '../includes/header.php';
?>
<main class="admin-usuarios-page">
<div class="admin-top"><div><h1>Usuarios</h1><p>Gestiona los roles respetando la jerarquía de seguridad.</p></div><a class="boton-php" href="index.php">Volver</a></div>
<table class="tabla-php usuarios-tabla"><tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Correo</th><th>Acciones</th></tr>
<?php foreach($usuarios as $x): ?><tr><td><?=htmlspecialchars($x['nombre'])?></td><td><?=htmlspecialchars($x['correo'])?></td><td><span class="rol-badge rol-<?=htmlspecialchars($x['rol'])?>"><?=htmlspecialchars($x['rol'])?></span></td><td><?=((int)$x['email_verificado']===1)?'Verificado':'Pendiente'?></td><td>
<?php if($x['rol']==='usuario'): ?><form class="inline-form" method="post"><input type="hidden" name="id" value="<?=$x['id']?>"><input type="hidden" name="accion" value="promover"><button type="submit">Hacer admin</button></form>
<?php elseif($x['rol']==='admin' && superadmin()): ?><form class="inline-form" method="post"><input type="hidden" name="id" value="<?=$x['id']?>"><input type="hidden" name="accion" value="quitar_admin"><button type="submit">Quitar admin</button></form>
<?php else: ?><span class="texto-suave">Protegido</span><?php endif; ?>
</td></tr><?php endforeach; ?></table>
</main>
<?php require '../includes/footer.php'; ?>
