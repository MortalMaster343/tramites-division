<?php
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../config.php'; // Conexión PDO
use PhpOffice\PhpWord\TemplateProcessor;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $numero_control = $_POST['numero_control'];

    // 📂 Carpeta base absoluta (segura)
    $baseDir = __DIR__ . "/";
    $expedienteDir = $baseDir . $numero_control . "/";

    // 🚫 Evitar duplicados en carpeta
    if (is_dir($expedienteDir)) {
        die("⚠️ Ya existe un expediente con este número de control.");
    }
    mkdir($expedienteDir, 0777, true);

    // Guardar archivos subidos
    $archivos = ['no_adeudo','acta','certificado','curp','fotos'];
    foreach ($archivos as $campo) {
        if (isset($_FILES[$campo]) && $_FILES[$campo]['error'] == 0) {
            $destino = $expedienteDir . basename($_FILES[$campo]['name']);
            move_uploaded_file($_FILES[$campo]['tmp_name'], $destino);
        }
    }

    // Guardar firma temporal
    $firmaPath = null;
    if (!empty($_POST['firma'])) {
        $firmaData = str_replace('data:image/png;base64,', '', $_POST['firma']);
        $firmaData = str_replace(' ', '+', $firmaData);
        $firmaBin = base64_decode($firmaData);
        $firmaPath = $expedienteDir . "firma.png";
        file_put_contents($firmaPath, $firmaBin);
    }

    // 📂 Carpeta de plantillas (segura)
    $plantillaDir = $baseDir . "Plantillas/";

    // Generar documentos desde plantillas
    $plantillas = [
        "SOLICITUD_DE_ACTO_RECEPCIONAL.docx" => "Solicitud_{$numero_control}.docx",
        "HOJA_DE_DATOS.docx" => "HojaDatos_{$numero_control}.docx",
        "CONSTANCIA_NO_ADEUDO.docx" => "NoAdeudo_{$numero_control}.docx"
    ];

    foreach ($plantillas as $plantilla => $salida) {
        $plantillaPath = $plantillaDir . $plantilla;

        if (!file_exists($plantillaPath)) {
            die("❌ No se encontró la plantilla: $plantillaPath");
        }

        $template = new TemplateProcessor($plantillaPath);

        // Rellenar valores
        foreach ($_POST as $campo => $valor) {
            if ($campo !== "firma") {
                $template->setValue($campo, $valor);
            }
        }

        // Insertar firma si existe
        if ($firmaPath) {
            $template->setImageValue('firma', [
                'path' => $firmaPath,
                'width' => 120,
                'height' => 60
            ]);
        }

        // Guardar documento generado
        $template->saveAs($expedienteDir . $salida);
    }

    // Eliminar firma temporal
    if ($firmaPath && file_exists($firmaPath)) unlink($firmaPath);

    // Guardar datos en JSON
    $jsonPath = $expedienteDir . "datos.json";
    file_put_contents($jsonPath, json_encode($_POST, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // 🚫 Evitar duplicados en la BD
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tramites WHERE numero_control = ? AND tipo = 'titulacion'");
    $stmt->execute([$numero_control]);
    if ($stmt->fetchColumn() > 0) {
        die("⚠️ Ya existe un trámite de titulación para este alumno.");
    }

    // Generar ticket único
    $ticket = uniqid("TIT-", true);

    // Insertar en BD
    $stmt = $pdo->prepare("
        INSERT INTO tramites (ticket, tipo, numero_control, datos, carpeta, estado, creado_en)
        VALUES (:ticket, :tipo, :numero_control, :datos, :carpeta, :estado, NOW())
    ");
    $stmt->execute([
        ':ticket' => $ticket,
        ':tipo' => 'titulacion',
        ':numero_control' => $numero_control,
        ':datos' => json_encode($_POST, JSON_UNESCAPED_UNICODE),
        ':carpeta' => "public/Titulacion/$numero_control/",
        ':estado' => 'enviado'
    ]);

    echo "✅ Trámite registrado correctamente.<br>";
    echo "📂 Expediente: $numero_control<br>";
    echo "🎫 Ticket: $ticket<br>";
    echo "<a href='index.php'>Regresar al inicio</a>";
}
