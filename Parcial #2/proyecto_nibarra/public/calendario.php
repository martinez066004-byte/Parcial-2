<?php
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/auth.php';
check_auth();
include __DIR__ . '/../includes/header.php';

// Navegación de meses
$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$year  = isset($_GET['year'])  ? intval($_GET['year'])  : date('Y');
if ($month < 1)  { $month = 12; $year--; }
if ($month > 12) { $month = 1;  $year++; }

$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$firstDay    = date('N', strtotime("$year-$month-01")); // 1 (Mon) .. 7 (Sun)
$today       = date('Y-m-d');

// Traer equipos (incluye ingreso, salida, estado, tipo, progreso)
$stmt = $pdoLocal->query("SELECT id, equipo, ingreso_fecha, salida_fecha, estado, tipo_mantenimiento, progreso FROM equipos");
$equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// cálculo meses para navegación
$prevMonth = $month - 1; $nextMonth = $month + 1;
$prevYear = $year; $nextYear = $year;
if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }
if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }
?>

<!-- INICIO: contenido del calendario (no repetir HEAD/BODY) -->
<div class="container mt-4" style="margin-top:2.8rem;">
  <h3>Calendario de Mantenimientos</h3>

  <div class="calendar-container" style="background:#fff;padding:20px;border-radius:12px;box-shadow:0 6px 16px rgba(0,0,0,0.06);">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <a class="btn btn-outline-primary btn-sm" href="?month=<?=$prevMonth?>&year=<?=$prevYear?>">&lt; Mes anterior</a>
      <h5 class="mb-0"><?= strftime('%B %Y', strtotime("$year-$month-01")) ?></h5>
      <a class="btn btn-outline-primary btn-sm" href="?month=<?=$nextMonth?>&year=<?=$nextYear?>">Mes siguiente &gt;</a>
    </div>

    <div class="calendar-grid text-center fw-bold text-secondary mb-2" style="display:grid;grid-template-columns:repeat(7,1fr);gap:6px;">
      <div>Lun</div><div>Mar</div><div>Mié</div><div>Jue</div><div>Vie</div><div>Sáb</div><div>Dom</div>
    </div>

    <!-- Grid con celdas que contienen data-date="YYYY-MM-DD" -->
    <div id="cal-grid" class="calendar-grid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:6px;">
      <?php
      // espacios vacíos antes del primer día de la semana
      for ($i = 1; $i < $firstDay; $i++) {
          echo "<div></div>";
      }

      for ($d = 1; $d <= $daysInMonth; $d++) {
          $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $d);
          $isToday = ($dateStr === $today) ? 'today' : '';
          echo "<div class='calendar-day day-box {$isToday}' data-date='{$dateStr}' style='position:relative;min-height:110px;border:1px solid #e9ecef;border-radius:8px;background:#fafafa;padding:6px;overflow:visible;'>";
          echo "<div class='day-num' style='font-weight:600;color:#0d6efd;margin-bottom:6px;'>$d</div>";
          echo "</div>";
      }
      ?>
    </div>
  </div>
</div>

<!-- Tooltip único -->
<div id="cal-tooltip" style="position:fixed;display:none;z-index:9999;background:rgba(0,0,0,0.85);color:#fff;padding:8px 10px;border-radius:6px;font-size:13px;max-width:320px;"></div>

<script>
// Equipos desde PHP (fechas en formato YYYY-MM-DD)
const equipos = <?= json_encode($equipos, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>;

// Helper: formatea Date a YYYY-MM-DD (uso local)
function dateToYMD(d) {
  const yyyy = d.getFullYear();
  const mm = String(d.getMonth()+1).padStart(2,'0');
  const dd = String(d.getDate()).padStart(2,'0');
  return `${yyyy}-${mm}-${dd}`;
}

// Añade eventos a las celdas por data-date (incluyendo inclusive la fecha de salida)
function applyEvents() {
  // limpiar previos (por si se llama varias veces)
  document.querySelectorAll('#cal-grid .event-dot, #cal-grid .event-line').forEach(el => el.remove());

  equipos.forEach(eq => {
    if (!eq.ingreso_fecha) return;
    const start = new Date(eq.ingreso_fecha + 'T00:00:00');
    const end = eq.salida_fecha ? new Date(eq.salida_fecha + 'T00:00:00') : new Date(eq.ingreso_fecha + 'T00:00:00');

    // Si estado terminada -> show green dot on salida (if salida exists), else still show dot on ingreso
    const isTerminated = (String(eq.estado || '').toLowerCase() === 'terminada');

    if (isTerminated && eq.salida_fecha) {
      const cell = document.querySelector(`#cal-grid [data-date="${eq.salida_fecha}"]`);
      if (cell) {
        const dot = document.createElement('div');
        dot.className = 'event-dot finished';
        dot.style.position = 'absolute';
        dot.style.left = '8px';
        dot.style.bottom = '8px';
        dot.style.zIndex = 50;
        dot.style.width = '10px';
        dot.style.height = '10px';
        dot.style.borderRadius = '50%';
        dot.style.background = '#22c55e';
        dot.dataset.info = `${eq.equipo} | Estado: ${eq.estado} | Tipo: ${eq.tipo_mantenimiento} | Progreso: ${eq.progreso}% | Ingreso: ${eq.ingreso_fecha} | Salida: ${eq.salida_fecha}`;
        cell.appendChild(dot);
      }
      return; // no dibujar línea si está terminada
    }

    if (eq.salida_fecha && eq.salida_fecha !== eq.ingreso_fecha) {
      // Línea roja desde ingreso hasta salida (incluye ambos extremos)
      // Recorremos fechas desde start hasta end inclusive
      for (let d = new Date(start.getTime()); d <= end; d.setDate(d.getDate()+1)) {
        const dayStr = dateToYMD(d);
        const cell = document.querySelector(`#cal-grid [data-date="${dayStr}"]`);
        if (cell) {
          // colocamos la línea como capa (no sobre los puntos)
          const line = document.createElement('div');
          line.className = 'event-line';
          line.style.position = 'absolute';
          line.style.left = '6%';
          line.style.right = '6%';
          line.style.height = '8px';
          // color rojo
          line.style.background = '#dc3545';
          // stackear líneas: contamos cuántas ya hay en la celda para desplazar cada una
          const existingLines = cell.querySelectorAll('.event-line').length;
          line.style.top = (40 + existingLines*12) + 'px';
          line.style.borderRadius = '6px';
          line.style.zIndex = 20;
          line.dataset.info = `${eq.equipo} | Estado: ${eq.estado} | Tipo: ${eq.tipo_mantenimiento} | Progreso: ${eq.progreso}% | Ingreso: ${eq.ingreso_fecha} | Salida: ${eq.salida_fecha}`;
          cell.appendChild(line);
        }
      }
      return;
    }

    // Caso: solo ingreso (o ingreso == salida) -> punto amarillo en la fecha de ingreso
    const cell = document.querySelector(`#cal-grid [data-date="${eq.ingreso_fecha}"]`);
    if (cell) {
      const dot = document.createElement('div');
      dot.className = 'event-dot';
      dot.style.position = 'absolute';
      dot.style.left = '8px';
      dot.style.bottom = '8px';
      dot.style.zIndex = 15;
      dot.style.width = '10px';
      dot.style.height = '10px';
      dot.style.borderRadius = '50%';
      dot.style.background = '#f59e0b';
      dot.dataset.info = `${eq.equipo} | Estado: ${eq.estado} | Tipo: ${eq.tipo_mantenimiento} | Progreso: ${eq.progreso}% | Ingreso: ${eq.ingreso_fecha}`;
      cell.appendChild(dot);
    }
  });
}

function attachTooltips() {
  const tooltip = document.getElementById('cal-tooltip');
  // delegación: mouseover en grid
  const grid = document.getElementById('cal-grid');
  grid.addEventListener('mouseover', function(e){
    const el = e.target.closest('.event-dot, .event-line');
    if (!el) return;
    const info = el.dataset.info || '';
    if (!info) return;
    tooltip.innerHTML = info;
    tooltip.style.display = 'block';
    tooltip.style.left = (e.pageX + 12) + 'px';
    tooltip.style.top  = (e.pageY + 12) + 'px';
    el.addEventListener('mousemove', moveHandler);
    el.addEventListener('mouseleave', leaveHandler, {once:true});
    function moveHandler(evt) {
      tooltip.style.left = (evt.pageX + 12) + 'px';
      tooltip.style.top  = (evt.pageY + 12) + 'px';
    }
    function leaveHandler() {
      tooltip.style.display = 'none';
      el.removeEventListener('mousemove', moveHandler);
    }
  });
}

// Ejecutar
applyEvents();
attachTooltips();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>