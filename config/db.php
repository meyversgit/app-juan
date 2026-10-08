<?php
declare(strict_types=1);
$host='localhost'; $db='gamezone_store'; $user='root'; $pass=''; $charset='utf8mb4';
try { $pdo=new PDO("mysql:host=$host;dbname=$db;charset=$charset",$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]); }
catch(PDOException $e){ die('No se pudo conectar con MySQL. Verifica XAMPP y que exista la base de datos gamezone_store.'); }
?>
