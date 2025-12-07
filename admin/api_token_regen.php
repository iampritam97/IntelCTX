<?php
require_once __DIR__ . '/../auth.php';
require_login();
$pdo = get_db();

$id = (int)($_GET['id'] ?? 0);

$new_token = bin2hex(random_bytes(32));

$pdo->prepare("UPDATE api_tokens SET token=?, last_used=NULL WHERE id=?")
    ->execute([$new_token, $id]);

echo "<h2>New Token:</h2>";
echo "<p style='font-family:monospace;font-size:14px;'>$new_token</p>";
echo "<a href='api_tokens.php'>Back</a>";
exit;
