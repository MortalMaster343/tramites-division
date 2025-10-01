<?php
require_once __DIR__ . '/../config.php';
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\{
    Chart, DataSeries, Layout, Legend, PlotArea, Title, DataSeriesValues
};

// Obtener selección del formulario
$tipos = $_POST['tipos'] ?? [];
$graficoVar = $_POST['grafico_variable'] ?? 'total';
$agruparPor = $_POST['agrupar_por'] ?? 'tipo_tramite';

$fechaInicio = $_POST['fecha_inicio'] ?? null;
$fechaFin    = $_POST['fecha_fin'] ?? null;

if (empty($tipos)) {
    die("Debes seleccionar al menos un tipo de trámite.");
}

$campos = ['nombre_alumno', 'numero_control', 'correo_alumno', 'tipo_tramite', 'estado'];

$condiciones = ["tipo_tramite IN (" . implode(',', array_fill(0, count($tipos), '?')) . ")"];
$parametros = $tipos;

// Agregar condiciones de fecha si se proporcionan
if (!empty($fechaInicio)) {
    $condiciones[] = "fecha_envio >= ?";
    $parametros[] = $fechaInicio;
}
if (!empty($fechaFin)) {
    $condiciones[] = "fecha_envio <= ?";
    $parametros[] = $fechaFin;
}

$where = implode(' AND ', $condiciones);

// Consulta de datos para hoja principal
$stmt = $pdo->prepare("
    SELECT " . implode(',', $campos) . "
    FROM tramites
    WHERE $where
    ORDER BY fecha_envio ASC
");
$stmt->execute($parametros);
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Crear Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Reporte');

$encabezados = array_merge(['#'], array_map(fn($c) => ucfirst(str_replace('_', ' ', $c)), $campos));
foreach ($encabezados as $i => $titulo) {
    $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
    $sheet->setCellValue($col . '1', $titulo);
}

// Llenar datos
$fila = 2;
foreach ($datos as $index => $filaDatos) {
    $sheet->setCellValue('A' . $fila, $index + 1);
    $col = 2;
    foreach ($campos as $campo) {
        $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
        $sheet->setCellValue($letter . $fila, $filaDatos[$campo]);
        $col++;
    }
    $fila++;
}

// Crear hoja de gráfico
$graficoSheet = $spreadsheet->createSheet();
$graficoSheet->setTitle('Gráfico');

$groupQuery = "
    SELECT $agruparPor AS categoria,
           SUM(estado = 'aprobado') AS aprobados,
           SUM(estado = 'rechazado') AS rechazados,
           COUNT(*) AS total
    FROM tramites
    WHERE $where
    GROUP BY categoria
    ORDER BY categoria
";

$statsStmt = $pdo->prepare($groupQuery);
$statsStmt->execute($parametros);
$stats = $statsStmt->fetchAll(PDO::FETCH_ASSOC);

// Encabezados de gráfico
$graficoSheet->setCellValue('A1', ucfirst($agruparPor));
$graficoSheet->setCellValue('B1', ucfirst($graficoVar));

$i = 2;
foreach ($stats as $s) {
    $graficoSheet->setCellValue("A$i", $s['categoria']);
    $graficoSheet->setCellValue("B$i", $s[$graficoVar]);
    $i++;
}

// Crear gráfico pastel
$labels = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "Gráfico!A2:A" . ($i - 1), null, count($stats))];
$values = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "Gráfico!B2:B" . ($i - 1), null, count($stats))];

$series = new DataSeries(
    DataSeries::TYPE_PIECHART,
    null,
    range(0, count($values) - 1),
    [],
    $labels,
    $values
);

$plot = new PlotArea(null, [$series]);
$chart = new Chart(
    'grafico_tramites',
    new Title("Trámites por " . ($agruparPor === 'tipo_tramite' ? "tipo" : "carrera") . ": " . ucfirst($graficoVar)),
    new Legend(),
    $plot
);
$chart->setTopLeftPosition('D2');
$chart->setBottomRightPosition('L20');

$graficoSheet->addChart($chart);

// Descargar archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte_tramites.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->setIncludeCharts(true);
$writer->save('php://output');
exit;
