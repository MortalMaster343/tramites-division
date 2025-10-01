<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Sistema de Trámites</title>

<link rel="icon" href="/tramites-division/includes/Images/favicon.ico" type="image/x-icon">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
  /* Contenedor principal: flex para desktop */
  header .container-fluid {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: nowrap;
  }

  header .text-center {
    text-align: center;
    flex: 1;
    margin: 0 20px;
  }

  header img {
    max-height: 70px;
    height: auto;
  }

  /* Moviles: logos arriba, texto debajo */
  @media (max-width: 576px) {
    header .container-fluid {
      flex-direction: column;
      align-items: center;
      gap: 10px;
    }

    header img {
      max-height: 50px;
    }

    header .text-center h1 {
      font-size: 1rem;
    }

    header .text-center h2 {
      font-size: 0.8rem;
    }
  }
</style>
</head>
<body>

<header class="p-3 bg-light border-bottom">
  <div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
    <!-- Logo izquierdo -->
    <a href="/tramites-division/public/index.php" class="mb-2 mb-md-0">
      <img src="/tramites-division/includes/Images/logo_izquierdo.png" alt="Logo Izquierdo" style="height:70px;">
    </a>

    <!-- Títulos -->
    <div class="text-center mb-2 mb-md-0 flex-grow-1">
      <h1 class="h5 fw-bold text-dark mb-1">Instituto Tecnológico de Cancún</h1>
      <h2 class="h6 text-secondary">División de Estudios Profesionales</h2>
    </div>

    <!-- Logo derecho -->
    <a href="/tramites-division/public/index.php" class="mb-2 mb-md-0">
      <img src="/tramites-division/includes/Images/logo_derecho.png" alt="Logo Derecho" style="height:70px;">
    </a>
  </div>
</header>


</body>
</html>
