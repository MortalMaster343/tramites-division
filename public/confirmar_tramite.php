<?php
require_once '../config.php';

// Obtener ticket de la URL
$ticket = $_GET['ticket'] ?? null;

if (!$ticket) {
    die("Ticket inválido.");
}

// Buscar trámite
$stmt = $pdo->prepare("SELECT * FROM tramites WHERE ticket = :ticket");
$stmt->execute([':ticket' => $ticket]);
$tramite = $stmt->fetch();

if (!$tramite) {  
    die("No se encontró el trámite.");
}

// Rutas de los documentos
$tramitePDFAbsolutePath = __DIR__ . '/tramites_generados/' . $ticket . '.pdf';
$tramitePDFPublicPath   = 'tramites_generados/' . $ticket . '.pdf';

$constanciaPDFAbsolutePath = __DIR__ . '/tramites_generados/CONSTANCIA_' . $ticket . '.pdf';
$constanciaPDFPublicPath   = 'tramites_generados/CONSTANCIA_' . $ticket . '.pdf';

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Trámite Registrado</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <style>
    body { background-color: #f8f9fa; font-size: 1rem; }
    .ticket-box {
      max-width: 650px;
      margin: 80px auto;
      padding: 35px;
      border-radius: 15px;
      background-color: #ffffff;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
      text-align: center;
    }
    .ticket-code {
      font-size: 2rem;
      color: #007bff;
      font-weight: bold;
      margin: 10px 0 20px;
    }
    .document-buttons a {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      margin-bottom: 12px;
      width: 100%;
    }
    @media (min-width: 768px) {
      .document-buttons a { width: auto; }
    }
  </style>
</head>
<body>
  <div class="ticket-box">
    <h3>✅ Trámite registrado correctamente</h3>
    <p>Tu número de seguimiento es:</p>

    <div class="ticket-code">
      <?= htmlspecialchars($ticket) ?>
    </div>

    <p class="mb-4">Se ha enviado una confirmación a tu correo electrónico.</p>

    <div class="document-buttons d-flex flex-column flex-md-row justify-content-center gap-3">
      <!-- Botón para descargar el trámite PDF -->
      <?php if (file_exists($tramitePDFAbsolutePath)): ?>
        <a href="<?= htmlspecialchars($tramitePDFPublicPath) ?>" class="btn btn-outline-success" download>
          <i class="fa-solid fa-file-pdf"></i> Descargar Trámite
        </a>
      <?php else: ?>
        <button class="btn btn-outline-secondary" disabled>
          <i class="fa-solid fa-ban"></i> Trámite no disponible
        </button>
      <?php endif; ?>

      <!-- Botón para descargar la constancia PDF -->
      <?php if (file_exists($constanciaPDFAbsolutePath)): ?>
        <a href="<?= htmlspecialchars($constanciaPDFPublicPath) ?>" class="btn btn-outline-primary" download>
          <i class="fa-solid fa-file-pdf"></i> Descargar Constancia
        </a>
      <?php else: ?>
        <button class="btn btn-outline-secondary" disabled>
          <i class="fa-solid fa-ban"></i> Constancia no disponible
        </button>
      <?php endif; ?>
    </div>

    <hr class="my-4">

    <!-- Botón para regresar -->
    <a href="index.php" class="btn btn-secondary w-100">
      <i class="fa-solid fa-arrow-left"></i> Regresar al inicio
    </a>
  </div>
</body>
</html>
