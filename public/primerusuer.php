<?php
require_once __DIR__ . '/../config.php';

$usuario = 'admin';
$clave = password_hash('admin123', PASSWORD_DEFAULT);
$rol = 'admin';

$stmt = $pdo->prepare("INSERT INTO usuarios (usuario, clave, rol) VALUES (?, ?, ?)");
$stmt->execute([$usuario, $clave, $rol]);

echo "Usuario creado correctamente: $usuario / admin123 (rol: $rol)";
