<?php
declare(strict_types=1);

/* SMTP REAL. Para Gmail usa una contraseña de aplicación, no tu contraseña normal. */
const SMTP_HOST = 'smtp.gmail.com';
const SMTP_PORT = 587;
const SMTP_USER = '';
const SMTP_PASSWORD = '';

// En Railway configura SMTP_USER y SMTP_PASSWORD como variables de entorno.
// En local puedes definirlas en el servidor antes de iniciar Apache.
const SMTP_FROM_NAME = 'GameZone Store';

// Sobrescribir las constantes con valores de entorno no es posible directamente,
// por eso las funciones usan estas variables.
function smtpUser(): string { return getenv('SMTP_USER') ?: SMTP_USER; }
function smtpPassword(): string { return getenv('SMTP_PASSWORD') ?: SMTP_PASSWORD; }

function dominioCorreoValido(string $correo): bool {
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) return false;
    $partes = explode('@', $correo);
    $dominio = strtolower(end($partes));
    if ($dominio === '' || !checkdnsrr($dominio, 'MX')) {
        return checkdnsrr($dominio, 'A') || checkdnsrr($dominio, 'AAAA');
    }
    return true;
}

function smtpRead($socket): string {
    $response = '';
    while (($line = fgets($socket, 515)) !== false) {
        $response .= $line;
        if (isset($line[3]) && $line[3] === ' ') break;
    }
    return $response;
}

function smtpCommand($socket, string $command, string $expected): bool {
    fwrite($socket, $command . "\r\n");
    return str_starts_with(smtpRead($socket), $expected);
}

function enviarCorreo(string $to, string $subject, string $html): bool {
    if (!dominioCorreoValido($to)) return false;
    if (smtpUser() === '' || smtpPassword() === '') return false;

    $socket = @fsockopen(SMTP_HOST, SMTP_PORT, $errno, $errstr, 15);
    if (!$socket) return false;
    smtpRead($socket);

    if (!smtpCommand($socket, 'EHLO localhost', '250')) { fclose($socket); return false; }
    if (!smtpCommand($socket, 'STARTTLS', '220')) { fclose($socket); return false; }
    if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) { fclose($socket); return false; }
    if (!smtpCommand($socket, 'EHLO localhost', '250')) { fclose($socket); return false; }
    if (!smtpCommand($socket, 'AUTH LOGIN', '334')) { fclose($socket); return false; }
    if (!smtpCommand($socket, base64_encode(smtpUser()), '334')) { fclose($socket); return false; }
    if (!smtpCommand($socket, base64_encode(smtpPassword()), '235')) { fclose($socket); return false; }
    if (!smtpCommand($socket, 'MAIL FROM:<' . smtpUser() . '>', '250')) { fclose($socket); return false; }
    if (!smtpCommand($socket, 'RCPT TO:<' . $to . '>', '250')) { fclose($socket); return false; }
    if (!smtpCommand($socket, 'DATA', '354')) { fclose($socket); return false; }

    $headers = [
        'From: ' . SMTP_FROM_NAME . ' <' . smtpUser() . '>',
        'To: <' . $to . '>',
        'Subject: =?UTF-8?B?' . base64_encode($subject) . '?=',
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit'
    ];
    $message = implode("\r\n", $headers) . "\r\n\r\n" . $html;
    $message = preg_replace('/^\./m', '..', $message);
    fwrite($socket, $message . "\r\n.\r\n");
    $ok = str_starts_with(smtpRead($socket), '250');
    fwrite($socket, "QUIT\r\n");
    fclose($socket);
    return $ok;
}

function enviarCodigoVerificacion(string $correo, string $nombre, string $codigo): bool {
    $html = '<div style="font-family:Arial,sans-serif;max-width:560px;margin:auto;padding:30px;background:#f2f2f2">'
          . '<div style="background:#050505;color:#fff;padding:28px;border-radius:14px;text-align:center">'
          . '<h1 style="margin:0 0 12px">GameZone Store</h1>'
          . '<p>Hola ' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . '.</p>'
          . '<p>Usa este código para verificar tu correo:</p>'
          . '<div style="font-size:34px;font-weight:bold;letter-spacing:8px;background:#fff;color:#000;padding:16px;border-radius:10px">' . $codigo . '</div>'
          . '<p>El código caduca en 15 minutos.</p>'
          . '</div></div>';
    return enviarCorreo($correo, 'Código de verificación - GameZone Store', $html);
}
?>
