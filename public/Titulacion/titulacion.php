<?php
include $_SERVER['DOCUMENT_ROOT'] . '/tramites-division/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar Titulación</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="/tramites-division/public/style.css">
  <style>
    .card-requisito {
      border-radius: 1rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      margin-bottom: 1.5rem;
      transition: transform 0.2s ease-in-out;
    }
    .card-requisito:hover {
      transform: scale(1.01);
    }
    h2 {
      margin-top: 1.2rem;
      font-size: 1.4rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: .5rem;
    }
    ul li {
      margin-bottom: .3rem;
    }
  </style>
</head>
<body>
<div class="container mt-5">
  <div class="text-center mb-4">
    <h2 class="mb-3"><i class="fa-solid fa-graduation-cap text-primary"></i> Trámite de Titulación</h2>
    <p class="lead">Bienvenido al proceso para obtener tu título profesional. Descarga los documentos, revisa los requisitos y comienza tu trámite.</p>
  </div>

  <div class="card card-requisito p-3">
    <h4><i class="fa-solid fa-folder-open text-warning"></i> Documentos para descargar</h4>
    <ul class="list-group list-group-flush">
      <li class="list-group-item">
        <a href="../Titulacion/REQUISITOS_TITULACION.pdf" target="_blank">
          <i class="fa-solid fa-file-pdf text-danger me-2"></i> Requisitos de Titulación (PDF)
        </a>
      </li>
      <li class="list-group-item">
        <a href="../Titulacion/CONSTANCIA_NO_ADEUDO.docx" target="_blank">
          <i class="fa-solid fa-file-word text-primary me-2"></i> Constancia de NO ADEUDO (Word)
        </a>
      </li>
    </ul>
  </div>

  <div class="card card-requisito p-4">
    <h3 class="text-center mb-3"><i class="fa-solid fa-list-check text-success"></i> Requisitos para Apertura de Expediente</h3>
    <p class="text-center"><strong>Importante:</strong> Únicamente se aceptan documentos entregados por el estudiante.</p>

    <h2><i class="fa-solid fa-1"></i> Solicitud de Opción</h2>
    <p>Formato de <strong>Solicitud de Acto de Recepción Profesional</strong> llenado a computadora y con firma.</p>
    <ul>
      <li>Fecha de inicio del proceso.</li>
      <li>Opción de titulación elegida (ej. Titulación Integral).</li>
      <li>Título profesional con género correcto.</li>
      <li>Nombre completo con acentos.</li>
      <li>Firma igual a la de tu INE.</li>
      <li>Número de control y nombre de la carrera.</li>
      <li>Instituto Tecnológico de procedencia.</li>
    </ul>

    <h2><i class="fa-solid fa-2"></i> Acta de Nacimiento</h2>
    <ul>
      <li>Sin tachaduras, sello y firma visibles.</li>
      <li><a href="https://www.gob.mx/ActaNacimiento/" target="_blank">Trámite en línea</a></li>
      <li>Extranjeros: acta apostillada y traducida.</li>
      <li>Entregar original y copia.</li>
    </ul>

    <h2><i class="fa-solid fa-3"></i> Certificado de Preparatoria</h2>
    <ul>
      <li>En buen estado, con periodos de estudio.</li>
      <li>Si no aparecen, anexar constancia de validación.</li>
    </ul>

    <h2><i class="fa-solid fa-4"></i> Certificado Profesional</h2>
    <ul><li>Emitido por el Tec, en buen estado, original y copia.</li></ul>

    <h2><i class="fa-solid fa-5"></i> Comprobante de No Adeudo</h2>
    <ul>
      <li>Recursos Financieros (Caja)</li>
      <li>Centro de Información (Biblioteca)</li>
    </ul>

    <h2><i class="fa-solid fa-camera"></i> Fotografías</h2>
    <ul>
      <li>7 credencial ovaladas y 4 infantiles, en papel mate B/N.</li>
      <li>Mujeres: blusa blanca, saco negro. Hombres: saco oscuro, corbata.</li>
    </ul>

    <h2><i class="fa-solid fa-file-circle-check"></i> Constancias</h2>
    <ul>
      <li>Liberación del Servicio Social.</li>
      <li>Liberación de Prácticas o Residencia.</li>
      <li>Acreditación de Inglés (si aplica).</li>
    </ul>

    <h2><i class="fa-solid fa-id-card"></i> CURP</h2>
    <p>Copia actualizada a color.</p>

    <h2><i class="fa-solid fa-money-bill-wave"></i> Pagos Bancarios</h2>
    <ul>
      <li>$1,200 - Trámite de titulación.</li>
      <li>$350 - Digitalización.</li>
    </ul>

    <h2><i class="fa-solid fa-signature"></i> E-Firma</h2>
    <p>Comprobante de generación de e-firma (SAT).</p>

    <h2><i class="fa-solid fa-passport"></i> FM3 (Extranjeros)</h2>
    <p>Documento migratorio original y copia.</p>

    <h2><i class="fa-solid fa-file-lines"></i> Hoja de Datos Personales</h2>
    <p>Formato oficial llenado a computadora.</p>

    <h2><i class="fa-solid fa-envelope-open-text"></i> Documentos Digitales</h2>
    <p>Escanear en PDF (máx. 2 MB) y enviar a <a href="mailto:se.titula@cancun.tecnm.mx">se.titula@cancun.tecnm.mx</a>.</p>

    <h2><i class="fa-solid fa-globe"></i> Requisitos para Extranjeros</h2>
    <ul>
      <li>CURP + FM3 en un solo PDF.</li>
      <li>Acta apostillada y traducida.</li>
      <li>Revalidación/equivalencia de estudios.</li>
    </ul>

    <p class="text-center mt-4"><strong>⏰ Horario de entrega:</strong> Lunes a viernes de 11:00 a 16:00 hrs.</p>
  </div>

  <div class="d-flex justify-content-center gap-3 mt-4">
    <a href="formulario_titulacion.php" class="btn btn-primary btn-lg">
      <i class="fa-solid fa-play"></i> Iniciar Trámite
    </a>
    <a href="../index.php" class="btn btn-secondary btn-lg">
      <i class="fa-solid fa-arrow-left"></i> Regresar
    </a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
