<?php
// admin/graph_data.php
// require_once __DIR__ . '/../auth.php';
// require_login();
require_once __DIR__ . '/db.php';
$pdo = get_db();

// Build graph on-the-fly (apt, malware, tools)
$apts = $pdo->query("SELECT id, name, country, risk_score, mitre_group_id, aliases, malware_families, tools, targeted_industries, active_from, active_to FROM apt_groups")->fetchAll(PDO::FETCH_ASSOC);

$nodes = [];
$edges = [];
$nodeIndex = [];
$nextId = 1;

function push_node(&$nodes, &$nodeIndex, &$nextId, $key, $label, $type, $meta=[]) {
    if (isset($nodeIndex[$key])) return $nodeIndex[$key];
    $nid = 'n' . $nextId++;
    $nodeIndex[$key] = $nid;
    $nodes[] = array_merge([
        'id' => $nid,
        'key' => $key,
        'label' => $label,
        'type' => $type
    ], $meta);
    return $nid;
}

function split_list($s) {
    if (!$s) return [];
    $parts = preg_split('/[\r\n,;]+/', $s);
    $out = [];
    foreach ($parts as $p) {
        $p = trim($p);
        if ($p !== '') $out[] = $p;
    }
    return array_values(array_unique($out));
}

// Add APT nodes (and their malware & tool edges)
foreach ($apts as $apt) {
    $meta = [
        'apt_id' => (int)$apt['id'],
        'country' => $apt['country'] ?? '',
        'risk' => isset($apt['risk_score']) ? (int)$apt['risk_score'] : 0,
        'mitre' => $apt['mitre_group_id'] ?? '',
        'aliases' => $apt['aliases'] ?? '',
        'targeted_industries' => $apt['targeted_industries'] ?? '',
        'active_from' => $apt['active_from'] ?? '',
        'active_to' => $apt['active_to'] ?? ''
    ];
    $aptKey = 'apt:' . $apt['id'];
    $aptNodeId = push_node($nodes, $nodeIndex, $nextId, $aptKey, $apt['name'], 'apt', $meta);

    // malware
    $mfs = split_list($apt['malware_families'] ?? '');
    foreach ($mfs as $mf) {
        $mfKey = 'malware:' . strtolower($mf);
        $mfNodeId = push_node($nodes, $nodeIndex, $nextId, $mfKey, $mf, 'malware', ['label'=>$mf]);
        $edges[] = ['from' => $aptNodeId, 'to' => $mfNodeId, 'label' => 'uses', 'type' => 'uses'];
    }

    // tools
    $tls = split_list($apt['tools'] ?? '');
    foreach ($tls as $t) {
        $tKey = 'tool:' . strtolower($t);
        $tNodeId = push_node($nodes, $nodeIndex, $nextId, $tKey, $t, 'tool', ['label'=>$t]);
        $edges[] = ['from' => $aptNodeId, 'to' => $tNodeId, 'label' => 'uses', 'type' => 'uses'];
    }
}

// Build apt<->apt overlap edges (weighted by malware overlap; also compute tool overlap and produce fields)
$aptById = [];
foreach ($apts as $a) $aptById[$a['id']] = $a;
$aptCount = count($apts);
for ($i=0;$i<$aptCount;$i++){
    $a = $apts[$i];
    $ma = array_map('strtolower', split_list($a['malware_families'] ?? ''));
    $ta = array_map('strtolower', split_list($a['tools'] ?? ''));
    for ($j=$i+1;$j<$aptCount;$j++){
        $b = $apts[$j];
        $mb = array_map('strtolower', split_list($b['malware_families'] ?? ''));
        $tb = array_map('strtolower', split_list($b['tools'] ?? ''));
        if (empty($ma) && empty($mb) && empty($ta) && empty($tb)) continue;
        $interM = array_intersect($ma,$mb);
        $interT = array_intersect($ta,$tb);
        $unionM = array_unique(array_merge($ma,$mb));
        $unionT = array_unique(array_merge($ta,$tb));
        $malOverlap = count($unionM) ? count($interM)/count($unionM) : 0;
        $toolOverlap = count($unionT) ? count($interT)/count($unionT) : 0;
        $weight = round(max($malOverlap, $toolOverlap), 2);
        if ($weight > 0) {
            $from = $nodeIndex['apt:' . $a['id']];
            $to   = $nodeIndex['apt:' . $b['id']];
            $edges[] = ['from'=>$from,'to'=>$to,'label'=>'overlap','weight'=>$weight,'type'=>'overlap','malOverlap'=>count($interM),'toolOverlap'=>count($interT)];
        }
    }
}

// respond
header('Content-Type: application/json');
echo json_encode(['nodes'=>$nodes,'edges'=>$edges], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
exit;
