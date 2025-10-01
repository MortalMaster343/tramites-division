<?php
session_start();
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['rol'], ['admin', 'editor'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Models/Tramite.php';


// Instancia del modelo
$tramiteModel = new Tramite($pdo);

// Parámetros de filtros y paginación
$filtros = [
    'tipo' => $_GET['tipo'] ?? null,
    'estado' => $_GET['estado'] ?? null,
    'fecha_desde' => $_GET['fecha_desde'] ?? null,
    'fecha_hasta' => $_GET['fecha_hasta'] ?? null,
    'buscar' => $_GET['buscar'] ?? null
];

$porPagina = 25;
$pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $porPagina;

// Obtener trámites
$tramites = $tramiteModel->listar($filtros, $porPagina, $offset);

// Función para calcular días transcurridos
function diasTranscurridos($fecha) {
    $enviado = new DateTime($fecha);
    $hoy = new DateTime();
    return $enviado->diff($hoy)->days;
}
$esadmin = ($_SESSION['rol'] === 'admin');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include __DIR__ . '/../includes/header.php'; ?>
  <meta charset="UTF-8">
  <title>Panel de Trámites</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" />
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-4 d-flex justify-content-end">
    <?php if ($esadmin): ?>
        <a href="panel_admin.php" class="btn btn-secondary btn-sm me-2" style="width:200px;height:60px;">
            ⬅ Volver al Panel
        </a>
    <?php else: ?>
        <a href="logout.php" class="btn btn-outline-danger btn-sm me-2" style="width:200px;height:60px;">
            🔒 Cerrar sesión
        </a>
    <?php endif; ?>
</div>

<h3 class="mt-3 text-center">📄 Panel de Trámites</h3>

<div class="container mt-4">
  <!-- Filtros -->
  <form class="row g-3 mb-4" method="get">
    <div class="col-md-3">
      <select name="tipo" class="form-select">
        <option value="">📝 Todos los tipos</option>
        <option value="prorroga" <?= ($filtros['tipo'] === 'prorroga') ? 'selected' : '' ?>>Prórroga</option>
        <option value="equivalencia" <?= ($filtros['tipo'] === 'equivalencia') ? 'selected' : '' ?>>Equivalencia</option>
        <option value="convalidacion" <?= ($filtros['tipo'] === 'convalidacion') ? 'selected' : '' ?>>Convalidación</option>
        <option value="traslado" <?= ($filtros['tipo'] === 'traslado') ? 'selected' : '' ?>>Traslado</option>
        <option value="activacion" <?= ($filtros['tipo'] === 'activacion') ? 'selected' : '' ?>>Activación</option>
      </select>
    </div>
    <div class="col-md-2">
      <select name="estado" class="form-select">
        <option value="">📌 Todos los estados</option>
        <option value="en revision" <?= ($filtros['estado'] === 'en revision') ? 'selected' : '' ?>>En Revisión</option>
        <option value="aprobado" <?= ($filtros['estado'] === 'aprobado') ? 'selected' : '' ?>>Aprobado</option>
        <option value="rechazado" <?= ($filtros['estado'] === 'rechazado') ? 'selected' : '' ?>>Rechazado</option>
        <option value="pendiente" <?= ($filtros['estado'] === 'pendiente') ? 'selected' : '' ?>>Pendiente</option>
      </select>
    </div>
    <div class="col-md-2">
      <input type="date" name="fecha_desde" class="form-control" value="<?= htmlspecialchars($filtros['fecha_desde'] ?? '') ?>">
    </div>
    <div class="col-md-2">
      <input type="date" name="fecha_hasta" class="form-control" value="<?= htmlspecialchars($filtros['fecha_hasta'] ?? '') ?>">
    </div>
    <div class="col-md-3">
      <input type="text" name="buscar" class="form-control" placeholder="🔍 Buscar..." value="<?= htmlspecialchars($filtros['buscar'] ?? '') ?>">
    </div>
    <div class="col-md-12 d-grid justify-content-center">
      <button type="submit" class="btn btn-primary" style="width: 200px; height: 60px; align-self: center;">Aplicar filtros</button>
    </div>
  </form>

<?php if (empty($tramites)): ?>
  <div class="alert alert-info">No se encontraron trámites con los filtros seleccionados.</div>
<?php else: ?>
<div class="d-flex justify-content-center">
  <table class="table table-striped table-bordered" style="min-width: 800px;">
    <thead class="table-light">
      <tr>
        <th>Ticket</th>
        <th>Tipo</th>
        <th>Nombre</th>
        <th>Núm. Control</th>
        <th>Correo</th>
        <th>Días</th>
        <th>Estado</th>
        <th>Enviado</th>
        <th>Actualizado</th>
        <th>Archivo</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($tramites as $t): 
        $dias = diasTranscurridos($t['fecha_envio']);
        if (in_array($t['estado'], ['aprobado', 'rechazado'])) {
            $badgeClass = 'bg-dark';
        } elseif ($dias >= 7) {
            $badgeClass = 'bg-danger';
        } elseif ($dias >= 3) {
            $badgeClass = 'bg-warning text-dark';
        } else {
            $badgeClass = 'bg-success';
        }
      ?>
      <tr>
        <td><?= htmlspecialchars($t['ticket']) ?></td>
        <td><?= htmlspecialchars($t['tipo_tramite']) ?></td>
        <td><?= htmlspecialchars($t['nombre_alumno']) ?></td>
        <td><?= htmlspecialchars($t['numero_control']) ?></td>
        <td><?= htmlspecialchars($t['correo_alumno']) ?></td>
        <td><span class="badge <?= $badgeClass ?>"><?= $dias ?> día<?= $dias !== 1 ? 's' : '' ?></span></td>
        <td><?= ucfirst($t['estado']) ?></td>
        <td><?= date('d/m/Y H:i', strtotime($t['fecha_envio'])) ?></td>
        <td><?= $t['fecha_actualizacion'] ? date('d/m/Y H:i', strtotime($t['fecha_actualizacion'])) : '-' ?></td>
        <td>
          <?php if (!empty($t['archivo'])): ?>
            <a href="<?= htmlspecialchars($t['archivo']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">Ver archivo</a>
          <?php else: ?>
            <span class="text-muted">-</span>
          <?php endif; ?>
        </td>
        <td><a href="tramite_detalle.php?ticket=<?= urlencode($t['ticket']) ?>" class="btn btn-sm btn-primary">Ver</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Paginación -->
<?php
$totalRegistros = count($tramites); // si quieres contar todos, hazlo desde el modelo
$totalPaginas = ceil($totalRegistros / $porPagina);
?>
<nav>
  <ul class="pagination justify-content-center mt-3">
    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
      <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>
  </ul>
</nav>

<?php endif; ?>
</div>
</body>
</html>
