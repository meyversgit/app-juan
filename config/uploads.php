<?php
declare(strict_types=1);


/**
 * Elimina únicamente el espacio blanco que rodea la imagen.
 * No modifica el blanco que está dentro de la propia portada.
 */
function recortarMargenBlanco($src) {
    $w = imagesx($src);
    $h = imagesy($src);

    if ($w < 10 || $h < 10) return $src;

    $umbral = 248; // Consideramos casi blanco el RGB >= 248.
    $minPixels = 3;

    // Cuenta píxeles que claramente pertenecen al contenido de la imagen.
    $filaTieneContenido = function(int $y) use ($src, $w, $umbral, $minPixels): bool {
        $cantidad = 0;
        $paso = max(1, intdiv($w, 500));
        for ($x = 0; $x < $w; $x += $paso) {
            $rgb = imagecolorat($src, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            if ($r < $umbral || $g < $umbral || $b < $umbral) {
                $cantidad++;
                if ($cantidad >= $minPixels) return true;
            }
        }
        return false;
    };

    $columnaTieneContenido = function(int $x) use ($src, $h, $umbral, $minPixels): bool {
        $cantidad = 0;
        $paso = max(1, intdiv($h, 500));
        for ($y = 0; $y < $h; $y += $paso) {
            $rgb = imagecolorat($src, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            if ($r < $umbral || $g < $umbral || $b < $umbral) {
                $cantidad++;
                if ($cantidad >= $minPixels) return true;
            }
        }
        return false;
    };

    $left = 0;
    while ($left < $w && !$columnaTieneContenido($left)) $left++;

    $right = $w - 1;
    while ($right > $left && !$columnaTieneContenido($right)) $right--;

    $top = 0;
    while ($top < $h && !$filaTieneContenido($top)) $top++;

    $bottom = $h - 1;
    while ($bottom > $top && !$filaTieneContenido($bottom)) $bottom--;

    // Si prácticamente no había margen blanco, dejamos la imagen intacta.
    if ($left === 0 && $right === $w - 1 && $top === 0 && $bottom === $h - 1) {
        return $src;
    }

    $nw = $right - $left + 1;
    $nh = $bottom - $top + 1;

    // Evita recortes agresivos por pequeños artefactos en los bordes.
    if ($nw < $w * 0.20 || $nh < $h * 0.20) {
        return $src;
    }

    $dst = imagecreatetruecolor($nw, $nh);
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    imagecopy($dst, $src, 0, 0, $left, $top, $nw, $nh);
    imagedestroy($src);

    return $dst;
}

function guardarImagenWebp(array $archivo, string $directorio): string {
    if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Selecciona una imagen válida.');
    }
    if (($archivo['size'] ?? 0) > 8 * 1024 * 1024) {
        throw new RuntimeException('La imagen no puede superar 8 MB.');
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($archivo['tmp_name']);
    $permitidos = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    if (!isset($permitidos[$mime])) throw new RuntimeException('Solo se permiten JPG, PNG o WEBP.');

    if (!extension_loaded('gd')) throw new RuntimeException('La extensión GD de PHP debe estar activada para convertir imágenes a WebP.');

    $src = match($mime) {
        'image/jpeg' => imagecreatefromjpeg($archivo['tmp_name']),
        'image/png' => imagecreatefrompng($archivo['tmp_name']),
        'image/webp' => imagecreatefromwebp($archivo['tmp_name']),
    };
    if (!$src) throw new RuntimeException('No se pudo procesar la imagen.');

    // Recorta automáticamente los márgenes blancos exteriores.
    // Esto permite subir portadas con fondo blanco sin tener que editarlas
    // manualmente antes de agregarlas a la tienda.
    $src = recortarMargenBlanco($src);

    $w=imagesx($src);$h=imagesy($src);
    $max=1600;
    if($w>$max || $h>$max){
        $ratio=min($max/$w,$max/$h);$nw=max(1,(int)($w*$ratio));$nh=max(1,(int)($h*$ratio));
        $dst=imagecreatetruecolor($nw,$nh);
        imagealphablending($dst,false);imagesavealpha($dst,true);
        imagecopyresampled($dst,$src,0,0,0,0,$nw,$nh,$w,$h);
        imagedestroy($src);$src=$dst;
    }
    imagealphablending($src,false);imagesavealpha($src,true);
    if(!is_dir($directorio)) mkdir($directorio,0775,true);
    $nombre=bin2hex(random_bytes(10)).'.webp';
    $ruta=$directorio.'/'.$nombre;
    if(!imagewebp($src,$ruta,88)){imagedestroy($src);throw new RuntimeException('No se pudo guardar la imagen WebP.');}
    imagedestroy($src);
    return 'assets/uploads/'.$nombre;
}
function eliminarImagenSubida(string $ruta): void {
    if(str_starts_with($ruta,'assets/uploads/')){
        $file=__DIR__.'/../'.$ruta;
        if(is_file($file)) @unlink($file);
    }
}
?>
