<?php
// includes/header.php
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>NIBARRA - Gestión</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom -->
  <link href="../public/assets/css/custom.css" rel="stylesheet">

  <style>
    html, body {
      margin: 0;
      padding: 0;
    }

    .site-header {
      background: linear-gradient(90deg, #0d6efd 0%, #6610f2 100%);
      color: white;
      padding: 18px 0;
      width: 100%;
      top: 0;
      left: 0;
      z-index: 1000;
      box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    .site-header .brand {
      font-size: 1.6rem;
      font-weight: 700;
      text-decoration: none;
      color: #fff;
    }

    .site-header .subtitle {
      font-size: 0.9rem;
      opacity: 0.85;
      margin-top: -2px;
    }

    .site-header nav a {
      color: #fff;
      font-weight: 500;
      text-decoration: none;
      margin-right: 20px;
      transition: opacity 0.2s;
    }

    .site-header nav a:hover {
      opacity: 0.8;
      text-decoration: underline;
    }

    .site-header .btn-outline-light {
      border-radius: 6px;
      font-weight: 500;
      padding: 4px 12px;
    }
  </style>
</head>

<body>
  <header class="site-header">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">
      <div>
        <a href="index.php" class="brand">NIBARRA</a>
        <div class="subtitle">Sistema de Gestión de Mantenimientos</div>
      </div>
      <nav class="d-flex align-items-center">
        <a href="index.php">Dashboard</a>
        <a href="equipos_list.php">Equipos</a>
        <a href="calendario.php">Calendario</a>
        <a href="mantenimiento.php">Mantenimiento</a>
        <a href="chatbot.php">ChatBot</a>
        <a href="logout.php" class="btn btn-sm btn-outline-light">Salir</a>
      </nav>
    </div>
  </header>