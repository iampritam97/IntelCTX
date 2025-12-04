<?php
require_once 'db.php';
$pdo = get_db();

$q = trim($_GET['q'] ?? '');
if ($q === '') { echo json_encode([]); exit; }
$like = "%$q%";

$stmt = $pdo->prepare("
  (SELECT 'apt' as type, id, name FROM apt_groups WHERE name LIKE ? LIMIT 5)
  UNION
  (SELECT 'mal' as type, id, name FROM malware_families WHERE name LIKE ? LIMIT 5)
  UNION
  (SELECT 'tool' as type, id, name FROM threat_tools WHERE name LIKE ? LIMIT 5)
  LIMIT 12
");
$stmt->execute([$like,$like,$like]);
$res = $stmt->fetchAll();

echo json_encode($res);
