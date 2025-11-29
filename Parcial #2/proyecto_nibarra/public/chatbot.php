<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
check_auth();
include __DIR__ . '/../includes/header.php';
?>

<div class="container mt-4">
  <h3>Chat Bot - Soporte</h3>
  <div id="chatbox" style="height:300px; overflow:auto; background:#f5f5f5; padding:10px; border-radius:6px"></div>
  <div class="input-group mt-2">
    <input id="msg" class="form-control" placeholder="Escribe tu pregunta...">
    <button id="send" class="btn btn-primary">Enviar</button>
  </div>
</div>

<!-- CORREGIDO: ruta completa -->
<script src="../public/assets/js/chatbot.js"></script>

<?php include __DIR__ . '/../includes/footer.php'; ?>