<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<?php include __DIR__ . '/../includes/header.php'; ?>

  <meta charset="UTF-8">
  <title>Panel de Administrador</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-4">
  <a href="logout.php" class="btn btn-danger btn-bg float-end" style="width: 200px; height: 50px;">Cerrar sesión</a>
  <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?> (Administrador)</h2>
  <hr>

  <div class="list-group">
    <a href="gestionar_usuarios.php" class="list-group-item list-group-item-action">👥 Gestionar usuarios</a>
    <a href="ver_tramites.php" class="list-group-item list-group-item-action">📄 Ver trámites</a>
    <a href="reportes.php" class="list-group-item list-group-item-action">📄 Ver reportes</a>
  </div>
</div>
</body>
</html>
