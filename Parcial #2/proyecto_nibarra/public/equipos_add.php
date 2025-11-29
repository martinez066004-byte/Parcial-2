<?php
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/replicate.php';
require_once __DIR__.'/../includes/auth.php';
check_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ingreso_fecha = $_POST['ingreso_fecha'];
    $equipo = $_POST['equipo'];
    $marca = $_POST['marca'];
    $serie = $_POST['serie'];
    $tipo_servicio = $_POST['tipo_servicio'];
    $tipo_mantenimiento = $_POST['tipo_mantenimiento'];
    $costo_inicial = $_POST['costo_inicial'] ?: 0;
    $observacion = $_POST['observacion'] ?: '';
    $stmt = $pdoLocal->prepare("INSERT INTO equipos (ingreso_fecha,equipo,marca,serie,tipo_servicio,tipo_mantenimiento,costo_inicial,observacion) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->execute([$ingreso_fecha,$equipo,$marca,$serie,$tipo_servicio,$tipo_mantenimiento,$costo_inicial,$observacion]);
    $id = $pdoLocal->lastInsertId();
    // replicar
    replicate_row($pdoLocal, $pdoRemote, 'equipos', $id);
    header('Location: equipos_list.php');
    exit;
}
?>

<!doctype html><html><head><meta charset="utf-8"><title>Agregar Equipo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="p-4">
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="container">
  <h3>Nuevo Ingreso</h3>
  <form method="post">
    <input name="ingreso_fecha" class="form-control mb-2" type="date" required>
    <input name="equipo" class="form-control mb-2" placeholder="Equipo" required>
    <input name="marca" class="form-control mb-2" placeholder="Marca">
    <input name="serie" class="form-control mb-2" placeholder="Serie">
    <input name="tipo_servicio" class="form-control mb-2" placeholder="Tipo de servicio">
    <select name="tipo_mantenimiento" class="form-control mb-2">
      <option value="preventivo">Preventivo</option>
      <option value="predictivo">Predictivo</option>
      <option value="correctivo">Correctivo</option>
    </select>
    <input name="costo_inicial" class="form-control mb-2" placeholder="Costo inicial">
    <textarea name="observacion" class="form-control mb-2" placeholder="Observación"></textarea>
    <button class="btn btn-success">Guardar</button>
  </form>
</div>
</body></html>

<?php include __DIR__ . '/../includes/footer.php'; ?>