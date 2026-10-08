<?php
require_once 'config/auth.php';
if (logueado() && !correoVerificado()) { header('Location: login.php?verify=1'); exit; }
header('Location: login.php'); exit;
?>
