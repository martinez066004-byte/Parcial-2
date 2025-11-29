<?php
// public/logout.php
session_start();

if (!isset($_GET['confirm'])) {
  // mostrar confirmación visual con Bootstrap modal
  echo '
  <!doctype html>
  <html lang="es">
  <head>
    <meta charset="utf-8">
    <title>Confirmar cierre de sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body class="d-flex align-items-center justify-content-center vh-100">
    <div class="text-center">
      <h4>¿Seguro que deseas cerrar sesión?</h4>
      <div class="mt-3">
        <a href="logout.php?confirm=yes" class="btn btn-danger me-2">Sí, cerrar sesión</a>
        <a href="javascript:history.back()" class="btn btn-secondary">No, volver</a>
      </div>
    </div>
  </body>
  </html>
  ';
  exit;
}

if ($_GET['confirm'] === 'yes') {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header('Location: login.php');
    exit;
}