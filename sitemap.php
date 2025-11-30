<?php
header("Content-Type: application/xml; charset=utf-8");
require 'auth.php';
$pdo = get_db();

echo "<?xml version='1.0' encoding='UTF-8'?>";
echo "<urlset>";

$pages = ['index','terms','privacy','faq','changelog','hunt_builder','mitre_groups'];
foreach ($pages as $p) {
  echo "<url><loc>https://".$_SERVER['HTTP_HOST']."/$p</loc></url>";
}

$stmt = $pdo->query("SELECT id,name FROM apt_groups");
foreach ($stmt as $a) {
  $slug = strtolower(str_replace(' ','-',$a['name']));
  echo "<url><loc>https://".$_SERVER['HTTP_HOST']."/apt/$slug</loc></url>";
}

$stmt = $pdo->query("SELECT name FROM malware_families");
foreach ($stmt as $m) {
  $slug = strtolower($m['name']);
  echo "<url><loc>https://".$_SERVER['HTTP_HOST']."/malware/$slug</loc></url>";
}

echo "</urlset>";
?>
