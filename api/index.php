<?php
require_once __DIR__ . '/../db.php';
header("Content-Type: application/json");

$pdo = get_db();

/*
|--------------------------------------------------------------------------
| STEP 1 — Validate API Token
|--------------------------------------------------------------------------
*/

$token = $_GET['token'] ?? null;

if (!$token) {
    echo json_encode(["error" => "Missing API token"]);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM api_tokens WHERE token = ? AND active = 1");
$stmt->execute([$token]);
$tk = $stmt->fetch();

if (!$tk) {
    echo json_encode(["error" => "Invalid or inactive token"]);
    exit;
}

/* Check expiry */
if ($tk['expires_at'] && strtotime($tk['expires_at']) < time()) {
    echo json_encode(["error" => "Token expired"]);
    exit;
}

/* Token scopes (default = read) */
$scopes = explode(",", $tk['scopes']);

function require_scope($required, $scopes) {
    if (!in_array($required, $scopes) && !in_array("admin", $scopes)) {
        echo json_encode(["error" => "Missing required scope: $required"]);
        exit;
    }
}

/* Update last used timestamp */
$pdo->prepare("UPDATE api_tokens SET last_used = NOW() WHERE token = ?")
    ->execute([$token]);


/*
|--------------------------------------------------------------------------
| STEP 2 — Log API access
|--------------------------------------------------------------------------
*/
$log = $pdo->prepare("
    INSERT INTO api_logs (token, endpoint, ip_address, user_agent)
    VALUES (?, ?, ?, ?)
");

$log->execute([
    $token,
    $_GET['resource'] ?? '',
    $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
]);


/*
|--------------------------------------------------------------------------
| STEP 3 — Router
|--------------------------------------------------------------------------
*/

$resource = $_GET['resource'] ?? '';

switch ($resource) {

    case "apt":
        require_scope("read", $scopes);
        $stmt = $pdo->query("SELECT * FROM apt_groups ORDER BY risk_score DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
        break;

    case "malware":
        require_scope("read", $scopes);
        $stmt = $pdo->query("SELECT * FROM malware_families ORDER BY id DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
        break;

    case "tools":
        require_scope("read", $scopes);
        $stmt = $pdo->query("SELECT * FROM threat_tools ORDER BY id DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
        break;

    case "all":
        require_scope("export", $scopes);
        echo json_encode([
            "apt_groups" => $pdo->query("SELECT * FROM apt_groups")->fetchAll(PDO::FETCH_ASSOC),
            "malware"    => $pdo->query("SELECT * FROM malware_families")->fetchAll(PDO::FETCH_ASSOC),
            "tools"      => $pdo->query("SELECT * FROM threat_tools")->fetchAll(PDO::FETCH_ASSOC)
        ], JSON_PRETTY_PRINT);
        break;

    default:
        echo json_encode(["error" => "Unknown API resource"]);
        break;
}
