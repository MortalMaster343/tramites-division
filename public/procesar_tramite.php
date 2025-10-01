<?php
require_once __DIR__ . '/../Models/Tramite.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
$tramiteModel = new Tramite($pdo);


// Zona horaria
date_default_timezone_set('America/Mexico_City');



// 1. Datos del formulario
$nombre   = $_POST['nombre_alumno'] ?? '';
$numero_control = $_POST['numero_control'] ?? '';
$carrera  = $_POST['carrera_seleccionada'] ?? '';
$correo   = $_POST['correo_alumno'] ?? '';
$tipo_tramite = $_POST['tipo_tramite'] ?? '';
$ticket   = strtoupper(uniqid('TKT-'));
$fecha_envio = date('Y-m-d H:i:s');
$estado   = 'pendiente';

// Firma en base64
$firmaBase64 = $_POST['firma'] ?? null;
// Guardar firma temporal
    $firmaPath = null;
    if (!empty($_POST['firma'])) {
        $firmaData = str_replace('data:image/png;base64,', '', $_POST['firma']);
        $firmaData = str_replace(' ', '+', $firmaData);
        $firmaBin = base64_decode($firmaData);
    // Guardar en carpeta temporal
    $firmaPath = sys_get_temp_dir() . "/firma_" . uniqid() . ".png";
    file_put_contents($firmaPath, $firmaBin);
}

// Fechas legibles
$fechaDia = date('d');
$formatter = new IntlDateFormatter('es_ES', IntlDateFormatter::LONG, IntlDateFormatter::NONE, null, null, 'MMMM');
$fechamesnombre = $formatter->format(new DateTime());

// 2. Plantillas disponibles
$plantillas = [
    'prorroga'      => 'plantillas/prorroga.docx',
    'equivalencia'  => 'plantillas/equivalencia.docx',
    'convalidacion' => 'plantillas/convalidacion.docx',
    'traslado'      => 'plantillas/traslado.docx',
    'activacion'    => 'plantillas/ACTIVACION.docx'
];
if (!isset($plantillas[$tipo_tramite])) {
    die("Tipo de trámite inválido.");
}

// 3. Generar documento Word del trámite
$plantilla = new TemplateProcessor($plantillas[$tipo_tramite]);

switch ($tipo_tramite) {
    case 'prorroga':
        $plantilla->setValue('fechaDia', $fechaDia);
        $plantilla->setValue('fechamesnombre', $fechamesnombre);
        $plantilla->setValue('nombre', $nombre);
        $plantilla->setValue('semestreA', $_POST['semestreA']);
        $plantilla->setValue('numero_control', $numero_control);
        $plantilla->setValue('carrera_seleccionada', $carrera);
        $plantilla->setValue('semestre_solicitado', $_POST['semestre_solicitado']);
        $plantilla->setValue('materias_faltantes', $_POST['materias_faltantes']);
        $plantilla->setValue('razon', $_POST['razon']);
        $plantilla->setValue('telefono', $_POST['telefono']);
        $plantilla->setValue('correo', $correo);

        
        break;

    case 'equivalencia':
        $campos = [
            'fecha', 'apellido_paterno', 'apellido_materno', 'nombres', 'domicilio', 'colonia',
            'codigo_postal', 'estado', 'ciudad', 'municipio', 'telefonos', 'curp', 'nacionalidad',
            'genero', 'instituto_prev', 'nivel', 'area', 'estado_prev', 'carrera_prev', 'plan_prev',
            'fecha1', 'fecha2', 'instituto_ingresar', 'estado_nuevo', 'ingenieria', 'plan_ingenieria',
            'licenciatura', 'plan_lic'
        ];
        foreach ($campos as $c) $plantilla->setValue($c, $_POST[$c]);

        break;

    case 'convalidacion':
        $campos = ['fecha','instituto_procedencia','numero_control','semestre','carrera_cursada',
                   'plan_estudios_cursado','carrera_solicitada','plan_estudios_solicitado'];
        foreach ($campos as $c) $plantilla->setValue($c, $_POST[$c]);
        $plantilla->setValue('nombre', $nombre);

        break;

    case 'traslado':
        $campos = ['dia','mes','semestre_actual','numero_control','carrera','plan_estudios',
                   'modalidad','instituto_destino','carrera_destino','plan_destino','motivo'];
        foreach ($campos as $c) $plantilla->setValue($c, $_POST[$c]);
        $plantilla->setValue('nombre', $nombre);

        break;

    case 'activacion':
        $campos = ['ultimo_semestre','periodo_anterior','semestreA'];
        foreach ($campos as $c) $plantilla->setValue($c, $_POST[$c]);
        $plantilla->setValue('carrera_seleccionada', $carrera);
        $plantilla->setValue('nombre', $nombre);
        $plantilla->setValue('numero_control', $numero_control);
        $plantilla->setValue('fechaDia', $fechaDia);
        $plantilla->setValue('fechamesnombre', $fechamesnombre);
        $plantilla->setValue('correo', $correo);
        $plantilla->setValue('telefono', $_POST['telefono']);

        break;
    }

        // Insertar firma en todas las plantillas si existe marcador ${firma}
if ($firmaPath && file_exists($firmaPath)) {
    try {
        $plantilla->setImageValue('firma', [
            'path'   => $firmaPath,
            'width'  => 120,
            'height' => 60,
            'ratio'  => true
        ]);
    } catch (Exception $e) {
        error_log("No se pudo insertar firma: " . $e->getMessage());
    }
}

// Guardar trámite Word
$docPath = "tramites_generados/$ticket.docx";
$plantilla->saveAs($docPath);

// 4. Convertir a PDF (LibreOffice)
$sofficePath = "C:\\Program Files\\LibreOffice\\program\\soffice.exe";
$docxPath = realpath($docPath);
$pdfPath = str_replace('.docx', '.pdf', $docxPath);
$command = "\"$sofficePath\" --headless --convert-to pdf --outdir \"" . dirname($pdfPath) . "\" \"$docxPath\"";
exec($command);

// 5. Subida de archivos adjuntos (solo algunos trámites)
$rutaArchivoSubido = null;
if ($tipo_tramite === 'convalidacion' && isset($_FILES['archivo_convalidacion'])) {
    if ($_FILES['archivo_convalidacion']['error'] === UPLOAD_ERR_OK) {
        $dir = __DIR__ . "/uploads/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $ext = pathinfo($_FILES['archivo_convalidacion']['name'], PATHINFO_EXTENSION);
        $nombreArchivo = $ticket . "_Kardex." . strtolower($ext);
        $destino = $dir . $nombreArchivo;
        if (move_uploaded_file($_FILES['archivo_convalidacion']['tmp_name'], $destino)) {
            $rutaArchivoSubido = "uploads/" . $nombreArchivo;
        }
    }
}
if ($tipo_tramite === 'traslado' && isset($_FILES['archivo_translado'])) {
    if ($_FILES['archivo_translado']['error'] === UPLOAD_ERR_OK) {
        $dir = __DIR__ . "/uploads/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $ext = pathinfo($_FILES['archivo_translado']['name'], PATHINFO_EXTENSION);
        $nombreArchivo = $ticket . "_CartaNoAdeudo." . strtolower($ext);
        $destino = $dir . $nombreArchivo;
        if (move_uploaded_file($_FILES['archivo_translado']['tmp_name'], $destino)) {
            $rutaArchivoSubido = "uploads/" . $nombreArchivo;
        }
    }
}

// 6. Guardar en BD
$datosParaGuardar = [
    'ticket'          => $ticket,
    'tipo_tramite'    => $tipo_tramite,
    'nombre_alumno'   => $nombre,
    'numero_control'  => $numero_control,
    'correo_alumno'   => $correo,
    'carrera_seleccionada' => $carrera,
    'datos_json'      => json_encode($_POST, JSON_UNESCAPED_UNICODE),
    'fecha_envio'     => $fecha_envio,
    'estado'          => $estado,
    'archivo'         => $rutaArchivoSubido
];

$tramiteModel->guardar($datosParaGuardar);

// 7. Generar constancia Word + PDF para el alumno
$constanciaDocPath = "tramites_generados/CONSTANCIA_$ticket.docx";
$word = new PhpWord();
$section = $word->addSection(['marginTop' => 600,'marginBottom' => 600,'marginLeft' => 800,'marginRight' => 800]);
$tituloStyle = ['bold' => true, 'size' => 18, 'color' => '1F4E79'];
$subtituloStyle = ['size' => 12, 'color' => '333333'];
$ticketStyle = ['bold' => true, 'size' => 24, 'color' => '0078D7'];
$section->addText('CONSTANCIA DE REGISTRO DE TRÁMITE', $tituloStyle, ['alignment' => Jc::CENTER]);
$section->addTextBreak(1);
$section->addText("Trámite: $tipo_tramite", $subtituloStyle, ['alignment' => Jc::CENTER]);
$section->addText("Alumno: $nombre", $subtituloStyle, ['alignment' => Jc::CENTER]);
$section->addText("Número de control: $numero_control", $subtituloStyle, ['alignment' => Jc::CENTER]);
$section->addTextBreak(1);
$section->addText("Tu número de ticket es:", $subtituloStyle, ['alignment' => Jc::CENTER]);
$section->addText($ticket, $ticketStyle, ['alignment' => Jc::CENTER]);
$section->addTextBreak(2);
$section->addText("Fecha de registro: " . date('d/m/Y'), ['size' => 10, 'italic' => true], ['alignment' => Jc::CENTER]);
$writer = \PhpOffice\PhpWord\IOFactory::createWriter($word, 'Word2007');
$writer->save($constanciaDocPath);
$constanciaDocx = realpath($constanciaDocPath);
$constanciaPdfPath = str_replace('.docx','.pdf',$constanciaDocx);
$cmd = "\"$sofficePath\" --headless --convert-to pdf --outdir \"" . dirname($constanciaPdfPath) . "\" \"$constanciaDocx\"";
exec($cmd);


// Eliminar firma temporal si existe
if ($firmaPath && file_exists($firmaPath)) {
    unlink($firmaPath);
}

// 8. Enviar correo confirmación
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = $_ENV['MAIL_HOST'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['MAIL_USERNAME'];
    $mail->Password   = $_ENV['MAIL_PASSWORD'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $_ENV['MAIL_PORT'];
    $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);
    $mail->addAddress($correo);
    $mail->isHTML(false);
    $mail->Subject = "Confirmación de Trámite - $tipo_tramite";
    $mail->Body    = "Hola $nombre,\n\nTu trámite fue registrado con éxito. Tu número de ticket es: $ticket.\n\nGracias.";
    $mail->send();
} catch (Exception $e) {
    error_log("Error al enviar correo: " . $mail->ErrorInfo);
}

// 9. Redirigir a confirmación
header("Location: confirmar_tramite.php?ticket=" . urlencode($ticket));
exit;
