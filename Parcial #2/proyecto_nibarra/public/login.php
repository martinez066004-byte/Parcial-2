<?php
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/auth.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = trim($_POST['password'] ?? '');
    if (login_user($pdoLocal, $u, $p)) {
        header('Location: index.php');
        exit;
    } else {
        $msg = "Usuario o contraseña incorrectos";
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Acceso - NIBARRA</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      height: 100vh;
      background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Inter', sans-serif;
    }
    .login-card {
      background: #fff;
      padding: 2.5rem;
      border-radius: 1rem;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 420px;
    }
    .login-card h3 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: #0d6efd;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <h3><strong>NIBARRA</strong><br><small class="text-muted">Gestión de Mantenimientos</small></h3>
    <?php if($msg): ?>
      <div class="alert alert-danger text-center"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="form-floating mb-3">
        <input type="text" name="username" class="form-control" id="username" placeholder="Usuario" required>
        <label for="username">Usuario</label>
      </div>
      <div class="form-floating mb-3">
        <input type="password" name="password" class="form-control" id="password" placeholder="Contraseña" required>
        <label for="password">Contraseña</label>
      </div>
      <button class="btn btn-primary w-100 py-2">Iniciar Sesión</button>
    </form>
  </div>
</body>
</html>