<?php
require_once '../config/db.php';require_once '../config/auth.php';require_once '../config/uploads.php';
soloAdmin();$id=(int)($_GET['id']??$_POST['id']??0);$st=$pdo->prepare('SELECT * FROM juegos WHERE id=?');$st->execute([$id]);$j=$st->fetch();if(!$j){header('Location: index.php');exit;}$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $nombre=trim($_POST['nombre']??'');$desc=trim($_POST['descripcion']??'');$precio=(float)($_POST['precio']??0);$cat=$_POST['categoria']??'';$slug=trim($_POST['slug']??'');
    try{
        if(!$nombre||!$desc||$precio<=0||!$slug||!in_array($cat,['PS5','PS4','Switch'],true)) throw new RuntimeException('Completa todos los campos.');
        $img=$j['imagen'];
        if(!empty($_FILES['imagen']['name'])){$img=guardarImagenWebp($_FILES['imagen'],__DIR__.'/../assets/uploads');eliminarImagenSubida($j['imagen']);}
        $st=$pdo->prepare('UPDATE juegos SET nombre=?,descripcion=?,precio=?,imagen=?,categoria=?,slug=? WHERE id=?');
        $st->execute([$nombre,$desc,$precio,$img,$cat,$slug,$id]);header('Location: index.php');exit;
    }catch(Throwable $e){$error=$e->getMessage();}
}
$titulo='Editar juego - GameZone Store';$base='../';require '../includes/header.php'; ?>
<?php if($error): ?><div class="alert-php"><?=htmlspecialchars($error)?></div><?php endif; ?>
<div class="form-php admin-form"><h1>Editar juego</h1><form method="post" enctype="multipart/form-data"><input type="hidden" name="id" value="<?=$id?>">
<label>Nombre</label><input name="nombre" value="<?=htmlspecialchars($j['nombre'])?>" required>
<label>Descripción</label><textarea name="descripcion" rows="6" required><?=htmlspecialchars($j['descripcion'])?></textarea>
<label>Precio</label><input type="number" step="0.1" name="precio" value="<?=htmlspecialchars($j['precio'])?>" required>
<label>Imagen actual</label><div class="preview-imagen"><img src="../<?=htmlspecialchars($j['imagen'])?>" alt=""></div><label>Reemplazar imagen (opcional)</label><input id="imagen-juego" type="file" name="imagen" accept="image/jpeg,image/png,image/webp"><div class="upload-preview" id="preview-juego" hidden><span>Vista previa de la nueva imagen</span><img id="preview-juego-img" alt="Vista previa de la nueva imagen"></div><small>Si seleccionas otra, se convertirá a WebP y se recortarán automáticamente los márgenes blancos exteriores.</small>
<label>Categoría</label><select name="categoria"><?php foreach(['PS5','PS4','Switch'] as $c): ?><option <?=$j['categoria']===$c?'selected':''?>><?=$c?></option><?php endforeach; ?></select>
<label>Slug</label><input name="slug" value="<?=htmlspecialchars($j['slug'])?>" required><button type="submit">Guardar cambios</button></form></div><script>
(()=>{const input=document.getElementById("imagen-juego"),preview=document.getElementById("preview-juego"),img=document.getElementById("preview-juego-img");if(!input||!preview||!img)return;input.addEventListener("change",()=>{const file=input.files&&input.files[0];if(!file){preview.hidden=true;return}if(img.dataset.url)URL.revokeObjectURL(img.dataset.url);const url=URL.createObjectURL(file);img.dataset.url=url;img.src=url;preview.hidden=false;requestAnimationFrame(()=>preview.scrollIntoView({behavior:"smooth",block:"center"}));});})();
</script><?php require '../includes/footer.php'; ?>
