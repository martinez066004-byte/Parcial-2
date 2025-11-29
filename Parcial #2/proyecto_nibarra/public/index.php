<?php
// public/index.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
check_auth();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Dashboard - NIBARRA</h2>
    <div>Bienvenido, <strong><?= htmlspecialchars($_SESSION['nombre'] ?? $_SESSION['username'] ?? 'Usuario') ?></strong></div>
  </div>

  <?php
  // estadísticas rápidas
  try {
    $total = $pdoLocal->query("SELECT COUNT(*) FROM equipos")->fetchColumn();
    $por_hacer = $pdoLocal->prepare("SELECT COUNT(*) FROM equipos WHERE estado = ?");
    $por_hacer->execute(['por hacer']);
    $por_hacer = $por_hacer->fetchColumn();

    $en_revision = $pdoLocal->prepare("SELECT COUNT(*) FROM equipos WHERE estado = ?");
    $en_revision->execute(['en revision']);
    $en_revision = $en_revision->fetchColumn();

    $terminada = $pdoLocal->prepare("SELECT COUNT(*) FROM equipos WHERE estado = ?");
    $terminada->execute(['terminada']);
    $terminada = $terminada->fetchColumn();

    $ultima = $pdoLocal->query("SELECT * FROM equipos ORDER BY creado_at DESC LIMIT 1")->fetch();
  } catch (Exception $e) {
    $total = $por_hacer = $en_revision = $terminada = 0;
    $ultima = null;
  }
  ?>

  <div class="row">
    <div class="col-md-3 mb-3">
      <div class="card p-3">
        <h5 class="card-title">Total equipos</h5>
        <div class="display-6"><?= intval($total) ?></div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card p-3">
        <h5 class="card-title">Por hacer</h5>
        <div class="display-6"><?= intval($por_hacer) ?></div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card p-3">
        <h5 class="card-title">En revisión</h5>
        <div class="display-6"><?= intval($en_revision) ?></div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card p-3">
        <h5 class="card-title">Terminadas</h5>
        <div class="display-6"><?= intval($terminada) ?></div>
      </div>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-8">
      <div class="card p-3">
        <h5>Último registro</h5>
        <?php if ($ultima): ?>
          <p><strong>Equipo:</strong> <?= htmlspecialchars($ultima['equipo']) ?></p>
          <p><strong>Ingreso:</strong> <?= $ultima['ingreso_fecha'] ?></p>
          <p><strong>Estado:</strong> <?= $ultima['estado'] ?> — <strong>Progreso:</strong> <?= $ultima['progreso'] ?>%</p>
          <a href="equipos_edit.php?id=<?= $ultima['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
        <?php else: ?>
          <p>No hay registros aún.</p>
        <?php endif; ?>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card p-3 trabajos-caja">
        <h5>Trabajos recientes</h5>
        <ul class="list-unstyled mb-0">
          <?php
            $stmt = $pdoLocal->query("SELECT id,equipo,estado,progreso FROM equipos ORDER BY actualizado_at DESC, creado_at DESC LIMIT 5");
            while ($r = $stmt->fetch()) {
              echo '<li class="mb-2"><strong>' . htmlspecialchars($r['equipo']) . '</strong><br>';
              echo '<small>' . $r['estado'] . ' — ' . $r['progreso'] . '%</small></li>';
            }
          ?>
        </ul>
      </div>
    </div>
  </div>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
