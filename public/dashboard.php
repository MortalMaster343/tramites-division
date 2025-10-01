<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$rol = $_SESSION['rol'];

if ($rol === 'admin') {
    header('Location: panel_admin.php');
} elseif ($rol === 'editor') {
    header('Location: ver_tramites.php');
} else {
    echo "Rol no reconocido.";
}
exit;
