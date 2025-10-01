<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'editor') {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config.php';

// =========================
// Filtros
// =========================
$where = [];
$params = [];

// Filtro tipo trámite
if (!empty($_GET['tipo'])) {
    $where[] = 'tipo_tramite = :tipo';
    $params[':tipo'] = $_GET['tipo'];
}

// Filtro estado
if (!empty($_GET['estado'])) {
    $where[] = 'estado = :estado';
    $params[':estado'] = $_GET['estado'];
}

// Filtro fechas
if (!empty($_GET['fecha_desde'])) {
    $where[] = 'fecha_envio >= :desde';
    $params[':desde'] = $_GET['fecha_desde'] . ' 00:00:00';
}
if (!empty($_GET['fecha_hasta'])) {
    $where[] = 'fecha_envio <= :hasta';
    $params[':hasta'] = $_GET['fecha_hasta'] . ' 23:59:59';
}

// 🔍 Búsqueda por coincidencia de caracteres
if (!empty($_GET['buscar'])) {
    $where[] = '(ticket LIKE :buscar OR nombre_alumno LIKE :buscar OR numero_control LIKE :buscar OR correo_alumno LIKE :buscar)';
    $params[':buscar'] = "%" . $_GET['buscar'] . "%";
}

// =========================
// Paginación
// =========================
$porPagina = 25;
$pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $porPagina;

// Contar total de registros
$sqlCount = "SELECT COUNT(*) FROM tramites";
if ($where) {
    $sqlCount .= " WHERE " . implode(' AND ', $where);
}
$stmtCount = $pdo->prepare($sqlCount);
$stmtCount->execute($params);
$totalRegistros = $stmtCount->fetchColumn();
$totalPaginas = ceil($totalRegistros / $porPagina);

// Obtener registros con límite
$sql = "SELECT * FROM tramites";
if ($where) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY fecha_envio DESC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':limit', $porPagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$tramites = $stmt->fetchAll(PDO::FETCH_ASSOC);

function diasTranscurridos($fecha) {
    $enviado = new DateTime($fecha);
    $hoy = new DateTime();
    return $enviado->diff($hoy)->days;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <?php include __DIR__ . '/../includes/header.php'; ?>
  <title>Ver Trámites</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" />
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-2">
  <a href="logout.php" class="btn btn-danger btn-bg float-end" style="width: 200px; height: 50px;">Cerrar sesión</a>
    <h3>📄 Panel de Trámites</h3>
</div>

<div class="container mt-4">
  <!-- 🔍 Filtros -->
  <form class="row g-3 mb-4" method="get">
    <div class="col-md-3">
      <select name="tipo" class="form-select">
        <option value="">📝 Todos los tipos</option>
        <option value="prorroga" <?= ($_GET['tipo'] ?? '') === 'prorroga' ? 'selected' : '' ?>>Prórroga</option>
        <option value="equivalencia" <?= ($_GET['tipo'] ?? '') === 'equivalencia' ? 'selected' : '' ?>>Equivalencia</option>
        <option value="convalidacion" <?= ($_GET['tipo'] ?? '') === 'convalidacion' ? 'selected' : '' ?>>Convalidación</option>
        <option value="traslado" <?= ($_GET['tipo'] ?? '') === 'traslado' ? 'selected' : '' ?>>Traslado</option>
      </select>
    </div>
    <div class="col-md-2">
      <select name="estado" class="form-select">
        <option value="">📌 Todos los estados</option>
        <option value="en revision" <?= ($_GET['estado'] ?? '') === 'en revision' ? 'selected' : '' ?>>En Revisión</option>
        <option value="aprobado" <?= ($_GET['estado'] ?? '') === 'aprobado' ? 'selected' : '' ?>>Aprobado</option>
        <option value="rechazado" <?= ($_GET['estado'] ?? '') === 'rechazado' ? 'selected' : '' ?>>Rechazado</option>
      </select>
    </div>
    <div class="col-md-2">
      <input type="date" name="fecha_desde" class="form-control" value="<?= htmlspecialchars($_GET['fecha_desde'] ?? '') ?>">
    </div>
    <div class="col-md-2">
      <input type="date" name="fecha_hasta" class="form-control" value="<?= htmlspecialchars($_GET['fecha_hasta'] ?? '') ?>">
    </div>
    <div class="col-md-3">
      <input type="text" name="buscar" class="form-control" placeholder="🔍 Buscar..." value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>">
    </div>
    <div class="col-md-12 d-grid">
      <button type="submit" class="btn btn-primary">Aplicar filtros</button>
    </div>
  </form>

<?php if ($totalRegistros == 0): ?>
  <div class="alert alert-info">No se encontraron trámites con los filtros seleccionados.</div>
<?php else: ?>
<div class="d-flex justify-content-center">
  <table class="table table-striped table-bordered" style="width: auto; min-width: 800px;">
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
        <td><a href="tramite_detalleE.php?ticket=<?= urlencode($t['ticket']) ?>" class="btn btn-sm btn-primary">Ver</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>


    <!-- 📌 Navegación de páginas -->
    <nav>
      <ul class="pagination">
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
