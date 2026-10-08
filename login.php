<?php
require_once 'config/db.php';
require_once 'config/auth.php';
require_once 'config/mail.php';

if (logueado()) refrescarUsuarioSesion($pdo);
$u = usuarioActual();
if ($u && correoVerificado()) { header('Location: ' . (in_array($u['rol'], ['admin','superadmin'], true) ? 'admin/index.php' : 'index.php')); exit; }

$error=''; $ok='';
$modoVerificacion = $u && !correoVerificado();

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $accion=$_POST['accion']??'login';

    if ($accion==='verificar' && $modoVerificacion) {
        $codigo=preg_replace('/\D/','',$_POST['codigo']??'');
        $st=$pdo->prepare('SELECT codigo_verificacion,codigo_expira FROM usuarios WHERE id=?');
        $st->execute([(int)$u['id']]); $row=$st->fetch();
        if($row && hash_equals((string)$row['codigo_verificacion'], $codigo) && strtotime((string)$row['codigo_expira']) >= time()) {
            $up=$pdo->prepare('UPDATE usuarios SET email_verificado=1,codigo_verificacion=NULL,codigo_expira=NULL WHERE id=?');
            $up->execute([(int)$u['id']]); refrescarUsuarioSesion($pdo);
            $u=usuarioActual();
            header('Location: '.(in_array($u['rol'],['admin','superadmin'],true)?'admin/index.php':'index.php')); exit;
        }
        $error='El código es incorrecto o ya expiró.';
    } elseif ($accion==='reenviar' && $modoVerificacion) {
        $codigo=(string)random_int(100000,999999); $expira=date('Y-m-d H:i:s',time()+900);
        if(enviarCodigoVerificacion($u['correo'],$u['nombre'],$codigo)) {
            $up=$pdo->prepare('UPDATE usuarios SET codigo_verificacion=?,codigo_expira=? WHERE id=?');
            $up->execute([$codigo,$expira,(int)$u['id']]); $ok='Te enviamos un nuevo código.';
        } else $error='No pudimos enviar el código. Revisa la configuración SMTP.';
    } else {
        $correo=trim($_POST['correo']??''); $clave=$_POST['clave']??'';
        $st=$pdo->prepare('SELECT id,nombre,correo,clave,rol,email_verificado,codigo_verificacion,codigo_expira,foto_perfil FROM usuarios WHERE correo=? LIMIT 1');
        $st->execute([$correo]); $usuario=$st->fetch();
        if(!$usuario || !password_verify($clave,$usuario['clave'])) {
            $error='Correo o contraseña incorrectos.';
        } elseif((int)$usuario['email_verificado']!==1) {
            $codigo=(string)random_int(100000,999999); $expira=date('Y-m-d H:i:s',time()+900);
            if(!enviarCodigoVerificacion($usuario['correo'],$usuario['nombre'],$codigo)) {
                $error='Tu cuenta aún no está verificada y no pudimos enviar el código. Revisa SMTP.';
            } else {
                $up=$pdo->prepare('UPDATE usuarios SET codigo_verificacion=?,codigo_expira=? WHERE id=?');
                $up->execute([$codigo,$expira,(int)$usuario['id']]);
                unset($usuario['clave']); session_regenerate_id(true); $_SESSION['usuario']=$usuario;
                header('Location: login.php?verify=1'); exit;
            }
        } else {
            unset($usuario['clave']); session_regenerate_id(true); $_SESSION['usuario']=$usuario;
            header('Location: '.(in_array($usuario['rol'],['admin','superadmin'],true)?'admin/index.php':'index.php')); exit;
        }
    }
}
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Iniciar sesión - GameZone Store</title><link rel="stylesheet" href="css/login.css">
</head>
<body class="login-body"><div class="login-shell">
<a class="login-logo" href="index.php"><img src="assets/logo.webp" alt="GameZone Store"><span>GameZone Store</span></a>
<section class="login-card">
<div class="login-icon"><img src="assets/logo.webp" alt=""></div>
<span class="login-mini"><?= $modoVerificacion ? 'VERIFICACIÓN DE CORREO' : 'GAMEZONE STORE' ?></span>
<h1><?= $modoVerificacion ? 'Verifica tu correo' : 'Iniciar sesión' ?></h1>
<?php if($modoVerificacion): ?>
<p>Tu cuenta está creada, pero debes confirmar <strong><?=htmlspecialchars($u['correo'])?></strong> antes de entrar a la tienda.</p>
<?php if($error): ?><div class="login-alert"><?=htmlspecialchars($error)?></div><?php endif; ?>
<?php if($ok): ?><div class="login-alert login-ok"><?=htmlspecialchars($ok)?></div><?php endif; ?>
<form method="post" class="verification-form"><input type="hidden" name="accion" value="verificar"><label for="codigo">Código de 6 dígitos</label><input id="codigo" name="codigo" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" placeholder="000000" autocomplete="one-time-code" required><button type="submit">Verificar correo</button></form>
<form method="post" class="resend-form"><input type="hidden" name="accion" value="reenviar"><button class="login-secondary" type="submit">Enviar otro código</button></form>
<a class="login-back" href="index.php">Volver al inicio</a>
<?php else: ?>
<p>Entra a tu cuenta para acceder a tu perfil, catálogo y carrito.</p>
<?php if($error): ?><div class="login-alert"><?=htmlspecialchars($error)?></div><?php endif; ?>
<form method="post"><label for="correo">Correo electrónico</label><input id="correo" type="email" name="correo" autocomplete="email" required><label for="clave">Contraseña</label><div class="password-wrap"><input id="clave" type="password" name="clave" autocomplete="current-password" required><button type="button" class="password-toggle" data-target="clave" aria-label="Mostrar contraseña" title="Mostrar contraseña"><svg class="eye-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg></button></div><button type="submit">Entrar</button></form>
<div class="login-links"><span>¿No tienes cuenta?</span><a href="registro.php">Crear cuenta</a></div><a class="login-back" href="index.php">← Volver a GameZone Store</a>
<?php endif; ?>
</section></div><script>document.querySelectorAll(".password-toggle").forEach(b=>b.addEventListener("click",()=>{const i=document.getElementById(b.dataset.target);const show=i.type==="password";i.type=show?"text":"password";b.setAttribute("aria-label",show?"Ocultar contraseña":"Mostrar contraseña");b.title=show?"Ocultar contraseña":"Mostrar contraseña";}));</script></body></html>
