<?php
require_once __DIR__.'/../includes/config.php';
header('Content-Type: application/json');
$events = [];
$stmt = $pdoLocal->query("SELECT id,equipo,ingreso_fecha,salida_fecha,estado FROM equipos");
while ($r = $stmt->fetch()) {
    $events[] = [
        'id' => $r['id'].'-ingreso',
        'title' => "Ingreso: ".$r['equipo']." ({$r['estado']})",
        'start' => $r['ingreso_fecha'],
    ];
    if ($r['salida_fecha']) {
        $events[] = [
            'id' => $r['id'].'-salida',
            'title' => "Salida: ".$r['equipo'],
            'start' => $r['salida_fecha'],
        ];
    }
}
echo json_encode($events);
