<?php
session_start();
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['rol'], ['admin', 'editor'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config.php';

// Validar ticket
if (!isset($_GET['ticket']) || empty($_GET['ticket'])) {
    echo "Ticket de trámite inválido.";
    exit;
}

$ticket = $_GET['ticket'];

// Obtener trámite
$stmt = $pdo->prepare("SELECT * FROM tramites WHERE ticket = :ticket LIMIT 1");
$stmt->execute([':ticket' => $ticket]);
$tramite = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tramite) {
    echo "Trámite no encontrado.";
    exit;
}

// Cambiar a revisado automáticamente si está en 'recibido'
if (!in_array($tramite['estado'], ['aprobado', 'rechazado'])) {
    $pdo->prepare("UPDATE tramites SET estado = 'en revision', fecha_actualizacion = NOW() WHERE ticket = :ticket")
        ->execute([':ticket' => $ticket]);
    $tramite['estado'] = 'en revision'; // actualizamos localmente también
}

// Procesar formulario (cambio de estado o comentario)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si se envió comentario
    if (!empty($_POST['comentario'])) {
        $nuevoEstado = 'comentado';
        $stmt = $pdo->prepare("UPDATE tramites SET comentario = :comentario, estado = :estado, fecha_actualizacion = NOW() WHERE ticket = :ticket");
        $stmt->execute([
            ':comentario' => $_POST['comentario'],
            ':estado' => $nuevoEstado,
            ':ticket' => $ticket
        ]);
        $tramite['comentario'] = $_POST['comentario'];
        $tramite['estado'] = $nuevoEstado;
    }

    if (in_array($_POST['estado'] ?? '', ['aprobado', 'rechazado'])) {
        $stmt = $pdo->prepare("UPDATE tramites SET estado = :estado, fecha_actualizacion = NOW() WHERE ticket = :ticket");
        $stmt->execute([
            ':estado' => $_POST['estado'],
            ':ticket' => $ticket
        ]);
        $tramite['estado'] = $_POST['estado']; // actualizar localmente
    }

    // Eliminar trámite (solo admin)
    if (isset($_POST['eliminar']) && $_SESSION['rol'] === 'admin') {
        $stmt = $pdo->prepare("DELETE FROM tramites WHERE ticket = :ticket");
        $stmt->execute([':ticket' => $ticket]);
        header("Location: ver_tramites.php?msg=eliminado");
        exit;
    }

    header("Location: tramite_detalle.php?ticket=$ticket");
    exit;
}

// Decodificar JSON
$datosAdicionales = json_decode($tramite['datos'], true);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <?php include __DIR__ . '/../includes/header.php'; ?>
  <title>Detalle del Trámite</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-4">
  <a href="panel_editor.php" class="btn btn-secondary btn-sm mb-3" style="width: 200px; height: 60px;">⬅ Volver</a>
  <h3>📄 Detalle del Trámite</h3>
  <hr>

  <div><strong>Ticket:</strong> <?= htmlspecialchars($tramite['ticket']) ?></div>
  <div><strong>Tipo:</strong> <?= ucfirst(htmlspecialchars($tramite['tipo_tramite'])) ?></div>
  <div><strong>Alumno:</strong> <?= htmlspecialchars($tramite['nombre_alumno']) ?></div>
  <div><strong>Número de Control:</strong> <?= htmlspecialchars($tramite['numero_control']) ?></div>
  <div><strong>Correo:</strong> <?= htmlspecialchars($tramite['correo_alumno']) ?></div>
  <div><strong>Estado:</strong> <?= ucfirst($tramite['estado']) ?></div>
  <div><strong>Enviado:</strong> <?= date('d/m/Y H:i', strtotime($tramite['fecha_envio'])) ?></div>
  <div><strong>Última Actualización:</strong> <?= $tramite['fecha_actualizacion'] ? date('d/m/Y H:i', strtotime($tramite['fecha_actualizacion'])) : '—' ?></div>

  <hr>
  <h5>🧾 Datos adicionales</h5>
  <?php if (is_array($datosAdicionales) && count($datosAdicionales) > 0): ?>
    <ul class="list-group">
      <?php foreach ($datosAdicionales as $clave => $valor): ?>
        <?php if (!empty($valor)): ?>
          <li class="list-group-item">
            <strong><?= ucwords(str_replace('_', ' ', $clave)) ?>:</strong>
            <?= nl2br(htmlspecialchars($valor)) ?>
          </li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <div class="alert alert-warning mt-3">No hay datos adicionales disponibles.</div>
  <?php endif; ?>

  <hr>
  <h5>📥 Documentos generados</h5>
  <?php
  $carpetaWeb = 'tramites_generados/';
  $ticket = $tramite['ticket'];
  $extensiones = ['pdf', 'docx'];
  $encontrados = [];

  foreach ($extensiones as $ext) {
      $archivo = $carpetaWeb . $ticket . '.' . $ext;
      if (file_exists(__DIR__ . '/' . $archivo)) {
          $encontrados[] = [
              'ruta' => $archivo,
              'tipo' => strtoupper($ext)
          ];
      }
  }
  ?>

  <?php if (!empty($encontrados)): ?>
    <ul class="list-group mb-3">
      <?php foreach ($encontrados as $doc): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          Documento <?= $doc['tipo'] ?>
          <a href="<?= htmlspecialchars($doc['ruta']) ?>" class="btn btn-sm btn-outline-success" style="width: 200px; height: 60px;" download>⬇ Descargar</a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <div class="alert alert-secondary">No hay documentos generados disponibles.</div>

  
    <hr>
  <?php endif; ?>

<h5>📎 Archivo del alumno</h5>
<?php 
// Ruta del archivo guardada en BD, por ejemplo: "uploads/TKT-68AC6462200DE_convalidacion.pdf"
$archivoBD = $tramite['archivo'] ?? '';

// Ruta en el servidor (para verificar existencia)
$rutaArchivoFS = __DIR__ . '/../public/' . $archivoBD;

// Ruta pública (para navegador)
$rutaArchivoWEB = '/public/' . $archivoBD;

if (!empty($archivoBD) && file_exists($rutaArchivoFS)): ?>
  <ul class="list-group mb-3">
    <li class="list-group-item d-flex justify-content-between align-items-center">
      Archivo enviado por el alumno
<a href="descargar_archivo.php?file=<?= urlencode(basename($archivoBD)) ?>" 
   class="btn btn-sm btn-outline-primary" style="width: 200px; height: 60px;">
   ⬇ Descargar
</a>
    </li>
  </ul>
<?php else: ?>
  <div class="alert alert-secondary">El alumno no subió ningún archivo.</div>
<?php endif; ?>


  <!-- Solo si no está aprobado o rechazado -->
  <?php if (!in_array($tramite['estado'], ['aprobado', 'rechazado'])): ?>
  <hr>
  <h5>📝 Acción administrativa</h5>
  <form method="post">
    <div class="mb-3">
      <label for="comentario" class="form-label">Comentario</label>
      <textarea name="comentario" id="comentario" class="form-control" rows="3"><?= htmlspecialchars($tramite['comentario'] ?? '') ?></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Cambiar estado a:</label><br>
      <button type="submit" name="estado" value="aprobado" class="btn btn-success btn-sm me-2">✅ Aprobar</button>
      <button type="submit" name="estado" value="rechazado" class="btn btn-danger btn-sm">❌ Rechazar</button>
    </div>

    <?php if ($_SESSION['rol'] === 'admin'): ?>
      <hr>
      <div class="mb-3">
        <button type="submit" name="eliminar" value="1" class="btn btn-outline-danger" onclick="return confirm('¿Estás seguro de eliminar este trámite? Esta acción no se puede deshacer.')">🗑 Eliminar trámite</button>
      </div>
    <?php endif; ?>
  </form>
  <?php endif; ?>

</div>
</body>
</html>

