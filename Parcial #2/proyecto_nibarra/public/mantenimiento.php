<?php
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/auth.php';
check_auth();
$estados = ['por hacer','espera material','en revision','terminada'];
include __DIR__ . '/../includes/header.php';
?>

<div class="container mt-4">
  <h3>Mantenimiento - Tablero</h3>
  <div class="row">
    <?php foreach($estados as $estado): ?>
      <div class="col-md-3 mb-3">
        <div class="card p-3">
          <h5 class="mb-3 text-center"><?= ucfirst($estado) ?></h5>
          <div class="col-kanban" id="<?=str_replace(' ','_',$estado)?>">
            <?php
              $stmt = $pdoLocal->prepare("SELECT * FROM equipos WHERE estado = ? ORDER BY ingreso_fecha DESC");
              $stmt->execute([$estado]);
              while($r = $stmt->fetch()) {
                $prog = intval($r['progreso']);
                if ($prog >= 100) $pb = 'bg-primary';
                elseif ($prog >= 50) $pb = 'bg-success';
                elseif ($prog >= 25) $pb = 'bg-warning';
                else $pb = 'bg-danger';
                echo "<div class='item'>";
                echo "<strong>".htmlspecialchars($r['equipo'])."</strong><br>";
                echo "Tipo: ".$r['tipo_mantenimiento']."<br>";
                echo "<div class='progress' style='height:14px'><div class='progress-bar {$pb}' role='progressbar' style='width:{$r['progreso']}%'>{$r['progreso']}%</div></div>";
                echo "<a class='btn btn-sm btn-link' href='equipos_edit.php?id={$r['id']}'>Editar</a>";
                echo "</div>";
              }
            ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
