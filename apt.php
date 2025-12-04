<?php
require_once __DIR__ . '/db.php';
include __DIR__ . '/partials/header.php';

$pdo = get_db();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM apt_groups WHERE id = ?");
$stmt->execute([$id]);
$apt = $stmt->fetch();

if (!$apt) {
    echo "<div class='max-w-6xl mx-auto py-10'><p class='text-sm text-red-600'>APT group not found.</p></div>";
    include __DIR__ . '/partials/footer.php';
    exit;
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_body'])) {
    $author = trim($_POST['author_name'] ?? 'Anon');
    $body = trim($_POST['comment_body'] ?? '');
    if ($body !== '') {
        $stmt = $pdo->prepare("INSERT INTO comments (apt_id, author_name, body) VALUES (?, ?, ?)");
        $stmt->execute([$apt['id'], $author, $body]);
        header("Location: apt.php?id=" . $apt['id']);
        exit;
    }
}

$commentsStmt = $pdo->prepare("SELECT * FROM comments WHERE apt_id = ? ORDER BY created_at DESC");
$commentsStmt->execute([$apt['id']]);
$comments = $commentsStmt->fetchAll();

// Related APTs (simple: share malware family string)
$related = [];
if (!empty($apt['malware_families'])) {
    $like = '%' . trim(explode("\n", $apt['malware_families'])[0]) . '%';
    $relStmt = $pdo->prepare("SELECT id, name, risk_score FROM apt_groups WHERE id <> ? AND malware_families LIKE ? LIMIT 6");
    $relStmt->execute([$apt['id'], $like]);
    $related = $relStmt->fetchAll();
}

// Timeline events
$timelineStmt = $pdo->prepare("SELECT * FROM apt_timeline WHERE apt_id = ? ORDER BY event_date DESC");
$timelineStmt->execute([$apt['id']]);
$events = $timelineStmt->fetchAll();

// Knowledge score (ensure numeric)
$ks = isset($apt['knowledge_score']) ? (int)$apt['knowledge_score'] : 0;
$ks = max(0, min(100, $ks));
if ($ks >= 80) $ks_color = "bg-green-600";
elseif ($ks >= 60) $ks_color = "bg-yellow-500";
elseif ($ks >= 40) $ks_color = "bg-orange-500";
else $ks_color = "bg-red-600";

// Risk color classes (neon/glow)
$risk = (int)$apt['risk_score'];
if ($risk >= 8) {
    $risk_bg = "from-red-500 to-red-400";
    $risk_glow = "shadow-[0_6px_30px_rgba(239,68,68,0.18)]";
    $risk_text = "text-red-400";
} elseif ($risk >= 6) {
    $risk_bg = "from-yellow-400 to-yellow-300";
    $risk_glow = "shadow-[0_6px_30px_rgba(252,211,77,0.14)]";
    $risk_text = "text-yellow-300";
} elseif ($risk >= 4) {
    $risk_bg = "from-orange-400 to-orange-300";
    $risk_glow = "shadow-[0_6px_30px_rgba(249,115,22,0.12)]";
    $risk_text = "text-orange-300";
} else {
    $risk_bg = "from-green-400 to-green-300";
    $risk_glow = "shadow-[0_6px_30px_rgba(34,197,94,0.12)]";
    $risk_text = "text-green-300";
}

// Extract MITRE technique tokens from TTP summary
$summary = $apt['ttp_summary'] ?? '';
preg_match_all('/\bT\d{4}\b/i', $summary, $matches);
$techniques = array_unique($matches[0]);

// Simple icon mapping for malware & tools (keyword heuristics)
$tool_text = strtolower($apt['tools'] ?? '');
$malware_text = strtolower($apt['malware_families'] ?? '');
function detect_icon($text) {
    $t = strtolower($text);
    if (preg_match('/c2|command and control|command-control|c2server|c2 server|beacon/', $t)) return 'c2';
    if (preg_match('/rat|remote access|remoteadmin|meterpreter|rat\W|reverse shell/', $t)) return 'rat';
    if (preg_match('/dropper|backdoor|implant|backdoor\W|webshell|wscript/', $t)) return 'backdoor';
    if (preg_match('/exploit|exploit kit|exploit-kit|exploit\W|zero-day|0day/', $t)) return 'exploit';
    // fallback
    return 'tool';
}
$icons_found = [];
// check both tools and malware list by splitting on common separators
foreach (preg_split('/[,\n;]+/', ($apt['tools'] ?? '')) as $part) {
    $part = trim($part);
    if ($part === '') continue;
    $icons_found[] = ['name'=>$part, 'icon'=>detect_icon($part)];
}
foreach (preg_split('/[,\n;]+/', ($apt['malware_families'] ?? '')) as $part) {
    $part = trim($part);
    if ($part === '') continue;
    $icons_found[] = ['name'=>$part, 'icon'=>detect_icon($part)];
}

// Structured data for SEO (minimal)
?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "name": "<?php echo htmlspecialchars($apt['name']); ?>",
  "alternateName": "<?php echo htmlspecialchars($apt['aliases']); ?>",
  "description": "<?php echo htmlspecialchars(substr($summary ?: '', 0, 150)); ?>",
  "provider": {"@type":"Organization","name":"IntelCTX"}
}
</script>

<style>
  /* REMOVE all harsh white borders used in the APT profile sections */
.border-ht_border {
    border-color: transparent !important;
}

/* Make cards look matte and soft */
.apt-card {
    background: rgba(255,255,255,0.04); /* same as bg-white/5 */
    backdrop-filter: blur(12px);
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,0.06); /* subtle only */
    box-shadow: 0 1px 4px rgba(0,0,0,0.25);
}

.apt-header-card {
    background: rgba(255,255,255,0.04);
    backdrop-filter: blur(10px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.35);
}

.meta-label {
    font-size: 10px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.45);
}

.meta-value {
    font-size: 14px;
    color: white;
    font-weight: 600;
}

</style>

<!-- ===== PAGE ===== -->
<section class="max-w-7xl mx-auto px-6 py-8">

  <!-- TOP: header card -->
<div class="apt-header-card p-6 rounded-xl mb-6 flex justify-between items-start gap-8">


    <!-- LEFT SIDE -->
    <div class="flex-1 space-y-3">

        <!-- Name + MITRE ID -->
        <div class="flex items-center gap-3">
            <h1 class="text-4xl font-extrabold text-white">
                <?= htmlspecialchars($apt['name']); ?>
            </h1>

            <?php if (!empty($apt['mitre_group_id'])): ?>
            <span class="text-[11px] px-2 py-0.5 bg-white/10 border border-white/20 
                         rounded-md font-semibold uppercase text-ht_blue">
                <?= htmlspecialchars($apt['mitre_group_id']); ?>
            </span>
            <?php endif; ?>
        </div>

        <!-- Aliases -->
        <?php if ($apt['aliases']): ?>
        <p class="text-sm text-ht_muted">
            Aliases: <?= htmlspecialchars($apt['aliases']); ?>
        </p>
        <?php endif; ?>

        <!-- Metadata Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-3">

            <div>
                <div class="meta-label">Origin</div>
                <div class="meta-value"><?= htmlspecialchars($apt['country']); ?></div>
            </div>

            <div>
                <div class="meta-label">Sponsor</div>
                <div class="meta-value"><?= htmlspecialchars($apt['sponsor']); ?></div>
            </div>

            <div>
                <div class="meta-label">Motivation</div>
                <div class="meta-value"><?= htmlspecialchars($apt['motivation']); ?></div>
            </div>

            <div>
                <div class="meta-label">Active</div>
                <div class="meta-value">
                    <?= htmlspecialchars($apt['active_from']); ?> –
                    <?= $apt['active_to'] ?: 'Present'; ?>
                </div>
            </div>

        </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="flex flex-col items-end gap-3 w-40">

        <!-- Risk Badge (Compact) -->
<div class="px-3 py-1 text-sm rounded bg-red-700/20 text-red-300 border border-red-700/40 font-mono">
    RISK: <?= (int)$apt['risk_score']; ?>/10
</div>
<!-- <div class="mt-4 space-y-2">
    <a href="export_pdf.php?id=<?= $apt['id']; ?>"
       class="block text-xs border border-ht_border rounded px-3 py-1 hover:bg-ht_bg hover:text-ht_blue transition">
        Export PDF
    </a>
</div> -->


        <p class="text-xs text-ht_muted"><?= htmlspecialchars($apt['confidence_level']); ?> confidence</p>

        <!-- <div class="w-full">
            <div class="text-[10px] uppercase text-ht_muted">Knowledge Score</div>
            <div class="w-full h-2 bg-white/10 rounded-full mt-1 overflow-hidden">
                <div class="bg-white/40" style="width: <?= $ks; ?>%; height: 100%;"></div>
            </div>
            <div class="text-right text-[10px] text-ht_muted"><?= $ks; ?>/100</div>
        </div> -->
                <!-- <a href="export_malware_pdf.php?id=<?= $mal['id'] ?>"
                   class="text-xs border border-ht_border rounded px-3 py-1 mt-2 inline-block hover:bg-ht_bg">
                    Export PDF
                </a> -->
    </div>
</div>


  <!-- MAIN GRID -->
  <div class="grid md:grid-cols-3 gap-6">

    <!-- MAIN COLUMN (2 cols wide) -->
    <main class="md:col-span-2 space-y-4">

      <!-- Attack Lifecycle & TTPs -->
      <div class="backdrop-blur-lg apt-card rounded-xl p-5">
        <h2 class="text-lg font-semibold text-ht_blue mb-3">Attack Lifecycle & TTPs</h2>

        <div class="prose prose-sm dark:prose-invert text-ht_muted max-w-none">
          <?php echo nl2br(htmlspecialchars($summary)); ?>
        </div>

        <!-- Animated lifecycle graph -->
        <div class="mt-6">
          <svg id="lifecycleSVG" viewBox="0 0 900 120" class="w-full h-28">
            <!-- steps: Recon -> Initial Access -> Execution -> Persistence -> Privilege Esc -> Lateral -> C2 -> Exfil -->
            <?php
            $steps = ['Recon','Initial Access','Execution','Persistence','Privilege Escalation','Lateral Movement','C2','Exfiltration'];
            $count = count($steps);
            $cx = 40;
            $spacing = (820)/($count-1);
            for ($i=0;$i<$count;$i++):
              $x = 40 + $i * $spacing;
              $y = 60;
            ?>
              <!-- connecting line -->
              <?php if ($i < $count-1): ?>
                <line x1="<?php echo $x+18;?>" y1="<?php echo $y;?>" x2="<?php echo $x+$spacing-18;?>" y2="<?php echo $y;?>" stroke="rgba(255,255,255,0.06)" stroke-width="4" />
              <?php endif; ?>

              <!-- step circle -->
              <g class="lifecycle-step" data-step="<?php echo $steps[$i]; ?>" transform="translate(<?php echo $x;?>,<?php echo $y;?>)">
                <circle cx="0" cy="0" r="14" fill="rgba(255,255,255,0.04)" stroke="rgba(255,255,255,0.08)" stroke-width="2"></circle>
                <circle class="pulse-dot" cx="0" cy="0" r="6" fill="transparent"></circle>
                <text x="0" y="36" fill="#9CA3AF" font-size="12" text-anchor="middle"><?php echo $steps[$i]; ?></text>
              </g>
            <?php endfor; ?>
          </svg>

          <div class="text-xs text-ht_muted mt-2">Highlighted lifecycle stages are detected in the TTP summary.</div>
        </div>
      </div>

      <!-- Targeting & Sectors -->
      <div class="grid md:grid-cols-2 gap-4">
        <div class="backdrop-blur-lg apt-card rounded-xl p-4">
          <h3 class="text-sm font-semibold text-ht_blue mb-2">Targeted Industries</h3>
          <div class="text-sm text-ht_muted whitespace-pre-line"><?php echo htmlspecialchars($apt['targeted_industries']); ?></div>
        </div>
        <div class="backdrop-blur-lg apt-card rounded-xl p-4">
          <h3 class="text-sm font-semibold text-ht_blue mb-2">Targeted Countries</h3>
          <div class="text-sm text-ht_muted whitespace-pre-line"><?php echo htmlspecialchars($apt['targeted_countries']); ?></div>
        </div>
      </div>

      <!-- Malware Families & Tools with icons -->
      <div class="grid md:grid-cols-2 gap-4">
        <div class="backdrop-blur-lg apt-card rounded-xl p-4">
          <h3 class="text-sm font-semibold text-ht_blue mb-3">Malware Families</h3>
          <div class="flex flex-wrap gap-2">
            <?php
            $mfs = array_filter(array_map('trim', preg_split('/[,\n;]+/', $apt['malware_families'] ?? '')));
            if (empty($mfs)) echo "<span class='text-xs text-ht_muted'>No malware families listed</span>";
            foreach ($mfs as $m): 
              $ic = detect_icon($m);
            ?>
              <div class="flex items-center gap-2 px-3 py-1 rounded-full apt-card">
                <?php if ($ic === 'c2'): ?>
                  <!-- c2 icon -->
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="#F97316" stroke-width="1.5"/><path d="M12 7v5l3 3" stroke="#F97316" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <?php elseif ($ic === 'rat'): ?>
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><rect x="3" y="6" width="18" height="12" rx="2" stroke="#60A5FA" stroke-width="1.5"/><path d="M8 9v6" stroke="#60A5FA" stroke-width="1.5" stroke-linecap="round"/></svg>
                <?php elseif ($ic === 'backdoor'): ?>
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M4 12h16" stroke="#F472B6" stroke-width="1.5"/><path d="M12 4v16" stroke="#F472B6" stroke-width="1.5"/></svg>
                <?php elseif ($ic === 'exploit'): ?>
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M4 12h16" stroke="#FB7185" stroke-width="1.5"/><path d="M8 8l8 8" stroke="#FB7185" stroke-width="1.5"/></svg>
                <?php else: ?>
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="#9CA3AF" stroke-width="1.5"/></svg>
                <?php endif; ?>
                <span class="text-xs text-white font-medium"><?php echo htmlspecialchars($m); ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="backdrop-blur-lg apt-card rounded-xl p-4">
          <h3 class="text-sm font-semibold text-ht_blue mb-3">Tools & Frameworks</h3>
          <div class="flex flex-wrap gap-2">
            <?php
            $tls = array_filter(array_map('trim', preg_split('/[,\n;]+/', $apt['tools'] ?? '')));
            if (empty($tls)) echo "<span class='text-xs text-ht_muted'>No tools listed</span>";
            foreach ($tls as $t):
              $ic = detect_icon($t);
            ?>
              <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/6">
                <?php if ($ic === 'c2'): ?>
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="#F97316" stroke-width="1.5"/><path d="M12 7v5l3 3" stroke="#F97316" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <?php elseif ($ic === 'rat'): ?>
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><rect x="3" y="6" width="18" height="12" rx="2" stroke="#60A5FA" stroke-width="1.5"/><path d="M8 9v6" stroke="#60A5FA" stroke-width="1.5" stroke-linecap="round"/></svg>
                <?php elseif ($ic === 'backdoor'): ?>
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M4 12h16" stroke="#F472B6" stroke-width="1.5"/><path d="M12 4v16" stroke="#F472B6" stroke-width="1.5"/></svg>
                <?php elseif ($ic === 'exploit'): ?>
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M4 12h16" stroke="#FB7185" stroke-width="1.5"/><path d="M8 8l8 8" stroke="#FB7185" stroke-width="1.5"/></svg>
                <?php else: ?>
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="#9CA3AF" stroke-width="1.5"/></svg>
                <?php endif; ?>
                <span class="text-xs text-white font-medium"><?php echo htmlspecialchars($t); ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- MITRE mapped badges -->
      <div class="backdrop-blur-lg apt-card rounded-xl p-4">
        <h3 class="text-sm font-semibold text-ht_blue mb-3">Mapped MITRE Techniques</h3>
        <div class="flex flex-wrap gap-2">
          <?php
            preg_match_all('/\b(T\d{4})\b/i', $summary, $matches2);
            $techniques2 = array_unique($matches2[0]);
            if (empty($techniques2)) {
              echo "<span class='text-xs text-ht_muted'>No techniques detected</span>";
            } else {
              foreach ($techniques2 as $t):
          ?>
            <button class="px-3 py-1 rounded-full bg-white/6 border border-white/8 text-xs font-mono text-white hover:bg-ht_blue/10 transition" data-tech="<?php echo htmlspecialchars($t); ?>">
              <?php echo strtoupper($t); ?>
            </button>
          <?php endforeach; } ?>
        </div>
      </div>

      <!-- Notable attacks & detection opportunities -->
      <div class="backdrop-blur-lg apt-card rounded-xl p-4 grid gap-4">
        <div>
          <h3 class="text-sm font-semibold text-ht_blue mb-1">Notable Past Attacks</h3>
          <p class="text-sm text-ht_muted whitespace-pre-line"><?php echo htmlspecialchars($apt['notable_attacks']); ?></p>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-ht_blue mb-1">Detection Opportunities</h3>
          <p class="text-sm text-ht_muted whitespace-pre-line"><?php echo htmlspecialchars($apt['detection_opportunities']); ?></p>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-ht_blue mb-1">References</h3>
          <p class="text-xs text-ht_muted whitespace-pre-line"><?php echo htmlspecialchars($apt['references_section']); ?></p>
        </div>
      </div>

      <!-- Full width Activity Timeline -->
      <div class="backdrop-blur-lg apt-card rounded-xl p-4">
        <div class="flex justify-between items-center mb-3">
          <h3 class="text-sm font-semibold text-ht_blue">Activity Timeline</h3>
          <a href="/admin/apt_timeline_edit.php?apt_id=<?php echo $apt['id']; ?>" class="text-xs underline">Add event</a>
        </div>

        <?php if (!$events): ?>
          <p class="text-xs text-ht_muted">No timeline events recorded.</p>
        <?php else: ?>
          <ul class="space-y-4 text-sm">
            <?php foreach ($events as $e): ?>
              <li class="p-3 bg-white/5 border border-white/6 rounded-md">
                <div class="flex justify-between items-start gap-3">
                  <div>
                    <div class="text-sm font-semibold text-white"><?php echo htmlspecialchars($e['title']); ?></div>
                    <?php if ($e['source']): ?><div class="text-xs text-ht_muted">Source: <?php echo htmlspecialchars($e['source']); ?></div><?php endif; ?>
                  </div>
                  <div class="text-xs text-ht_muted"><?php echo date('d M Y', strtotime($e['event_date'])); ?></div>
                </div>
                <div class="mt-2 text-xs text-ht_muted"><?php echo nl2br(htmlspecialchars($e['description'])); ?></div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

    </main>

    <!-- SIDEBAR -->
    <aside class="space-y-4 sticky top-6">

      <!-- IOCs Panel -->
      <div class="backdrop-blur-lg apt-card rounded-xl p-4">
        <div class="flex justify-between items-center mb-2">
          <h3 class="text-sm font-semibold text-ht_blue">Indicators of Compromise</h3>
          <div class="text-xs">
            <button onclick="exportProfile()" class="underline mr-2">Export</button>
            <button onclick="copyAllIOCs()" class="underline">Copy All</button>
          </div>
        </div>

        <div class="space-y-3 text-xs font-mono text-ht_muted">
          <div>
            <div class="flex justify-between items-center mb-1"><strong>Domains</strong><button class="underline" onclick="copyText('ioc_domains')">Copy</button></div>
            <textarea id="ioc_domains" readonly rows="3" class="w-full bg-transparent border border-white/8 rounded p-2 text-xs"><?php echo htmlspecialchars($apt['ioc_domains']); ?></textarea>
          </div>

          <div>
            <div class="flex justify-between items-center mb-1"><strong>IPs</strong><button class="underline" onclick="copyText('ioc_ips')">Copy</button></div>
            <textarea id="ioc_ips" readonly rows="3" class="w-full bg-transparent border border-white/8 rounded p-2 text-xs"><?php echo htmlspecialchars($apt['ioc_ips']); ?></textarea>
          </div>

          <div>
            <div class="flex justify-between items-center mb-1"><strong>Hashes</strong><button class="underline" onclick="copyText('ioc_hashes')">Copy</button></div>
            <textarea id="ioc_hashes" readonly rows="3" class="w-full bg-transparent border border-white/8 rounded p-2 text-xs"><?php echo htmlspecialchars($apt['ioc_hashes']); ?></textarea>
          </div>

          <div>
            <div class="flex justify-between items-center mb-1"><strong>Emails / Patterns</strong><button class="underline" onclick="copyText('ioc_emails')">Copy</button></div>
            <textarea id="ioc_emails" readonly rows="2" class="w-full bg-transparent border border-white/8 rounded p-2 text-xs"><?php echo htmlspecialchars($apt['ioc_emails']); ?></textarea>
          </div>

          <div>
            <div class="flex justify-between items-center mb-1"><strong>Registry paths</strong><button class="underline" onclick="copyText('ioc_registry')">Copy</button></div>
            <textarea id="ioc_registry" readonly rows="2" class="w-full bg-transparent border border-white/8 rounded p-2 text-xs"><?php echo htmlspecialchars($apt['ioc_registry_paths']); ?></textarea>
          </div>

          <div>
            <div class="flex justify-between items-center mb-1"><strong>YARA / Rules</strong><button class="underline" onclick="copyText('ioc_yara')">Copy</button></div>
            <textarea id="ioc_yara" readonly rows="6" class="w-full bg-transparent border border-white/8 rounded p-2 font-mono text-xs"><?php echo htmlspecialchars($apt['ioc_yara']); ?></textarea>
          </div>
        </div>
      </div>

      <!-- Related APTs -->
      <div class="backdrop-blur-lg apt-card rounded-xl p-4">
        <h3 class="text-sm font-semibold text-ht_blue mb-2">Related APT Groups</h3>
        <?php if (!$related): ?>
          <p class="text-xs text-ht_muted">No related APTs found.</p>
        <?php else: ?>
          <ul class="space-y-2 text-sm">
            <?php foreach ($related as $r): ?>
              <li class="flex justify-between">
                <a href="apt.php?id=<?php echo $r['id']; ?>" class="text-white hover:underline"><?php echo htmlspecialchars($r['name']); ?></a>
                <span class="text-xs text-ht_muted">Risk: <?php echo (int)$r['risk_score']; ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <!-- Notes & Comments -->
      <div class="backdrop-blur-lg apt-card rounded-xl p-4">
        <h3 class="text-sm font-semibold text-ht_blue mb-2">Notes & Comments</h3>
        <form method="post" class="space-y-2">
          <input type="text" name="author_name" placeholder="Name (optional)" class="w-full bg-transparent border border-white/8 rounded px-2 py-1 text-xs" />
          <textarea name="comment_body" rows="3" required placeholder="Hypothesis, detection ideas, notes..." class="w-full bg-transparent border border-white/8 rounded px-2 py-1 text-xs"></textarea>
          <div class="flex justify-between items-center">
            <button type="submit" class="text-xs bg-ht_blue text-white px-3 py-1 rounded">Add comment</button>
            <span class="text-xs text-ht_muted"><?php echo count($comments); ?> comments</span>
          </div>
        </form>

        <div class="mt-3 space-y-2 max-h-56 overflow-y-auto text-xs">
          <?php foreach ($comments as $c): ?>
            <div class="border border-white/6 rounded p-2 bg-white/3">
              <p class="text-sm text-white whitespace-pre-line"><?php echo htmlspecialchars($c['body']); ?></p>
              <div class="mt-1 text-xs text-ht_muted"><?php echo htmlspecialchars($c['author_name'] ?: 'Anon'); ?> · <?php echo $c['created_at']; ?></div>
            </div>
          <?php endforeach; ?>
          <?php if (!$comments): ?>
            <p class="text-xs text-ht_muted">No comments yet.</p>
          <?php endif; ?>
        </div>
      </div>

    </aside>

  </div>

</section>

<!-- ===== SCRIPTS ===== -->
<script>
/* copy helpers */
function copyText(id) {
    const el = document.getElementById(id);
    if (!el) return;
    const text = el.value ?? el.innerText ?? '';
    navigator.clipboard.writeText(text).then(() => {
        toast('Copied to clipboard.');
    }).catch(() => { toast('Copy failed.'); });
}

function copyAllIOCs() {
    const ids = ['ioc_domains','ioc_ips','ioc_hashes','ioc_emails','ioc_registry','ioc_yara'];
    let out = [];
    ids.forEach(i => {
        const el = document.getElementById(i);
        if (el && el.value && el.value.trim() !== '') out.push(el.value.trim());
    });
    const text = out.join("\n\n");
    navigator.clipboard.writeText(text).then(() => toast('All IOCs copied')).catch(()=>toast('Copy failed'));
}

/* export MD */
function exportProfile(){
    const name = "<?php echo addslashes($apt['name']); ?>";
    const md = [
        `# ${name}`,
        `**Aliases:** <?php echo addslashes($apt['aliases']); ?>`,
        `**Country:** <?php echo addslashes($apt['country']); ?>`,
        `**Sponsor:** <?php echo addslashes($apt['sponsor']); ?>`,
        `**Active:** <?php echo addslashes($apt['active_from'] . '–' . ($apt['active_to'] ?: 'Present')); ?>`,
        `**Motivation:** <?php echo addslashes($apt['motivation']); ?>`,
        '',
        '## Attack Lifecycle & TTPs',
        `<?php echo addslashes($summary); ?>`,
        '',
        '## Targeting',
        `<?php echo addslashes($apt['targeted_industries']); ?>`,
        `<?php echo addslashes($apt['targeted_countries']); ?>`,
        '',
        '## Malware Families',
        `<?php echo addslashes($apt['malware_families']); ?>`,
        '',
        '## Tools',
        `<?php echo addslashes($apt['tools']); ?>`,
        '',
        '## Notable Attacks',
        `<?php echo addslashes($apt['notable_attacks']); ?>`,
        '',
        '## Detection Opportunities',
        `<?php echo addslashes($apt['detection_opportunities']); ?>`,
        '',
        '## IOCs',
        '### Domains', `<?php echo addslashes($apt['ioc_domains']); ?>`,
        '### IPs', `<?php echo addslashes($apt['ioc_ips']); ?>`,
        '### Hashes', `<?php echo addslashes($apt['ioc_hashes']); ?>`,
        '### Emails', `<?php echo addslashes($apt['ioc_emails']); ?>`,
        '### Registry', `<?php echo addslashes($apt['ioc_registry_paths']); ?>`,
        '',
        '## YARA',
        '```yara',
        `<?php echo addslashes($apt['ioc_yara']); ?>`,
        '```',
        '',
        '## References',
        `<?php echo addslashes($apt['references_section']); ?>`
    ].join("\n\n");

    const blob = new Blob([md], {type: 'text/markdown'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = name.replace(/\s+/g,'_') + '_profile.md';
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
}

/* small toast */
function toast(msg) {
    let el = document.getElementById('intelctx_toast');
    if (!el) {
        el = document.createElement('div');
        el.id = 'intelctx_toast';
        el.className = 'fixed left-1/2 -translate-x-1/2 bottom-8 z-50 bg-white/10 text-white text-sm px-4 py-2 rounded-md backdrop-blur-md';
        document.body.appendChild(el);
    }
    el.textContent = msg;
    el.style.opacity = '1';
    clearTimeout(el._t);
    el._t = setTimeout(()=>{ el.style.opacity = '0'; }, 2200);
}

/* --- Lifecycle animation: highlight steps detected in summary --- */
(function() {
    const detected = <?php echo json_encode(array_values(array_map('strtoupper', $techniques ? $techniques : []))); ?>;
    // Map common techniques to lifecycle steps heuristically
    const map = {
        'T1598':'Recon', 'T1595':'Recon', 'T1592':'Recon', // example reconnaissance entries
        'T1078':'Initial Access', 'T1190':'Initial Access', 'T1566':'Initial Access',
        'T1059':'Execution', 'T1204':'Execution',
        'T1547':'Persistence', 'T1543':'Persistence',
        'T1068':'Privilege Escalation', 'T1134':'Privilege Escalation',
        'T1021':'Lateral Movement', 'T1075':'Lateral Movement',
        'T1071':'C2','T1095':'C2',
        'T1041':'Exfiltration','T1537':'Exfiltration'
    };
    // Build set of lifecycle steps to highlight
    const highlight = new Set();
    detected.forEach(t => {
        if (map[t]) highlight.add(map[t]);
    });

    // If none matched, attempt keyword matching against summary text
    if (highlight.size === 0) {
        const s = <?php echo json_encode(strtolower($summary)); ?>;
        if (s.includes('scan') || s.includes('recon')) highlight.add('Recon');
        if (s.includes('phish') || s.includes('exploit') || s.includes('initial access')) highlight.add('Initial Access');
        if (s.includes('execute') || s.includes('powershell') || s.includes('cmd')) highlight.add('Execution');
        if (s.includes('persistence') || s.includes('autorun') || s.includes('registry')) highlight.add('Persistence');
        if (s.includes('privilege') || s.includes('sudo') || s.includes('token')) highlight.add('Privilege Escalation');
        if (s.includes('lateral') || s.includes('rdp') || s.includes('ssh')) highlight.add('Lateral Movement');
        if (s.includes('c2') || s.includes('command and control') || s.includes('beacon')) highlight.add('C2');
        if (s.includes('exfil') || s.includes('upload') || s.includes('data transfer')) highlight.add('Exfiltration');
    }

    // animate SVG: add pulsing circles to lifecycle steps
    document.addEventListener('DOMContentLoaded', () => {
        const steps = document.querySelectorAll('#lifecycleSVG .lifecycle-step');
        steps.forEach(g => {
            const stepName = g.getAttribute('data-step');
            const pulse = g.querySelector('.pulse-dot');
            if (highlight.has(stepName)) {
                // set colored fill and pulsing animation
                pulse.setAttribute('fill', '#60A5FA'); // neon blue default
                pulse.animate([
                    { r: 6, opacity: 1, transform: 'scale(1)' },
                    { r: 12, opacity: 0.3, transform: 'scale(1.6)' },
                    { r: 6, opacity: 1, transform: 'scale(1)' }
                ], { duration: 1600 + Math.random()*600, iterations: Infinity });
            } else {
                pulse.setAttribute('fill', 'transparent');
            }
        });
    });
})();

</script>

<!-- ===== STYLES (glass + neon + small tweaks) ===== -->
<style>
/* ensure backdrop blur works nicely */
.backdrop-blur-xl { backdrop-filter: blur(10px); }
.backdrop-blur-lg { backdrop-filter: blur(6px); }

/* small pulse animation fallback */
@keyframes intel-pulse {
  0% { box-shadow: 0 0 0 0 rgba(96,165,250,0.35); transform: scale(1); }
  70% { box-shadow: 0 0 0 10px rgba(96,165,250,0); transform: scale(1.03); }
  100% { box-shadow: 0 0 0 0 rgba(96,165,250,0); transform: scale(1); }
}

/* neon glow helper for general use */
.neon-blue { box-shadow: 0 6px 30px rgba(59,130,246,0.12); border-color: rgba(59,130,246,0.2); }
.neon-red { box-shadow: 0 6px 30px rgba(239,68,68,0.12); border-color: rgba(239,68,68,0.18); }

/* small responsive tweaks */
@media (max-width: 768px) {
  section.max-w-7xl { padding-left: 1rem; padding-right: 1rem; }
  .w-44 { width: 88px !important; }
}

/* subtle style for badges / chips */
button[data-tech] { cursor: pointer; }

</style>
</main>
<?php include __DIR__ . '/partials/footer.php'; ?>
