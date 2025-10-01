<!DOCTYPE html>
<html lang="es">
<?php include __DIR__ . '/../includes/header.php'; ?>
<head>
  <meta charset="UTF-8" />
  <title>Consulta de Trámite</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
  <style>
    body {
      background-color: #f0f2f5;
    }
    .consulta-box {
      max-width: 400px;
      margin: 100px auto;
      padding: 25px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
  <div class="consulta-box">
    <h3 class="mb-4 text-center">Consulta tu trámite</h3>
    <form action="ver_tramite.php" method="POST">
      <div class="mb-3">
        <label for="ticket" class="form-label">Número de Ticket</label>
        <input type="text" name="ticket" id="ticket" class="form-control" placeholder="Ejemplo: TKT-123ABC" required />
      </div>
      <button type="submit" class="btn btn-primary w-100">Buscar trámite</button>
    </form>
    <a href="index.php" class="btn btn-link mt-3 d-block text-center">← Regresar</a>
  </div>
</body>
</html>
