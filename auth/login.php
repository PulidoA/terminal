<?php
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/auth.php';

$error = null;
$next = isset($_GET['next']) ? $_GET['next'] : '/terminal/admin/index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user = trim((string)($_POST['username'] ?? ''));
  $pass = (string)($_POST['password'] ?? '');
  $token = (string)($_POST['csrf'] ?? '');

  if (!csrf_check($token)) {
    $error = 'CSRF inválido.';
  } else {
    $stmt = mysqli_prepare($conn, "SELECT id, username, password_hash FROM admin_users WHERE username = ?");
    if ($stmt) {
      mysqli_stmt_bind_param($stmt, "s", $user);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      $row = mysqli_fetch_assoc($result);

      if ($row && password_verify($pass, $row['password_hash'])) {
        $_SESSION['admin_user_id'] = $row['id'];
        $_SESSION['admin_username'] = $row['username'];
        header('Location: ' . $next);
        exit;
      } else {
        $error = 'Usuario o contraseña incorrectos.';
      }

      mysqli_stmt_close($stmt);
    } else {
      $error = 'Error en la consulta a la base de datos.';
    }
  }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Acceso seguro al panel administrativo de la Terminal de Transporte. Inicia sesión con tu usuario y contraseña para gestionar empresas y rutas.">
  <link rel="canonical" href="http://localhost:8888/Proyecto_web_Alexa_Pulido%204/auth/login.php">

  <!-- Open Graph -->
  <meta property="og:title" content="Ingreso Administrador – Terminal de Transporte">
  <meta property="og:description" content="Formulario de acceso seguro para administradores de la Terminal de Transporte.">
  <meta property="og:url" content="http://localhost:8888/Proyecto_web_Alexa_Pulido%204/auth/login.php">
  <meta property="og:type" content="website">
  <meta property="og:image" content="http://localhost:8888/Proyecto_web_Alexa_Pulido%204/img/og-image.jpg">
  <title>Login admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="mb-3">Ingreso Administrador</h5>
            <?php if ($error): ?>
              <div class="alert alert-danger py-2"><?php echo e($error); ?></div>
            <?php endif; ?>
            <form method="post">
              <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
              <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input name="username" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <button class="btn btn-primary w-100">Ingresar</button>
              <p class="text-muted small mt-3 mb-0">Serás redirigido al panel de administración.</p>
            </form>
          </div>
        </div>
        <p class="text-center small mt-3"><a href="/terminal/index.php">← Volver al sitio</a></p>
      </div>
    </div>
  </div>
</body>
</html>