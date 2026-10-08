<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

function usuarioActual(): ?array { return $_SESSION['usuario'] ?? null; }
function logueado(): bool { return isset($_SESSION['usuario']); }
function rolActual(): string { return $_SESSION['usuario']['rol'] ?? ''; }
function admin(): bool { return in_array(rolActual(), ['admin','superadmin'], true); }
function superadmin(): bool { return rolActual() === 'superadmin'; }
function correoVerificado(): bool { return logueado() && (int)($_SESSION['usuario']['email_verificado'] ?? 0) === 1; }

function soloAdmin(): void {
    if (!admin()) { header('Location: ../login.php'); exit; }
}
function soloSuperadmin(): void {
    if (!superadmin()) { header('Location: ../index.php'); exit; }
}
function exigirCorreoVerificado(string $base = ''): void {
    if (logueado() && !correoVerificado()) {
        header('Location: ' . $base . 'login.php?verify=1');
        exit;
    }
}
function flash(string $tipo, string $mensaje): void { $_SESSION['flash']=['tipo'=>$tipo,'mensaje'=>$mensaje]; }
function obtenerFlash(): ?array { $f=$_SESSION['flash']??null; unset($_SESSION['flash']); return $f; }
function refrescarUsuarioSesion(PDO $pdo): void {
    if (!logueado()) return;
    $st=$pdo->prepare('SELECT id,nombre,correo,rol,email_verificado,foto_perfil FROM usuarios WHERE id=? LIMIT 1');
    $st->execute([(int)$_SESSION['usuario']['id']]);
    if($u=$st->fetch()) $_SESSION['usuario']=$u;
}
?>
