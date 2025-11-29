<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/replicate.php';
require_once __DIR__ . '/../includes/auth.php';
check_auth();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    header('Location: equipos_list.php');
    exit;
}

$stmt = $pdoLocal->prepare("SELECT * FROM equipos WHERE id = ?");
$stmt->execute([$id]);
$eq = $stmt->fetch();
if (!$eq) {
    header('Location: equipos_list.php');
    exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ingreso_fecha = $_POST['ingreso_fecha'] ?? null;
    $salida_fecha = $_POST['salida_fecha'] ?? null;
    $equipo = $_POST['equipo'] ?? '';
    $marca = $_POST['marca'] ?? '';
    $serie = $_POST['serie'] ?? '';
    $tipo_servicio = $_POST['tipo_servicio'] ?? '';
    $tipo_mantenimiento = $_POST['tipo_mantenimiento'] ?? 'preventivo';
    $estado = $_POST['estado'] ?? 'por hacer';
    $costo_inicial = $_POST['costo_inicial'] ?? 0;
    $costo_final = $_POST['costo_final'] ?? 0;
    $observacion = $_POST['observacion'] ?? '';
    $progreso = intval($_POST['progreso'] ?? 0);
    if ($progreso < 0) $progreso = 0;
    if ($progreso > 100) $progreso = 100;

    $sql = "UPDATE equipos SET ingreso_fecha=?, salida_fecha=?, equipo=?, marca=?, serie=?, tipo_servicio=?, tipo_mantenimiento=?, estado=?, costo_inicial=?, costo_final=?, observacion=?, progreso=? WHERE id=?";
    $stmt = $pdoLocal->prepare($sql);
    try {
        $stmt->execute([$ingreso_fecha, $salida_fecha ?: null, $equipo, $marca, $serie, $tipo_servicio, $tipo_mantenimiento, $estado, $costo_inicial, $costo_final, $observacion, $progreso, $id]);
        replicate_row($pdoLocal, $pdoRemote, 'equipos', $id);
        $msg = "Guardado correctamente.";
        $stmt2 = $pdoLocal->prepare("SELECT * FROM equipos WHERE id = ?");
        $stmt2->execute([$id]);
        $eq = $stmt2->fetch();
    } catch (Exception $e) {
        $msg = "Error: " . $e->getMessage();
    }
}

$estados = ['por hacer', 'espera material', 'en revision', 'terminada'];
$tipos = ['preventivo','predictivo','correctivo'];
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="container mt-4">
  <h3>Editar equipo #<?= $id ?></h3>
  <?php if ($msg): ?><div class="alert alert-info"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

  <form id="editForm" method="post" class="mb-4">
    <div class="row">
      <div class="col-md-3 mb-2">
        <label>Ingreso</label>
        <input type="date" name="ingreso_fecha" class="form-control" value="<?= htmlspecialchars($eq['ingreso_fecha']) ?>" required>
      </div>
      <div class="col-md-3 mb-2">
        <label>Salida</label>
        <input type="date" name="salida_fecha" class="form-control" value="<?= htmlspecialchars($eq['salida_fecha']) ?>">
      </div>
      <div class="col-md-6 mb-2">
        <label>Equipo</label>
        <input type="text" name="equipo" class="form-control" value="<?= htmlspecialchars($eq['equipo']) ?>" required>
      </div>
    </div>

    <div class="row">
      <div class="col-md-3 mb-2">
        <label>Marca</label>
        <input type="text" name="marca" class="form-control" value="<?= htmlspecialchars($eq['marca']) ?>">
      </div>
      <div class="col-md-3 mb-2">
        <label>Serie</label>
        <input type="text" name="serie" class="form-control" value="<?= htmlspecialchars($eq['serie']) ?>">
      </div>
      <div class="col-md-3 mb-2">
        <label>Tipo servicio</label>
        <input type="text" name="tipo_servicio" class="form-control" value="<?= htmlspecialchars($eq['tipo_servicio']) ?>">
      </div>
      <div class="col-md-3 mb-2">
        <label>Tipo mantenimiento</label>
        <select name="tipo_mantenimiento" class="form-control">
          <?php foreach ($tipos as $t): ?>
            <option value="<?= $t ?>" <?= ($eq['tipo_mantenimiento'] === $t) ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="row">
      <div class="col-md-3 mb-2">
        <label>Estado</label>
        <select id="estadoSelect" name="estado" class="form-control">
          <?php foreach ($estados as $e): ?>
            <option value="<?= $e ?>" <?= ($eq['estado'] === $e) ? 'selected' : '' ?>><?= ucfirst($e) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3 mb-2">
        <label>Progreso (%)</label>
        <input id="progresoInput" type="number" min="0" max="100" name="progreso" class="form-control" value="<?= intval($eq['progreso']) ?>">
      </div>
      <div class="col-md-3 mb-2">
        <label>Costo inicial</label>
        <input type="number" step="0.01" name="costo_inicial" class="form-control" value="<?= htmlspecialchars($eq['costo_inicial']) ?>">
      </div>
      <div class="col-md-3 mb-2">
        <label>Costo final</label>
        <input type="number" step="0.01" name="costo_final" class="form-control" value="<?= htmlspecialchars($eq['costo_final']) ?>">
      </div>
    </div>

    <div class="mb-2">
      <label>Observación</label>
      <textarea name="observacion" class="form-control"><?= htmlspecialchars($eq['observacion']) ?></textarea>
    </div>

    <input type="hidden" id="originalProgress" name="original_progress" value="<?= intval($eq['progreso']) ?>">

    <div class="mb-3">
      <button class="btn btn-success">Guardar cambios</button>
      <a href="equipos_list.php" class="btn btn-secondary">Volver</a>
      <a href="equipos_delete.php?id=<?= $id ?>" class="btn btn-danger" onclick="return confirm('¿Eliminar registro? Esto borrará también en el servidor remoto si existe.')">Eliminar</a>
    </div>
  </form>
</div>

<script>
function rangeFor(p) {
  if (p >= 100) return 'terminada';
  if (p >= 50) return 'en revision';
  if (p >= 25) return 'espera material';
  return 'por hacer';
}

document.getElementById('editForm').addEventListener('submit', function(e){
  const orig = Number(document.getElementById('originalProgress').value || 0);
  const nuevo = Number(document.getElementById('progresoInput').value || 0);
  const origRange = rangeFor(orig);
  const newRange = rangeFor(nuevo);

  if (origRange !== newRange) {
    e.preventDefault();
    const msg = `El progreso cambió de rango (${orig}% → ${nuevo}%).\n¿Deseas cambiar el estado a "${newRange}"?`;
    if (confirm(msg)) {
      document.getElementById('estadoSelect').value = newRange;
    }
    document.getElementById('originalProgress').value = nuevo;
    this.submit();
  }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>