<?php
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/auth.php';
require_auth();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Panel administrativo de la Terminal de Transporte. Permite gestionar empresas, rutas y horarios del sistema.">
  <link rel="canonical" href="http://localhost:8888/terminal/admin/index.php">

  <!-- Open Graph -->
  <meta property="og:title" content="Panel Administrativo – Terminal de Transporte">
  <meta property="og:description" content="Accede al módulo administrativo para gestionar empresas y rutas del sistema Terminal de Transporte.">
  <meta property="og:url" content="http://localhost:8888/terminal/admin/index.php">
  <meta property="og:type" content="website">
  <meta property="og:image" content="http://localhost:8888/terminal/img/og-image.jpg">
  <title>Panel Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="mb-0">Panel de Administración</h3>
      <div>
        <span class="me-3">Hola, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
        <a class="btn btn-outline-secondary btn-sm" href="/terminal/auth/logout.php">Cerrar sesión</a>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-md-6">
        <a href="/terminal/admin/empresas.php" class="btn btn-primary w-100">Gestionar Empresas</a>
      </div>
      <div class="col-md-6">
        <a href="/terminal/admin/rutas.php" class="btn btn-primary w-100">Gestionar Rutas</a>
      </div>
    </div>

    <p class="text-center mt-4"><a href="/terminal/index.php">← Ir al sitio público</a></p>
  </div>
</body>
</html>