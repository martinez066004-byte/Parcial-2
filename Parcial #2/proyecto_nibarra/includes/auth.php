<?php
// includes/auth.php
session_start();

function check_auth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /public/login.php');
        exit;
    }
}

function login_user($pdo, $username, $password) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if (!$user) return false;
    $pass_hash_sql = $user['password_hash'];
    if (hash('sha256', $password) === $pass_hash_sql) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nombre'] = $user['nombre'];
        return true;
    }
    return false;
}
