<?php
// includes/config.php
// Ajusta estos valores según tu entorno XAMPP / VM
$LOCAL_DB = [
    'host' => '127.0.0.1',
    'port' => 3306,
    'dbname' => 'nibarra',
    'user' => 'root',
    'pass' => '' // XAMPP default
];

$REMOTE_DB = [
    'host' => '192.168.56.101',
    'port' => 3306,
    'dbname' => 'nibarra',
    'user' => 'syncuser',
    'pass' => 'SyncPass123!'
];

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
];

try {
    $pdoLocal = new PDO(
        "mysql:host={$LOCAL_DB['host']};port={$LOCAL_DB['port']};dbname={$LOCAL_DB['dbname']}",
        $LOCAL_DB['user'],
        $LOCAL_DB['pass'],
        $options
    );
} catch (PDOException $e) {
    die("Error conexión local: " . $e->getMessage());
}

try {
    // Conexión remota (si no está disponible, atrapa error y continúa)
    $pdoRemote = new PDO(
        "mysql:host={$REMOTE_DB['host']};port={$REMOTE_DB['port']};dbname={$REMOTE_DB['dbname']}",
        $REMOTE_DB['user'],
        $REMOTE_DB['pass'],
        $options
    );
} catch (Exception $e) {
    // No detengas la app si la replicación remota falla; loguea error.
    $pdoRemote = null;
}
