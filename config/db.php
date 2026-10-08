<?php
declare(strict_types=1);

/*
 * Conexión a MySQL.
 *
 * En Railway se usan automáticamente las variables:
 * MYSQLHOST, MYSQLPORT, MYSQLUSER, MYSQLPASSWORD y MYSQLDATABASE.
 *
 * También se mantiene un valor local para poder ejecutar el proyecto
 * con XAMPP sin tener que cambiar este archivo.
 */

$host = getenv('MYSQLHOST') ?: 'localhost';
$port = (int)(getenv('MYSQLPORT') ?: 3306);
$db   = getenv('MYSQLDATABASE') ?: 'gamezone_store';
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: '';
$charset = 'utf8mb4';

try {
    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";

    $pdo = new PDO(
        $dsn,
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    // No mostramos usuario, contraseña ni otros datos sensibles.
    die('No se pudo conectar con MySQL. Verifica las variables de conexión de Railway.');
}
?>
