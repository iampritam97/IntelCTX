<?php
require_once __DIR__ . '/../auth.php';
require_login();
$pdo = get_db();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id) {
    $stmt = $pdo->prepare("SELECT name FROM apt_groups WHERE id=?");
    $stmt->execute([$id]);
    $name = $stmt->fetchColumn();

    $pdo->prepare("DELETE FROM apt_groups WHERE id=?")->execute([$id]);

    if ($name) {
        $log = $pdo->prepare("INSERT INTO audit_logs (action, actor, apt_id, apt_name) VALUES (?,?,?,?)");
        $log->execute(['delete', $_SESSION['admin_username'], $id, $name]);
    }
}
header('Location: dashboard.php');
exit;
