<?php
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/replicate.php';
require_once __DIR__.'/../includes/auth.php';
check_auth();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdoLocal->prepare("DELETE FROM equipos WHERE id = ?");
    $stmt->execute([$id]);
    delete_remote_row($pdoRemote, 'equipos', $id);
    header('Location: equipos_list.php');
    exit;
}

$rows = $pdoLocal->query("SELECT * FROM equipos ORDER BY ingreso_fecha DESC")->fetchAll();
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="container mt-4">
  <h3>Equipos</h3>
  <a class="btn btn-primary mb-2" href="equipos_add.php">Nuevo</a>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th><th>Ingreso</th><th>Equipo</th><th>Tipo</th><th>Estado</th><th>Progreso</th><th>Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($rows as $r): 
      $prog = intval($r['progreso']);
      if ($prog >= 100) $pb = 'bg-primary';
      elseif ($prog >= 50) $pb = 'bg-success';
      elseif ($prog >= 25) $pb = 'bg-warning';
      else $pb = 'bg-danger';
    ?>
      <tr>
        <td><?= $r['id'] ?></td>
        <td><?= htmlspecialchars($r['ingreso_fecha']) ?></td>
        <td><?= htmlspecialchars($r['equipo']) ?></td>
        <td><?= htmlspecialchars($r['tipo_mantenimiento']) ?></td>
        <td><?= htmlspecialchars($r['estado']) ?></td>
        <td>
          <div class="progress" style="height:18px">
            <div class="progress-bar <?= $pb ?>" role="progressbar" style="width:<?=$prog?>%"><?=$prog?>%</div>
          </div>
        </td>
        <td>
          <a href="equipos_edit.php?id=<?=$r['id']?>" class="btn btn-sm btn-warning">Editar</a>
          <a href="?delete=<?=$r['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Borrar registro?')">Borrar</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>