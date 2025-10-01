<?php
require_once __DIR__ . '/../Models/Tramite.php';
require_once __DIR__ . '/../config.php';
use PhpOffice\PhpWord\TemplateProcessor;
use PHPMailer\PHPMailer\PHPMailer;

class TramiteController {
    private $model;

    public function __construct($pdo) {
        $this->model = new Tramite($pdo);
    }

    public function procesarFormulario($post, $files) {
        // Validaciones y procesamiento de $post, $files
        // Generar ticket, fechas, Word/PDF, firma, etc.

        $ticket = strtoupper(uniqid('TKT-'));
        $datosParaGuardar = [
            'ticket' => $ticket,
            'tipo_tramite' => $post['tipo_tramite'],
            'nombre_alumno' => $post['nombre_alumno'],
            'numero_control' => $post['numero_control'],
            'correo_alumno' => $post['correo_alumno'],
            'carrera_seleccionada' => $post['carrera_seleccionada'],
            'datos_json' => json_encode($post, JSON_UNESCAPED_UNICODE),
            'fecha_envio' => date('Y-m-d H:i:s'),
            'estado' => 'pendiente',
            'archivo' => null // luego se llena con la ruta del archivo si aplica
        ];

        $this->model->guardar($datosParaGuardar);

        return $ticket;
    }
}
