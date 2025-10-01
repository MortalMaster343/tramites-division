<?php
session_start();
require_once __DIR__ . '/../config.php';

$usuario = $_POST['usuario'] ?? '';
$clave = $_POST['clave'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
$stmt->execute([$usuario]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($clave, $user['clave'])) {
    $_SESSION['usuario'] = $user['usuario'];
    $_SESSION['rol'] = $user['rol'];
    header("Location: dashboard.php");
    exit;
} else {
    $_SESSION['login_error'] = "Usuario o contraseña incorrectos.";
    header("Location: login.php");
    exit;
}
