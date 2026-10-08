<?php
require_once '../config/db.php';require_once '../config/auth.php';require_once '../config/uploads.php';
soloAdmin();$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $nombre=trim($_POST['nombre']??'');$desc=trim($_POST['descripcion']??'');$precio=(float)($_POST['precio']??0);$cat=$_POST['categoria']??'';$slug=preg_replace('/[^a-z0-9-]+/','-',strtolower(trim($_POST['slug']??$nombre)));
    try{
        if(!$nombre||!$desc||$precio<=0||!in_array($cat,['PS5','PS4','Switch'],true)||!$slug) throw new RuntimeException('Completa todos los campos.');
        if(empty($_FILES['imagen']['name'])) throw new RuntimeException('Debes seleccionar una imagen.');
        $img=guardarImagenWebp($_FILES['imagen'],__DIR__.'/../assets/uploads');
        $st=$pdo->prepare('INSERT INTO juegos(nombre,descripcion,precio,imagen,categoria,slug) VALUES(?,?,?,?,?,?)');
        $st->execute([$nombre,$desc,$precio,$img,$cat,$slug]);header('Location: index.php');exit;
    }catch(Throwable $e){$error=$e->getMessage();}
}
$titulo='Agregar juego - GameZone Store';$base='../';require '../includes/header.php'; ?>
<?php if($error): ?><div class="alert-php"><?=htmlspecialchars($error)?></div><?php endif; ?>
<div class="form-php admin-form"><h1>Agregar juego</h1><form method="post" enctype="multipart/form-data">
<label>Nombre</label><input name="nombre" required>
<label>Descripción</label><textarea name="descripcion" rows="6" required></textarea>
<label>Precio</label><input type="number" step="0.1" min="0.1" name="precio" required>
<label>Imagen del juego</label><input id="imagen-juego" type="file" name="imagen" accept="image/jpeg,image/png,image/webp" required><div class="upload-preview" id="preview-juego" hidden><span>Vista previa</span><img id="preview-juego-img" alt="Vista previa de la imagen seleccionada"></div><small>JPG, PNG o WebP. Se convertirá a WebP y se recortarán automáticamente los márgenes blancos exteriores.</small>
<label>Categoría</label><select name="categoria"><option>PS5</option><option>PS4</option><option>Switch</option></select>
<label>Slug</label><input name="slug" placeholder="mi-juego" required>
<button type="submit">Guardar</button></form></div><script>
(()=>{const input=document.getElementById("imagen-juego"),preview=document.getElementById("preview-juego"),img=document.getElementById("preview-juego-img");if(!input||!preview||!img)return;input.addEventListener("change",()=>{const file=input.files&&input.files[0];if(!file){preview.hidden=true;return}if(img.dataset.url)URL.revokeObjectURL(img.dataset.url);const url=URL.createObjectURL(file);img.dataset.url=url;img.src=url;preview.hidden=false;requestAnimationFrame(()=>preview.scrollIntoView({behavior:"smooth",block:"center"}));});})();
</script><?php require '../includes/footer.php'; ?>
