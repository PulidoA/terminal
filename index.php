<?php
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/auth.php';


// Helpers sencillos
function format_currency_cop($value) {
    if ($value === null || $value === '') return '';
    // Asumimos que en la BD el precio está en números (ej: 120000)
    return '$ ' . number_format((float)$value, 0, ',', '.');
}

// --- Consultas ---
// Empresas
$empresas = [];
$sql_empresas = "SELECT id, nombre, telefono, web, terminal FROM empresas ORDER BY nombre ASC";
if ($result = mysqli_query($conn, $sql_empresas)) {
    $empresas = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
}

// Rutas y horarios
$rutas = [];
$sql_rutas = "SELECT r.id,
                     r.empresa_id,
                     r.servicio,
                     r.origen,
                     r.destino,
                     DATE_FORMAT(r.salida, '%H:%i') AS salida_hora,
                     DATE_FORMAT(r.llegada, '%H:%i') AS llegada_hora,
                     r.disponibilidad,
                     r.precio,
                     e.nombre AS empresa_nombre
              FROM rutas r
              JOIN empresas e ON e.id = r.empresa_id
              ORDER BY r.salida ASC";
if ($result = mysqli_query($conn, $sql_rutas)) {
    $rutas = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Consulta rutas, horarios, disponibilidad y empresas de transporte en la Terminal de Transporte. Plataforma académica desarrollada con PHP y MySQL.">
<link rel="canonical" href="http://localhost:8888/terminal/">

<!-- Open Graph -->
<meta property="og:title" content="Terminal de Transporte – Rutas y horarios">
<meta property="og:description" content="Consulta rutas, horarios, disponibilidad y empresas de transporte. Sistema académico basado en PHP y MySQL.">
<meta property="og:url" content="http://localhost:8888/terminal/">
<meta property="og:type" content="website">
<meta property="og:image" content="http://localhost:8888/terminal/img/og-image.jpg">
  <title>Terminal de Transporte</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Estilos propios -->
  <link href="style.css" rel="stylesheet">
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "Terminal de Transporte",
    "url": "http://localhost:8888/terminal/",
    "logo": "http://localhost:8888/terminal/img/logo.png"
  }
  </script>

  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebSite",
    "url": "http://localhost:8888/terminal/",
    "name": "Terminal de Transporte",
    "potentialAction": {
      "@type": "SearchAction",
      "target": "http://localhost:8888/terminal/buscar.php?q={search_term_string}",
      "query-input": "required name=search_term_string"
    }
  }
  </script>
</head>
<body>
  <!-- NAV -->
  <nav class="navbar navbar-expand-lg border-bottom bg-white sticky-top">
    <div class="container">
      <a class="navbar-brand fw-bold d-flex align-items-center" href="#inicio">
        <i class="bi bi-bus-front me-2"></i> Terminal de Transporte
      </a>
      <div id="nav" class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link active" href="#inicio">Inicio</a></li>
          <li class="nav-item"><a class="nav-link" href="#empresas">Empresas</a></li>
          <li class="nav-item"><a class="nav-link" href="#rutas">Rutas y Horarios</a></li>
          <li class="nav-item"><a class="nav-link" href="#compra">Compra</a></li>
          <li class="nav-item"><a class="nav-link" href="#pqrs">PQRS</a></li>
          <li class="nav-item"><a class="nav-link" href="#politicas">Políticas</a></li>
          <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
                 <?php if (!empty($_SESSION['admin_user_id'])): ?>
            <li class="nav-item"><a class="nav-link" href="/terminal/admin/index.php">Panel</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="/terminal/auth/login.php">Admin</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- HERO -->
  <header id="inicio" class="py-5 bg-light border-bottom">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <h1 class="display-6 fw-semibold">Consulta y compra tus tiquetes en línea</h1>
          <p class="lead text-muted">Información de empresas, rutas, horarios, disponibilidad y precios.</p>
        </div>
        <div class="col-lg-5">
          <div class="card shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Búsqueda rápida</h5>
              <p class="text-muted">Formulario de ejemplo (solo visual, sin acciones).</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- EMPRESAS -->
  <section id="empresas" class="py-5 bg-light border-top">
    <div class="container">
      <h2 class="h4 mb-4">Empresas vinculadas</h2>
      <div class="row g-4">
            <?php if (empty($empresas)): ?>
      <div class="col-12">
        <div class="alert alert-warning mb-0">No hay empresas registradas aún.</div>
      </div>
    <?php else: ?>
      <?php foreach ($empresas as $emp): ?>
        <!-- Expreso Brasilia -->
        <div class="col-md-6">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
                            <h5 class="card-title">🚌 <?php echo e($emp['nombre']); ?></h5>
              <p class="mb-1"><b>Tel:</b> <?php echo e($emp['telefono']); ?></p>
              <p class="mb-1"><b>Web:</b>
                <?php if (!empty($emp['web'])): ?>
                  <a href="<?php echo e($emp['web']); ?>" target="_blank" rel="noopener" class="link-primary text-decoration-underline">sitio</a>
                <?php else: ?>
                  <span class="text-muted">—</span>
                <?php endif; ?>
              </p>
              <p class="mb-0"><b>Terminal:</b> <?php echo e($emp['terminal']); ?></p>
            </div>
          </div>
        </div>
              <?php endforeach; ?>
    <?php endif; ?>
  </div>
      </div>
    </div>
  </section>

  <!-- RUTAS Y HORARIOS -->
  <section id="rutas" class="py-5">
    <div class="container">
      <h2 class="h4 mb-4">Rutas y horarios</h2>

      <!-- Tabla -->
      <div class="table-responsive">
        <table class="table align-middle">
          <thead class="table-light">
            <tr>
              <th>Empresa</th>
              <th>Servicio</th>
              <th>Origen</th>
              <th>Destino</th>
              <th>Salida</th>
              <th>Llegada</th>
              <th>Disponibilidad</th>
              <th>Precio</th>
            </tr>
          </thead>
          <tbody>
              <?php if (empty($rutas)): ?>
    <tr>
      <td colspan="8" class="text-center text-muted">No hay rutas registradas aún.</td>
    </tr>
  <?php else: ?>
    <?php foreach ($rutas as $ruta): ?>
      <tr>
        <td><?php echo e($ruta['empresa_nombre']); ?></td>
        <td><?php echo e($ruta['servicio']); ?></td>
        <td><?php echo e($ruta['origen']); ?></td>
        <td><?php echo e($ruta['destino']); ?></td>
        <td><?php echo e($ruta['salida_hora']); ?></td>
        <td><?php echo e($ruta['llegada_hora']); ?></td>
        <td>
          <?php if ((int)$ruta['disponibilidad'] > 0): ?>
            <span class="badge bg-success"><?php echo e((int)$ruta['disponibilidad']); ?> asientos</span>
          <?php else: ?>
            <span class="badge bg-secondary">Sin asientos</span>
          <?php endif; ?>
        </td>
        <td><?php echo format_currency_cop($ruta['precio']); ?></td>
      </tr>
    <?php endforeach; ?>
  <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- COMPRA -->
  <section id="compra" class="py-5 bg-light border-top">
    <div class="container">
      <h2 class="h4 mb-4">Resumen de compra</h2>
      <div class="card shadow-sm">
        <div class="card-body">
          <p class="text-muted mb-0">Ejemplo de resumen estático (sin acciones).</p>
        </div>
      </div>
    </div>
  </section>

  <!-- PQRS -->
  <section id="pqrs" class="py-5">
    <div class="container">
      <h2 class="h4 mb-3">PQRS</h2>
      <p class="text-muted">Formulario de PQRS (solo referencia visual).</p>
    </div>
  </section>

  <!-- POLÍTICAS -->
  <section id="politicas" class="py-5 bg-light border-top">
    <div class="container">
      <h2 class="h4 mb-3">Políticas de viaje</h2>
      <ul>
        <li><b>Equipaje:</b> Lineamientos básicos sobre equipaje permitido.</li>
        <li><b>Viaje con menores:</b> Requisitos y recomendaciones para menores de edad.</li>
        <li><b>Cambios y devoluciones:</b> Condiciones y plazos para reprogramar o solicitar reembolso.</li>
      </ul>
    </div>
  </section>

  <!-- CONTACTO -->
  <section id="contacto" class="py-5">
    <div class="container">
      <h2 class="h4 mb-4">Contacto</h2>
      <ul class="list-unstyled small">
        <li><i class="bi bi-geo-alt me-2"></i>Terminal de Transportes de Cartagena</li>
        <li><i class="bi bi-telephone me-2"></i>(+57) 605 666 0000</li>
        <li><i class="bi bi-whatsapp me-2"></i>(+57) 300 123 4567</li>
      </ul>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="py-4 border-top bg-white">
    <div class="container small d-flex flex-column flex-md-row justify-content-between gap-2">
      <span>© 2025 Alexandra Pulido UNAD</span>
      <div class="d-flex gap-3">
        <a class="text-muted" href="#politicas">Políticas</a>
        <a class="text-muted" href="#pqrs">PQRS</a>
        <a class="text-muted" href="#contacto">Contacto</a>
      </div>
    </div>
  </footer>

  <!-- JS propio -->
  <script src="script.js"></script>
</body>
</html>