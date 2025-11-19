<?php
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/auth.php';
require_auth();

$errors = [];
$ok = null;

// Cargar empresas para selects
$empresas = [];
$result = mysqli_query($conn, "SELECT id, nombre FROM empresas ORDER BY nombre ASC");
if ($result) {
    $empresas = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
}

/** CREATE / UPDATE / DELETE **/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_check($_POST['csrf'] ?? '')) {
    $errors[] = 'CSRF inválido.';
  } else {
    $action = $_POST['action'] ?? '';
    $empresa_id = (int)($_POST['empresa_id'] ?? 0);
    $servicio = trim((string)($_POST['servicio'] ?? ''));
    $origen = trim((string)($_POST['origen'] ?? ''));
    $destino = trim((string)($_POST['destino'] ?? ''));
    $salida = trim((string)($_POST['salida'] ?? ''));   // HH:ii
    $llegada = trim((string)($_POST['llegada'] ?? '')); // HH:ii
    $disponibilidad = (int)($_POST['disponibilidad'] ?? 0);
    $precio = (float)($_POST['precio'] ?? 0);

    if ($action === 'create') {
      if ($empresa_id <= 0) $errors[] = 'Empresa requerida.';
      if ($servicio === '' || $origen === '' || $destino === '' || $salida === '' || $llegada === '') {
        $errors[] = 'Todos los campos de ruta son obligatorios.';
      }
      if (!$errors) {
        $stmt = mysqli_prepare($conn, "INSERT INTO rutas (empresa_id, servicio, origen, destino, salida, llegada, disponibilidad, precio) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
          mysqli_stmt_bind_param($stmt, "isssssii", $empresa_id, $servicio, $origen, $destino, $salida, $llegada, $disponibilidad, $precio);
          if (mysqli_stmt_execute($stmt)) {
            $ok = 'Ruta creada.';
          } else {
            $errors[] = 'Error al crear ruta.';
          }
          mysqli_stmt_close($stmt);
        } else {
          $errors[] = 'Error en la preparación de la consulta.';
        }
      }
    } elseif ($action === 'update') {
      $id = (int)($_POST['id'] ?? 0);
      if ($id <= 0) $errors[] = 'ID inválido.';
      if ($empresa_id <= 0) $errors[] = 'Empresa requerida.';
      if ($servicio === '' || $origen === '' || $destino === '' || $salida === '' || $llegada === '') {
        $errors[] = 'Todos los campos de ruta son obligatorios.';
      }
      if (!$errors) {
        $stmt = mysqli_prepare($conn, "UPDATE rutas SET empresa_id=?, servicio=?, origen=?, destino=?, salida=?, llegada=?, disponibilidad=?, precio=? WHERE id=?");
        if ($stmt) {
          mysqli_stmt_bind_param($stmt, "isssssiii", $empresa_id, $servicio, $origen, $destino, $salida, $llegada, $disponibilidad, $precio, $id);
          if (mysqli_stmt_execute($stmt)) {
            $ok = 'Ruta actualizada.';
          } else {
            $errors[] = 'Error al actualizar ruta.';
          }
          mysqli_stmt_close($stmt);
        } else {
          $errors[] = 'Error en la preparación de la consulta.';
        }
      }
    } elseif ($action === 'delete') {
      $id = (int)($_POST['id'] ?? 0);
      if ($id <= 0) $errors[] = 'ID inválido.';
      if (!$errors) {
        $stmt = mysqli_prepare($conn, "DELETE FROM rutas WHERE id=?");
        if ($stmt) {
          mysqli_stmt_bind_param($stmt, "i", $id);
          if (mysqli_stmt_execute($stmt)) {
            $ok = 'Ruta eliminada.';
          } else {
            $errors[] = 'Error al eliminar ruta.';
          }
          mysqli_stmt_close($stmt);
        } else {
          $errors[] = 'Error en la preparación de la consulta.';
        }
      }
    }
  }
}

/** LIST **/
$rutas = [];
$sql = "SELECT r.*, e.nombre AS empresa_nombre
        FROM rutas r
        JOIN empresas e ON e.id = r.empresa_id
        ORDER BY r.salida ASC";
if ($result = mysqli_query($conn, $sql)) {
    $rutas = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Gestión de rutas y horarios de la Terminal de Transporte. Permite crear, editar y eliminar rutas de transporte.">
  <link rel="canonical" href="http://localhost:8888/terminal/admin/rutas.php">

  <!-- Open Graph -->
  <meta property="og:title" content="Gestión de rutas – Terminal de Transporte">
  <meta property="og:description" content="Administra rutas, empresas, horarios y disponibilidad en el sistema de la Terminal de Transporte.">
  <meta property="og:url" content="http://localhost:8888/terminal/admin/rutas.php">
  <meta property="og:type" content="website">
  <meta property="og:image" content="http://localhost:8888/terminal/img/og-image.jpg">

  <title>Rutas - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">Rutas</h4>
      <div>
        <a class="btn btn-outline-secondary btn-sm" href="/terminal/admin/index.php">Panel</a>
        <a class="btn btn-outline-danger btn-sm" href="/terminal/auth/logout.php">Salir</a>
      </div>
    </div>

    <?php if ($ok): ?><div class="alert alert-success py-2"><?php echo e($ok); ?></div><?php endif; ?>
    <?php foreach ($errors as $er): ?><div class="alert alert-danger py-2"><?php echo e($er); ?></div><?php endforeach; ?>

    <div class="card mb-4">
      <div class="card-body">
        <h6 class="mb-3">Nueva ruta</h6>
        <form method="post" class="row g-2">
          <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
          <input type="hidden" name="action" value="create">
          <div class="col-md-3">
            <select name="empresa_id" class="form-select" required>
              <option value="">Empresa...</option>
              <?php foreach ($empresas as $em): ?>
                <option value="<?php echo (int)$em['id']; ?>"><?php echo e($em['nombre']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2"><input class="form-control" name="servicio" placeholder="Servicio" required></div>
          <div class="col-md-2"><input class="form-control" name="origen" placeholder="Origen" required></div>
          <div class="col-md-2"><input class="form-control" name="destino" placeholder="Destino" required></div>
          <div class="col-md-1"><input class="form-control" type="time" name="salida" required></div>
          <div class="col-md-1"><input class="form-control" type="time" name="llegada" required></div>
          <div class="col-md-1"><input class="form-control" type="number" min="0" name="disponibilidad" placeholder="Disp." required></div>
          <div class="col-md-2"><input class="form-control" type="number" min="0" step="0.01" name="precio" placeholder="Precio" required></div>
          <div class="col-md-2 d-grid"><button class="btn btn-primary">Crear</button></div>
        </form>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table align-middle table-sm">
        <thead class="table-light">
          <tr>
            <th>Empresa</th><th>Servicio</th><th>Origen</th><th>Destino</th>
            <th>Salida</th><th>Llegada</th><th>Disp.</th><th>Precio</th><th style="width:320px">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rutas as $r): ?>
            <tr>
              <form method="post" class="row g-1">
                <td class="col">
                  <select name="empresa_id" class="form-select form-select-sm">
                    <?php foreach ($empresas as $em): ?>
                      <option value="<?php echo (int)$em['id']; ?>" <?php if ($em['id']==$r['empresa_id']) echo 'selected'; ?>>
                        <?php echo e($em['nombre']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </td>
                <td class="col"><input class="form-control form-control-sm" name="servicio" value="<?php echo e($r['servicio']); ?>"></td>
                <td class="col"><input class="form-control form-control-sm" name="origen" value="<?php echo e($r['origen']); ?>"></td>
                <td class="col"><input class="form-control form-control-sm" name="destino" value="<?php echo e($r['destino']); ?>"></td>
                <td class="col"><input class="form-control form-control-sm" type="time" name="salida" value="<?php echo e($r['salida']); ?>"></td>
                <td class="col"><input class="form-control form-control-sm" type="time" name="llegada" value="<?php echo e($r['llegada']); ?>"></td>
                <td class="col"><input class="form-control form-control-sm" type="number" min="0" name="disponibilidad" value="<?php echo (int)$r['disponibilidad']; ?>"></td>
                <td class="col"><input class="form-control form-control-sm" type="number" min="0" step="0.01" name="precio" value="<?php echo (float)$r['precio']; ?>"></td>
                <td class="col d-flex gap-2">
                  <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
                  <input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>">
                  <button name="action" value="update" class="btn btn-sm btn-success">Guardar</button>
                  <button name="action" value="delete" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar ruta?');">Eliminar</button>
                </td>
              </form>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </div>
</body>
</html>