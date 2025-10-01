<?php
// public/ver_tramite.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Models/Tramite.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket = $_POST['ticket'] ?? '';

    if (!$ticket) {
        die("Debes proporcionar un número de ticket.");
    }

    $tramiteModel = new Tramite($pdo);
    $tramite = $tramiteModel->obtenerPorTicket($ticket);

    if (!$tramite) {
        die("No se encontró trámite con ese número de ticket.");
    }

    // Rutas a archivos generados
    $docxFile = "tramites_generados/{$ticket}.docx";
    $pdfFile  = "tramites_generados/{$ticket}.pdf";
} else {
    die("Método no permitido.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Consulta de Trámite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" />
    <style>
        body { font-size: 1rem; }
        .document-buttons a {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 10px;
            width: 100%;
        }
        @media (min-width: 768px) {
            .document-buttons a { width: auto; }
        }
    </style>
</head>

<body class="bg-light">
<div class="container mt-4">
    <a href="index.php" class="btn btn-secondary mb-3"><i class="fa-solid fa-arrow-left"></i> Regresar</a>

    <div class="card shadow-sm">
        <div class="card-header text-center">
            <h4 class="mb-0">Detalle del Trámite</h4>
        </div>
        <div class="card-body">
            <p><strong>Ticket:</strong> <?= htmlspecialchars($tramite['ticket']) ?></p>
            <p><strong>Tipo de trámite:</strong> <?= htmlspecialchars($tramite['tipo_tramite']) ?></p>
            <p><strong>Nombre:</strong> <?= htmlspecialchars($tramite['nombre_alumno']) ?></p>
            <p><strong>Número de Control:</strong> <?= htmlspecialchars($tramite['numero_control']) ?></p>
            <p><strong>Correo:</strong> <?= htmlspecialchars($tramite['correo_alumno']) ?></p>
            <p><strong>Estado:</strong> <?= htmlspecialchars($tramite['estado'] ?? 'Recibido') ?></p>

            <hr />
            <h5 class="text-center mb-3">📂 Documentos</h5>
            <div class="document-buttons d-flex flex-column flex-md-row justify-content-center gap-3">
                <?php if (file_exists($docxFile)): ?>
                    <a href="<?= htmlspecialchars($docxFile) ?>" download class="btn btn-outline-primary">
                        <i class="fa-solid fa-file-word"></i> Descargar Word
                    </a>
                <?php else: ?>
                    <button class="btn btn-outline-secondary" disabled><i class="fa-solid fa-ban"></i> Word no disponible</button>
                <?php endif; ?>

                <?php if (file_exists($pdfFile)): ?>
                    <a href="<?= htmlspecialchars($pdfFile) ?>" download class="btn btn-outline-danger">
                        <i class="fa-solid fa-file-pdf"></i> Descargar PDF
                    </a>
                <?php else: ?>
                    <button class="btn btn-outline-secondary" disabled><i class="fa-solid fa-ban"></i> PDF no disponible</button>
                <?php endif; ?>
            </div>

            <hr />
            <h5 class="text-center mb-3">📝 Comentarios</h5>
            <?php if (empty($tramite['comentario'])): ?>
                <p class="text-muted text-center"><em>No hay comentarios para este trámite aún.</em></p>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    <?= nl2br(htmlspecialchars($tramite['comentario'])) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <form action="consulta_tramite.php" method="POST" class="mt-4 text-center">
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-magnifying-glass"></i> Consultar otro trámite
        </button>
    </form>
</div>
</body>
</html>
