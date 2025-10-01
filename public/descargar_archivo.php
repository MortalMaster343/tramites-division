<?php
session_start();
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['rol'], ['admin', 'editor'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config.php';

if (!isset($_GET['file']) || empty($_GET['file'])) {
    die("Archivo no especificado.");
}

// Solo tomar el nombre del archivo, evitar rutas maliciosas
$archivo = basename($_GET['file']);
$ruta = __DIR__ . '/../public/uploads/' . $archivo;

if (!file_exists($ruta)) {
    die("El archivo no existe.");
}

// Forzar descarga
header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"$archivo\"");
header("Expires: 0");
header("Cache-Control: must-revalidate");
header("Pragma: public");
header("Content-Length: " . filesize($ruta));
readfile($ruta);
exit;
