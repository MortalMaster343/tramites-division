<?php
session_start();
?>
<?php
include $_SERVER['DOCUMENT_ROOT'] . '/tramites-division/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Formulario Titulación</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.6/dist/signature_pad.umd.min.js"></script>
<link rel="stylesheet" href="/tramites-division/public/style.css">

  <style>
    #signature-pad {
      border: 2px solid #000;
      border-radius: 5px;
      width: 400px;
      height: 200px;
    }
  </style>
</head>
<body>
<div class="container mt-5">
  <h2>Formulario de Titulación</h2>
  <p>Llena cada apartado y sube tus documentos correspondientes.</p>

  <form action="procesar_titulacion.php" method="POST" enctype="multipart/form-data">

    <!-- Número de control -->
    <div class="mb-3">
      <label for="control" class="form-label">Número de Control</label>
      <input type="text" class="form-control" name="numero_control" id="control" required>
    </div>

    <!-- Acordeón -->
    <div class="accordion" id="formulariosTitulacion">

      <!-- Solicitud Acto Recepcional -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingOne">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#solicitud">
            Solicitud de Acto Recepcional
          </button>
        </h2>
        <div id="solicitud" class="accordion-collapse collapse show">
          <div class="accordion-body">
            <div class="mb-3"><label>Opción</label><input type="text" name="opcion" class="form-control" required></div>
            <div class="mb-3"><label>Título profesional</label><input type="text" name="titulo_prof" class="form-control" required></div>
            <div class="mb-3"><label>Nombre completo</label><input type="text" name="nombre" class="form-control" required></div>
            <div class="mb-3"><label>Instituto de procedencia</label><input type="text" name="procedencia" class="form-control" required></div>
          </div>
        </div>
      </div>

      <!-- Hoja de Datos -->
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#hojaDatos">
            Hoja de Datos Personales
          </button>
        </h2>
        <div id="hojaDatos" class="accordion-collapse collapse">
          <div class="accordion-body">
            <div class="mb-3"><label>Domicilio</label><input type="text" name="domicilio" class="form-control" required></div>
            <div class="mb-3"><label>Teléfono</label><input type="text" name="telefono" class="form-control" required></div>
            <div class="mb-3"><label>Correo</label><input type="email" name="correo" class="form-control" required></div>
          </div>
        </div>
      </div>

      <!-- Constancia No Adeudo -->
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#noAdeudo">
            Constancia de No Adeudo
          </button>
        </h2>
        <div id="noAdeudo" class="accordion-collapse collapse">
          <div class="accordion-body">
            <p>Sube tu constancia:</p>
            <input type="file" name="no_adeudo" class="form-control" accept=".pdf,.jpg,.png" required>
          </div>
        </div>
      </div>
    </div>

    <!-- Firma -->
    <h4 class="mt-4">Firma del Alumno</h4>
    <canvas id="signature-pad"></canvas>
    <div class="mt-2">
      <button type="button" class="btn btn-warning btn-sm" id="clear">Limpiar</button>
    </div>
    <input type="hidden" name="firma" id="firmaInput">

    <!-- Documentos extra -->
    <h4 class="mt-4">Otros documentos</h4>
    <div class="mb-3"><label>Acta nacimiento (PDF)</label><input type="file" name="acta" class="form-control" accept=".pdf" required></div>
    <div class="mb-3"><label>Certificado profesional (PDF)</label><input type="file" name="certificado" class="form-control" accept=".pdf" required></div>
    <div class="mb-3"><label>CURP (PDF)</label><input type="file" name="curp" class="form-control" accept=".pdf" required></div>
    <div class="mb-3"><label>Fotografías (ZIP o PDF)</label><input type="file" name="fotos" class="form-control" accept=".zip,.pdf" required></div>

    <button type="submit" class="btn btn-success mt-3">Enviar Trámite</button>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const canvas = document.getElementById("signature-pad");
  const signaturePad = new SignaturePad(canvas);

  document.querySelector("form").addEventListener("submit", () => {
    if (!signaturePad.isEmpty()) {
      document.getElementById("firmaInput").value = signaturePad.toDataURL("image/png");
    } else {
      alert("Por favor, firme antes de enviar.");
      event.preventDefault();
    }
  });

  document.getElementById("clear").addEventListener("click", () => signaturePad.clear());
</script>
</body>
</html>
