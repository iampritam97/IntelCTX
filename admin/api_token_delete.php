<?php
require_once __DIR__ . '/../auth.php';
require_login();
$pdo = get_db();

$id = (int)($_GET['id'] ?? 0);

$pdo->prepare("DELETE FROM api_tokens WHERE id=?")->execute([$id]);

header("Location: api_tokens.php");
exit;
