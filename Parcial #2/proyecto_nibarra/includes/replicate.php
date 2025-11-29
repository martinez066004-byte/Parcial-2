<?php
// includes/replicate.php
// Requiere $pdoRemote y $pdoLocal disponibles desde config.php
function replicate_row($pdoLocal, $pdoRemote, $table, $id) {
    if (!$pdoRemote) return false;
    // selecciona la fila local
    $stmt = $pdoLocal->prepare("SELECT * FROM {$table} WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) return false;

    // prepara columnas y valores
    $cols = array_keys($row);
    $placeholders = array_map(fn($c) => ":{$c}", $cols);

    // intenta UPDATE, si NO existe -> INSERT (UPSERT)
    $sql = "REPLACE INTO {$table} (" . implode(",", $cols) . ") VALUES (" . implode(",", $placeholders) . ")";
    $stmt2 = $pdoRemote->prepare($sql);
    foreach ($row as $c => $v) {
        $stmt2->bindValue(":{$c}", $v);
    }
    return $stmt2->execute();
}

function delete_remote_row($pdoRemote, $table, $id) {
    if (!$pdoRemote) return false;
    $stmt = $pdoRemote->prepare("DELETE FROM {$table} WHERE id = ?");
    return $stmt->execute([$id]);
}
