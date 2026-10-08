<?php
require_once 'config/db.php';
require_once 'config/auth.php';
require_once 'config/uploads.php';
if(!logueado()){ header('Location: login.php'); exit; }
refrescarUsuarioSesion($pdo); $u=usuarioActual(); $error=''; $ok='';
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['accion']) && $_POST['accion']==='foto'){
    try{
        if(empty($_FILES['foto']['name'])) throw new RuntimeException('Selecciona una imagen.');
        $foto=guardarImagenWebp($_FILES['foto'],__DIR__.'/assets/uploads');
        if(!empty($u['foto_perfil'])) eliminarImagenSubida($u['foto_perfil']);
        $st=$pdo->prepare('UPDATE usuarios SET foto_perfil=? WHERE id=?'); $st->execute([$foto,(int)$u['id']]);
        refrescarUsuarioSesion($pdo); $u=usuarioActual(); $ok='Foto de perfil actualizada.';
    }catch(Throwable $e){$error=$e->getMessage();}
}
$titulo='Mi perfil - GameZone Store'; $base=''; require 'includes/header.php';
?>
<main class="perfil-pagina">
<div class="perfil-card">
    <div class="perfil-avatar-wrap">
      <?php if(!empty($u['foto_perfil'])): ?><img class="perfil-avatar perfil-avatar-img" src="<?=htmlspecialchars($u['foto_perfil'])?>" alt="Foto de perfil"><?php else: ?><div class="perfil-avatar"><?=htmlspecialchars(strtoupper(mb_substr($u['nombre'],0,1)))?></div><?php endif; ?>
    </div>
    <h1><?=htmlspecialchars($u['nombre'])?></h1>
    <span class="rol-badge rol-<?=htmlspecialchars($u['rol'])?>"><?=htmlspecialchars(strtoupper($u['rol']))?></span>
    <?php if($error): ?><div class="alert-php error-local"><?=htmlspecialchars($error)?></div><?php endif; ?>
    <?php if($ok): ?><div class="alert-php ok-local"><?=htmlspecialchars($ok)?></div><?php endif; ?>
    <form class="foto-form" method="post" enctype="multipart/form-data"><input type="hidden" name="accion" value="foto"><label class="foto-selector"><span>Subir / cambiar foto</span><input id="foto-perfil" type="file" name="foto" accept="image/jpeg,image/png,image/webp" required></label><div class="upload-preview upload-preview-perfil" id="preview-perfil" hidden><span>Vista previa</span><img id="preview-perfil-img" alt="Vista previa de la foto de perfil"></div><button class="boton-php" type="submit">Guardar foto</button></form><script>
(()=>{const input=document.getElementById("foto-perfil"),preview=document.getElementById("preview-perfil"),img=document.getElementById("preview-perfil-img");if(!input||!preview||!img)return;input.addEventListener("change",()=>{const file=input.files&&input.files[0];if(!file){preview.hidden=true;return}if(img.dataset.url)URL.revokeObjectURL(img.dataset.url);const url=URL.createObjectURL(file);img.dataset.url=url;img.src=url;preview.hidden=false;requestAnimationFrame(()=>preview.scrollIntoView({behavior:"smooth",block:"center"}));});})();
</script>
    <div class="perfil-datos">
        <div><span>Nombre</span><strong><?=htmlspecialchars($u['nombre'])?></strong></div>
        <div><span>Correo</span><strong><?=htmlspecialchars($u['correo'])?></strong></div>
        <div><span>Rol</span><strong><?=htmlspecialchars($u['rol'])?></strong></div>
        <div><span>Correo verificado</span><strong><?=((int)$u['email_verificado']===1)?'Sí':'No'?></strong></div>
    </div>
    <div class="acciones-perfil">
        <?php if((int)$u['email_verificado']!==1): ?><a class="boton-php" href="verificar.php">Verificar correo</a><?php endif; ?>
        <?php if(admin()): ?><a class="boton-php" href="admin/index.php">Panel de administración</a><?php endif; ?>
        <a class="boton-php boton-salir-perfil" href="logout.php">Cerrar sesión</a>
    </div>
</div></main>
<?php require 'includes/footer.php'; ?>