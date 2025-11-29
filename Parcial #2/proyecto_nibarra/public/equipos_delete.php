<?php
// public/equipos_delete.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/replicate.php';
require_once __DIR__ . '/../includes/auth.php';
check_auth();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    header('Location: equipos_list.php');
    exit;
}

try {
    $stmt = $pdoLocal->prepare("DELETE FROM equipos WHERE id = ?");
    $stmt->execute([$id]);

    // intentar borrar remoto también
    delete_remote_row($pdoRemote, 'equipos', $id);

    header('Location: equipos_list.php');
    exit;
} catch (Exception $e) {
    // en caso de error, redirige con mensaje (simple)
    header('Location: equipos_list.php?error=' . urlencode($e->getMessage()));
    exit;
}
