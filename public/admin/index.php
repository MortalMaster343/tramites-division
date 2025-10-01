<?php
session_start();
if (!isset($_SESSION['usuario']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'editor')) {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Administración</title>
  <link href="../assets/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
  <h2>Bienvenido, <?= htmlspecialchars($_SESSION['usuario']) ?> (<?= $_SESSION['rol'] ?>)</h2>
  <p>Este es el panel de administración.</p>
  <a href="usuarios.php" class="btn btn-primary">Gestionar usuarios</a>
  <a href="../logout.php" class="btn btn-danger">Cerrar sesión</a>
</body>
</html>
