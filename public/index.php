<?php
$fecha_actual = date('d/m/Y'); // Formato dd/mm/aaaa
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistema de Trámites</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" />
  <!-- Tu CSS adicional -->
  <link rel="stylesheet" href="style.css">

  <style>
    .form-section { display: none; }

    #signature-pad {
      border: 2px solid #000;
      border-radius: 5px;
      width: 100%;
      max-width: 450px;
      height: 250px;
    }

    #overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.6);
      z-index: 998;
    }

    #explicacion-tramite {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: #fff;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.3);
      max-width: 90%;
      width: 600px;
      z-index: 999;
      text-align: center;
    }

    #explicacion-tramite p {
      font-size: 1.5rem;
      line-height: 1.5;
    }

    @media (max-width: 768px) {
      #explicacion-tramite {
        width: 95%;
        padding: 20px;
      }
      #explicacion-tramite p {
        font-size: 1rem;
      }
    }

    #explicacion-tramite .cerrar-explicacion {
      position: absolute;
      top: 8px;
      right: 12px;
      cursor: pointer;
      color: #666;
      font-size: 1.5rem;
    }

    #explicacion-tramite .cerrar-explicacion:hover {
      color: #000;
    }
  </style>
</head>
<body class="bg-light">

<div class="container mt-4">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3 gap-2">
    <h2 class="mb-0 text-center text-md-start">Iniciar Trámite</h2>
    <div class="d-flex flex-column flex-sm-row gap-2">
     <a href="Titulacion/titulacion.php" class="btn btn-secondary rounded-pill px-3 py-2">Titulación</a>
      <a href="login.php" class="btn btn-secondary rounded-pill px-3 py-2">Iniciar sesión</a>
    </div>
  </div>

  <div id="overlay"></div>
  <div id="explicacion-tramite">
    <span class="cerrar-explicacion"><i class="fa-solid fa-xmark"></i></span>
    <p id="texto-explicacion"></p>
  </div>

  <form action="procesar_tramite.php" method="POST" enctype="multipart/form-data">
    <!-- Datos generales -->
    <div class="mb-3">
      <label class="form-label"><i class="fa-solid fa-user"></i> Nombre completo</label>
      <input type="text" name="nombre_alumno" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Número de control</label>
      <input type="text" name="numero_control" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label"><i class="fa-solid fa-envelope"></i> Correo institucional</label>
      <input type="email" name="correo_alumno" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Selecciona tu carrera</label>
      <select name="carrera_seleccionada" class="form-select" required>
        <option value="">-- Selecciona una carrera --</option>
        <option value="Ingeniería Ferroviaria">Ingeniería Ferroviaria</option>
        <option value="Turismo">Turismo</option>
        <option value="Arquitectura">Arquitectura</option>
        <option value="Contador Público">Contador Público</option>
        <option value="Licenciatura en Administración">Licenciatura en Administración</option>
        <option value="Ingeniería en Gestión Empresarial">Ingeniería en Gestión Empresarial</option>
        <option value="Ingeniería en Sistemas Computacionales">Ingeniería en Sistemas Computacionales</option>
        <option value="Ingeniería Civil">Ingeniería Civil</option>
        <option value="Ingeniería Mecatrónica">Ingeniería Mecatrónica</option>
        <option value="Ingeniería Electromecánica">Ingeniería Electromecánica</option>
        <option value="Ingeniería en Administración">Ingeniería en Administración</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Tipo de trámite</label>
      <select name="tipo_tramite" id="tipo_tramite" class="form-select" required>
        <option value="">Selecciona un trámite</option>
        <option value="prorroga">Prórroga</option>
        <option value="equivalencia">Equivalencia</option>
        <option value="convalidacion">Convalidación</option>
        <option value="traslado">Traslado</option>
        <option value="activacion">Activación</option>
      </select>
    </div>

    <?php include __DIR__ . '/../includes/forms/prorroga.php'; ?>
    <?php include __DIR__ . '/../includes/forms/equivalencia.php'; ?>
    <?php include __DIR__ . '/../includes/forms/convalidacion.php'; ?>
    <?php include __DIR__ . '/../includes/forms/traslado.php'; ?>
    <?php include __DIR__ . '/../includes/forms/activacion.php'; ?>

    <div id="firma-section" style="display: none;">
      <h4 class="mt-4">Firma del Alumno</h4>
      <canvas id="signature-pad"></canvas>
      <div class="mt-2">
        <button type="button" class="btn btn-warning btn-sm" id="clear">Limpiar</button>
      </div>
      <input type="hidden" name="firma" id="firmaInput">
    </div>

    <div class="d-flex justify-content-center mt-3">
      <button type="submit" class="btn btn-primary mx-2">Enviar trámite</button>
      <a href="consulta_tramite.php" class="btn btn-info mx-2">Consultar trámite</a>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
  const tipoTramite = document.getElementById('tipo_tramite');
  const explicacionBox = document.getElementById('explicacion-tramite');
  const overlay = document.getElementById('overlay');
  const textoExplicacion = document.getElementById('texto-explicacion');
  const firmaSection = document.getElementById('firma-section');
  const cerrarExplicacion = document.querySelector('.cerrar-explicacion');

  const explicaciones = {
    prorroga: "La prórroga te permite extender tu permanencia en la institución por un semestre adicional, dependiendo de la situación académica.",
    equivalencia: "La equivalencia valida las materias cursadas en otra institución para integrarlas en tu plan de estudios actual.",
    convalidacion: "La convalidación reconoce tus estudios previos en otra institución para continuar en el ITC.",
    traslado: "El traslado es el trámite para cambiar de campus o institución dentro del sistema de tecnológicos.",
    activacion: "La activación reactiva tu matrícula si estuviste inactivo y deseas continuar tus estudios."
  };

  const secciones = {
    prorroga: document.getElementById('form-prorroga'),
    equivalencia: document.getElementById('form-equivalencia'),
    convalidacion: document.getElementById('form-convalidacion'),
    traslado: document.getElementById('form-traslado'),
    activacion: document.getElementById('form-activacion')
  };

  tipoTramite.addEventListener('change', () => {
    Object.values(secciones).forEach(sec => sec.style.display = 'none');
    const selected = tipoTramite.value;

    if (explicaciones[selected]) {
      textoExplicacion.textContent = explicaciones[selected];
      explicacionBox.style.display = 'block';
      overlay.style.display = 'block';
    } else {
      explicacionBox.style.display = 'none';
      overlay.style.display = 'none';
    }

    if (secciones[selected]) {
      secciones[selected].style.display = 'block';
    }

    firmaSection.style.display = selected !== "" ? "block" : "none";
  });

  cerrarExplicacion.addEventListener('click', () => {
    explicacionBox.style.display = 'none';
    overlay.style.display = 'none';
  });

  overlay.addEventListener('click', () => {
    explicacionBox.style.display = 'none';
    overlay.style.display = 'none';
  });

  const canvas = document.getElementById("signature-pad");
  const signaturePad = new SignaturePad(canvas);

  document.querySelector("form").addEventListener("submit", (event) => {
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
