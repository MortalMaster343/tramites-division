<?php
require_once __DIR__ . '/../config.php';

$stmt = $pdo->query("SELECT * FROM tramites ORDER BY fecha_envio DESC");
$tramites = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tipos disponibles
$tiposDisponibles = ['prorroga', 'equivalencia', 'convalidacion', 'traslado'];

// Estadísticas por tipo de trámite
$stats = $pdo->query("
    SELECT tipo_tramite,
           SUM(estado = 'aprobado') AS aprobados,
           SUM(estado = 'rechazado') AS rechazados,
           COUNT(*) AS total
    FROM tramites
    GROUP BY tipo_tramite
")->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas por carrera
$statsCarrera = $pdo->query("
    SELECT carrera_seleccionada,
           SUM(estado = 'aprobado') AS aprobados,
           SUM(estado = 'rechazado') AS rechazados,
           COUNT(*) AS total
    FROM tramites
    GROUP BY carrera_seleccionada
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<?php include __DIR__ . '/../includes/header.php'; ?>
<head>
  <meta charset="UTF-8">

  <title>Reportes Dinámicos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
</head>
<body class="container mt-4">
  <a href="panel_admin.php" class="btn btn-secondary mb-3" style="width: 200px; height: 60px;">⬅ Volver</a>
  <h3>📊 Reportes de Trámites</h3>

  <!-- Estadísticas por tipo -->
  <div class="mb-4">
    <h5>📁 Estadísticas por tipo de trámite</h5>
    <table class="table table-bordered table-sm">
      <thead class="table-light">
        <tr><th>Tipo</th><th>Aprobados</th><th>Rechazados</th><th>Total</th></tr>
      </thead>
      <tbody>
        <?php foreach ($stats as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['tipo_tramite']) ?></td>
            <td><?= $r['aprobados'] ?></td>
            <td><?= $r['rechazados'] ?></td>
            <td><?= $r['total'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Estadísticas por carrera -->
  <div class="mb-4">
    <h5>🎓 Estadísticas por carrera</h5>
    <table class="table table-bordered table-sm">
      <thead class="table-light">
        <tr><th>Carrera</th><th>Aprobados</th><th>Rechazados</th><th>Total</th></tr>
      </thead>
      <tbody>
        <?php foreach ($statsCarrera as $c): ?>
          <tr>
            <td><?= htmlspecialchars($c['carrera_seleccionada']) ?></td>
            <td><?= $c['aprobados'] ?></td>
            <td><?= $c['rechazados'] ?></td>
            <td><?= $c['total'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="mb-4">
  <h5>📅 Rango de Fechas</h5>
  <div class="row g-2">
    <div class="col-md-6">
      <label for="fecha_inicio" class="form-label">Desde:</label>
      <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control">
    </div>
    <div class="col-md-6">
      <label for="fecha_fin" class="form-label">Hasta:</label>
      <input type="date" id="fecha_fin" name="fecha_fin" class="form-control">
    </div>
  </div>
</div>

  <!-- Formulario de exportación -->
  <form action="generar_excel.php" method="post">
    <div class="mb-4">
      <h5>📝 Tipos de trámite a incluir</h5>
      <?php foreach ($tiposDisponibles as $tipo): ?>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="tipos[]" value="<?= $tipo ?>" id="tipo_<?= $tipo ?>">
          <label class="form-check-label" for="tipo_<?= $tipo ?>"><?= ucfirst($tipo) ?></label>
        </div>
      <?php endforeach; ?>
    </div>

<div class="mb-3">
  <h5>📌 Agrupar gráfica por</h5>
  <select name="agrupar_por" class="form-select w-auto">
    <option value="tipo_tramite">Tipo de trámite</option>
    <option value="carrera_seleccionada">Carrera</option>
  </select>
</div>


    <div class="mb-4">
      <p>El reporte incluirá automáticamente: número de fila, nombre del alumno, número de control, correo, tipo y estado del trámite.</p>
      <button type="submit" class="btn btn-success">📥 Generar Reporte Excel</button>
    </div>
  </form>
</body>
</html>