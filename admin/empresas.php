<?php
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/auth.php';
require_auth();

$errors = [];
$ok = null;

/** CREATE / UPDATE / DELETE **/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_check($_POST['csrf'] ?? '')) {
    $errors[] = 'CSRF inválido.';
  } else {
    $action = $_POST['action'] ?? '';
    $nombre = trim((string)($_POST['nombre'] ?? ''));
    $telefono = trim((string)($_POST['telefono'] ?? ''));
    $web = trim((string)($_POST['web'] ?? ''));
    $terminal = trim((string)($_POST['terminal'] ?? ''));

    if ($action === 'create') {
      if ($nombre === '') $errors[] = 'El nombre es obligatorio.';
      if (!$errors) {
        $stmt = mysqli_prepare($conn, "INSERT INTO empresas (nombre, telefono, web, terminal) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $nombre, $telefono, $web, $terminal);
        if (mysqli_stmt_execute($stmt)) {
          $ok = 'Empresa creada.';
        } else {
          $errors[] = 'Error al crear empresa: ' . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
      }
    } elseif ($action === 'update') {
      $id = (int)($_POST['id'] ?? 0);
      if ($id <= 0) $errors[] = 'ID inválido.';
      if ($nombre === '') $errors[] = 'El nombre es obligatorio.';
      if (!$errors) {
        $stmt = mysqli_prepare($conn, "UPDATE empresas SET nombre=?, telefono=?, web=?, terminal=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssssi", $nombre, $telefono, $web, $terminal, $id);
        if (mysqli_stmt_execute($stmt)) {
          $ok = 'Empresa actualizada.';
        } else {
          $errors[] = 'Error al actualizar empresa: ' . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
      }
    } elseif ($action === 'delete') {
      $id = (int)($_POST['id'] ?? 0);
      if ($id <= 0) $errors[] = 'ID inválido.';
      if (!$errors) {
        $stmt = mysqli_prepare($conn, "DELETE FROM empresas WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
          $ok = 'Empresa eliminada.';
        } else {
          $errors[] = 'Error al eliminar empresa: ' . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
      }
    }
  }
}

/** LIST **/
$empresas = [];
$result = mysqli_query($conn, "SELECT id, nombre, telefono, web, terminal FROM empresas ORDER BY nombre ASC");
if ($result) {
  $empresas = mysqli_fetch_all($result, MYSQLI_ASSOC);
  mysqli_free_result($result);
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Administración de empresas de transporte dentro del sistema Terminal de Transporte. Permite crear, editar y eliminar empresas registradas.">
  <link rel="canonical" href="http://localhost:8888/terminal/admin/empresas.php">

  <!-- Open Graph -->
  <meta property="og:title" content="Gestión de empresas de transporte – Panel Administrativo">
  <meta property="og:description" content="Crea, actualiza y elimina empresas de transporte en el sistema Terminal de Transporte.">
  <meta property="og:url" content="http://localhost:8888/terminal/admin/empresas.php">
  <meta property="og:type" content="website">
  <meta property="og:image" content="http://localhost:8888/terminal/img/og-image.jpg">
  <title>Empresas - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">Empresas</h4>
      <div>
        <a class="btn btn-outline-secondary btn-sm" href="/terminal/admin/index.php">Panel</a>
        <a class="btn btn-outline-danger btn-sm" href="/terminal/auth/logout.php">Salir</a>
      </div>
    </div>

    <?php if ($ok): ?><div class="alert alert-success py-2"><?php echo e($ok); ?></div><?php endif; ?>
    <?php foreach ($errors as $er): ?><div class="alert alert-danger py-2"><?php echo e($er); ?></div><?php endforeach; ?>

    <div class="card mb-4">
      <div class="card-body">
        <h6 class="mb-3">Nueva empresa</h6>
        <form method="post" class="row g-2">
          <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
          <input type="hidden" name="action" value="create">
          <div class="col-md-3"><input class="form-control" name="nombre" placeholder="Nombre" required></div>
          <div class="col-md-2"><input class="form-control" name="telefono" placeholder="Teléfono"></div>
          <div class="col-md-3"><input class="form-control" name="web" placeholder="https://..."></div>
          <div class="col-md-2"><input class="form-control" name="terminal" placeholder="Terminal"></div>
          <div class="col-md-2 d-grid"><button class="btn btn-primary">Crear</button></div>
        </form>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table align-middle table-sm">
        <thead class="table-light">
          <tr>
            <th>Nombre</th><th>Teléfono</th><th>Web</th><th>Terminal</th><th style="width:220px">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($empresas as $eRow): ?>
            <tr>
              <form method="post" class="row g-1">
                <td class="col">
                  <input class="form-control form-control-sm" name="nombre" value="<?php echo e($eRow['nombre']); ?>" required>
                </td>
                <td class="col">
                  <input class="form-control form-control-sm" name="telefono" value="<?php echo e($eRow['telefono']); ?>">
                </td>
                <td class="col">
                  <input class="form-control form-control-sm" name="web" value="<?php echo e($eRow['web']); ?>">
                </td>
                <td class="col">
                  <input class="form-control form-control-sm" name="terminal" value="<?php echo e($eRow['terminal']); ?>">
                </td>
                <td class="col d-flex gap-2">
                  <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
                  <input type="hidden" name="id" value="<?php echo (int)$eRow['id']; ?>">
                  <button name="action" value="update" class="btn btn-sm btn-success">Guardar</button>
                  <button name="action" value="delete" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar empresa? También eliminará sus rutas.');">Eliminar</button>
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