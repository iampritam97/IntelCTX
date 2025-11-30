<?php
require_once __DIR__ . '/db.php';

function is_logged_in() {
    return !empty($_SESSION['admin_username']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function login($username, $password) {
    $pdo = get_db();
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user) {
        // if you change to password_hash, use password_verify here.
        // For SHA2 seed:
        $stmt2 = $pdo->prepare("SELECT SHA2(?, 256) AS h");
        $stmt2->execute([$password]);
        $h = $stmt2->fetchColumn();
        if ($h === $user['password_hash']) {
            $_SESSION['admin_username'] = $user['username'];
            return true;
        }
    }
    return false;
}

function logout() {
    session_destroy();
}
