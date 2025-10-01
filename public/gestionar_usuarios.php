<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config.php';

// Crear usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_usuario'])) {
    $usuario = trim($_POST['usuario']);
    $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];

    $stmt = $pdo->prepare("INSERT INTO usuarios (usuario, clave, rol) VALUES (?, ?, ?)");
    $stmt->execute([$usuario, $clave, $rol]);
    header("Location: gestionar_usuarios.php");
    exit;
}

// Eliminar usuario
if (isset($_GET['eliminar']) && $_GET['eliminar'] !== $_SESSION['usuario']) {
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE usuario = ?");
    $stmt->execute([$_GET['eliminar']]);
    header("Location: gestionar_usuarios.php");
    exit;
}

// Obtener todos los usuarios
$stmt = $pdo->query("SELECT * FROM usuarios ORDER BY creado_en DESC");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
<?php include __DIR__ . '/../includes/header.php'; ?>
  <title>Gestionar Usuarios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-4">
  <h3>👥 Gestión de Usuarios</h3>
  <a href="panel_admin.php" class="btn btn-secondary btn-sm mb-3" style="width: 200px; height: 60px;">⬅ Volver al panel</a>

  <form method="POST" class="card p-3 mb-4">
    <h5>➕ Crear nuevo usuario</h5>
    <div class="row g-2">
      <div class="col-md-4">
        <input type="text" name="usuario" class="form-control" placeholder="Nombre de usuario" required>
      </div>
      <div class="col-md-4">
        <input type="password" name="clave" class="form-control" placeholder="Contraseña" required>
      </div>
      <div class="col-md-3">
        <select name="rol" class="form-select" required>
          <option value="editor">Editor</option>
          <option value="admin">Administrador</option>
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" name="nuevo_usuario" class="btn btn-primary w-200">Crear</button>
      </div>
    </div>
  </form>

  <table class="table table-bordered table-hover">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Usuario</th>
        <th>Rol</th>
        <th>Creado en</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($usuarios as $u): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td><?= htmlspecialchars($u['usuario']) ?></td>
          <td><?= $u['rol'] ?></td>
          <td><?= $u['creado_en'] ?></td>
          <td>
            <?php if ($u['usuario'] !== $_SESSION['usuario']): ?>
              <a href="?eliminar=<?= urlencode($u['usuario']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
            <?php else: ?>
              <span class="text-muted">Sesión actual</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
