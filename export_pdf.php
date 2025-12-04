<?php
require_once 'db.php';
require_once 'vendor/dompdf/autoload.php';

use Dompdf\Dompdf;

$pdo = get_db();
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM apt_groups WHERE id=?");
$stmt->execute([$id]);
$apt = $stmt->fetch();

if (!$apt) {
    die("Invalid APT ID");
}

$html = '
<!DOCTYPE html>
<html>
<head>
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
h1 { font-size: 22px; margin-bottom: 10px; color: #2563EB; }
h2 { font-size: 16px; margin-top: 20px; color: #111; }
.section { margin-bottom: 15px; }
pre {
    background: #f2f2f2;
    padding: 10px;
    font-size: 11px;
    white-space: pre-wrap;
    border-radius: 5px;
}
</style>
</head>
<body>

<h1>APT Report: '.htmlspecialchars($apt['name']).'</h1>

<div class="section">
<b>Aliases:</b> '.htmlspecialchars($apt['aliases']).'<br>
<b>Country:</b> '.htmlspecialchars($apt['country']).'<br>
<b>Sponsor:</b> '.htmlspecialchars($apt['sponsor']).'<br>
<b>Motivation:</b> '.htmlspecialchars($apt['motivation']).'<br>
<b>Active:</b> '.htmlspecialchars($apt['active_from']).' - '.htmlspecialchars($apt['active_to'] ?: "Present").'<br>
<b>Risk Score:</b> '.$apt['risk_score'].' / 10 <br>
<b>Knowledge Score:</b> '.($apt['knowledge_score'] ?? "N/A").' / 100
</div>

<h2>TTP Summary</h2>
<div class="section">'.nl2br(htmlspecialchars($apt['ttp_summary'])).'</div>

<h2>Malware Families</h2>
<div class="section">'.nl2br(htmlspecialchars($apt['malware_families'])).'</div>

<h2>Tools Used</h2>
<div class="section">'.nl2br(htmlspecialchars($apt['tools'])).'</div>

<h2>Notable Attacks</h2>
<div class="section">'.nl2br(htmlspecialchars($apt['notable_attacks'])).'</div>

<h2>Indicators of Compromise</h2>
<pre>
Domains:
'.$apt['ioc_domains'].'

IPs:
'.$apt['ioc_ips'].'

Hashes:
'.$apt['ioc_hashes'].'
</pre>

<h2>Detection Opportunities</h2>
<div class="section">'.nl2br(htmlspecialchars($apt['detection_opportunities'])).'</div>

<h2>References</h2>
<div class="section">'.nl2br(htmlspecialchars($apt['references_section'])).'</div>

</body>
</html>
';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$canvas = $dompdf->get_canvas();
$w = $canvas->get_width();
$h = $canvas->get_height();

$now = date("d M Y H:i");

// ===== HEADER =====
$canvas->set_opacity(1.0);

$canvas->text(
    35,  20,                      // X / Y position
    "IntelCTX — Threat Intelligence Report", 
    "Helvetica-Bold", 
    12, 
    array(37/255, 99/255, 235/255)  // Blue accent
);

// Thin header line
$canvas->line(30, 40, $w - 30, 40, array(0,0,0), 0.5);


// ===== FOOTER =====
$canvas->text(
    40,
    $h - 30,
    "Classification: INTERNAL USE ONLY",
    "Helvetica-Bold",
    9,
    array(220/255, 38/38, 38/255)      // Soft red
);

$canvas->text(
    $w - 180,
    $h - 30,
    "Generated: $now",
    "Helvetica",
    9,
    array(120/255, 120/255, 120/255)   // Gray
);

// Footer line
$canvas->line(30, $h - 45, $w - 30, $h - 45, array(0,0,0), 0.5);

// Page numbers support
$font = $dompdf->getFontMetrics()->get_font("Helvetica", "normal");
$canvas->page_text(
    $w - 80,
    $h - 30,
    "Page {PAGE_NUM} of {PAGE_COUNT}",
    $font,
    9,
    array(0.3, 0.3, 0.3)
);

$dompdf->stream($apt['name'] . "_Threat_Report.pdf", ["Attachment" => true]);
exit;

