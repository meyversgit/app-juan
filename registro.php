<?php
require_once 'config/db.php'; require_once 'config/auth.php'; require_once 'config/mail.php';
if(logueado()){ header('Location: login.php?verify=1'); exit; }
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $nombre=trim($_POST['nombre']??''); $correo=trim($_POST['correo']??''); $clave=$_POST['clave']??'';
    if($nombre==='' || !filter_var($correo,FILTER_VALIDATE_EMAIL) || strlen($clave)<8) {
        $error='Completa los datos correctamente. La contraseña debe tener mínimo 8 caracteres.';
    } elseif(!dominioCorreoValido($correo)) {
        $error='Ese correo no pertenece a un dominio válido o no puede recibir correos.';
    } else {
        $st=$pdo->prepare('SELECT id FROM usuarios WHERE correo=? LIMIT 1'); $st->execute([$correo]);
        if($st->fetch()) $error='Ese correo ya está registrado.';
        else {
            $codigo=(string)random_int(100000,999999); $expira=date('Y-m-d H:i:s',time()+900);
            if(!enviarCodigoVerificacion($correo,$nombre,$codigo)) {
                $error='No pudimos enviar el código a ese correo. La cuenta no fue creada. Revisa que el correo exista y que SMTP esté configurado.';
            } else {
                $st=$pdo->prepare("INSERT INTO usuarios(nombre,correo,clave,rol,email_verificado,codigo_verificacion,codigo_expira) VALUES(?,?,?,'usuario',0,?,?)");
                $st->execute([$nombre,$correo,password_hash($clave,PASSWORD_DEFAULT),$codigo,$expira]);
                $id=(int)$pdo->lastInsertId(); session_regenerate_id(true);
                $_SESSION['usuario']=['id'=>$id,'nombre'=>$nombre,'correo'=>$correo,'rol'=>'usuario','email_verificado'=>0];
                header('Location: login.php?verify=1'); exit;
            }
        }
    }
}
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Crear cuenta - GameZone Store</title><link rel="stylesheet" href="css/login.css">
</head>
<body class="login-body"><div class="login-shell"><a class="login-logo" href="index.php"><img src="assets/logo.webp"><span>GameZone Store</span></a><section class="login-card"><div class="login-icon"><img src="assets/logo.webp"></div><span class="login-mini">NUEVA CUENTA</span><h1>Crear cuenta</h1><p>Usa un correo real. Te enviaremos un código antes de permitirte entrar.</p><?php if($error): ?><div class="login-alert"><?=htmlspecialchars($error)?></div><?php endif; ?><form method="post"><label>Nombre completo</label><input name="nombre" required><label>Correo electrónico</label><input type="email" name="correo" autocomplete="email" required><label>Contraseña</label><div class="password-wrap"><input id="registro-clave" type="password" name="clave" minlength="8" required><button type="button" class="password-toggle" data-target="registro-clave" aria-label="Mostrar contraseña" title="Mostrar contraseña"><svg class="eye-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg></button></div><button type="submit">Crear cuenta</button></form><div class="login-links"><span>¿Ya tienes cuenta?</span><a href="login.php">Iniciar sesión</a></div><a class="login-back" href="index.php">← Volver</a></section></div><script>document.querySelectorAll(".password-toggle").forEach(b=>b.addEventListener("click",()=>{const i=document.getElementById(b.dataset.target);const show=i.type==="password";i.type=show?"text":"password";b.setAttribute("aria-label",show?"Ocultar contraseña":"Mostrar contraseña");b.title=show?"Ocultar contraseña":"Mostrar contraseña";}));</script></body></html>
