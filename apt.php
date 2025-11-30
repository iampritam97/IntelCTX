<?php
require_once __DIR__ . '/db.php';
include __DIR__ . '/partials/header.php';

$pdo = get_db();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM apt_groups WHERE id = ?");
$stmt->execute([$id]);
$apt = $stmt->fetch();

if (!$apt) {
    echo "<p class='text-sm text-red-600'>APT group not found.</p>";
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

$comments = $pdo->prepare("SELECT * FROM comments WHERE apt_id = ? ORDER BY created_at DESC");
$comments->execute([$apt['id']]);
$comments = $comments->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script type="application/ld+json">
{
  "name": "<?php echo htmlspecialchars($apt['name']); ?>",
  "alsoKnownAs": "<?php echo htmlspecialchars($apt['aliases']); ?>",
  "country": "<?php echo htmlspecialchars($apt['country']); ?>",
  "tactics": "<?php echo htmlspecialchars($apt['ttp_summary']); ?>",
  "riskScore": "<?php echo (int)$apt['risk_score']; ?>"
}
</script>

  <meta charset="UTF-8">

  <!-- SEO -->
  <title><?php echo htmlspecialchars($apt['name']); ?> APT Group Profile | APT Intel Encyclopedia</title>
  <meta name="description" content="<?php echo substr(htmlspecialchars($apt['ttp_summary'] ?: ''), 0, 150); ?>">
  <meta name="keywords" content="<?php echo htmlspecialchars($apt['name']).', '.htmlspecialchars($apt['aliases'] ?: '').', '.htmlspecialchars($apt['country'] ?: ''); ?>">

  <!-- OpenGraph -->
  <meta property="og:title" content="<?php echo htmlspecialchars($apt['name']); ?>">
  <meta property="og:description" content="<?php echo substr(htmlspecialchars($apt['ttp_summary'] ?: ''), 0, 150); ?>">
  <meta property="og:type" content="article">

  <!-- Twitter -->
  <meta name="twitter:card" content="summary">
  <meta name="twitter:title" content="<?php echo htmlspecialchars($apt['name']); ?>">
  <meta name="twitter:description" content="<?php echo substr(htmlspecialchars($apt['ttp_summary'] ?: ''), 0, 150); ?>">

</head>
<body>

<section class="bg-white border border-border rounded-xl shadow-sm p-6 mb-6">
  <div class="flex justify-between items-start mb-3">
    <div>
      <div class="flex items-center gap-2">
  <h1 class="text-3xl font-extrabold tracking-tight text-accent">
    <?php echo htmlspecialchars($apt['name']); ?>
  </h1>

  <?php if (!empty($apt['mitre_group_id'])): ?>
    <span class="text-[10px] px-2 py-0.5 bg-slate-200 text-slate-800 rounded-md font-semibold uppercase tracking-wider">
      <?php echo htmlspecialchars($apt['mitre_group_id']); ?>
    </span>
  <?php endif; ?>
</div>

      <?php if ($apt['aliases']): ?>
        <p class="text-sm text-slate-500">Aliases: <?php echo htmlspecialchars($apt['aliases']); ?></p>
      <?php endif; ?>
    </div>

    <div class="text-right">
      <span class="text-xs text-slate-500 uppercase tracking-wide">Risk Score</span>
      <p class="text-3xl font-extrabold text-accent"><?php echo (int)$apt['risk_score']; ?>/10</p>
      <span class="text-xs text-slate-600"><?php echo htmlspecialchars($apt['confidence_level']) ?> Confidence</span>
    </div>
  </div>

  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
    <div><span class="field-label">Origin</span><p class="field-value"><?php echo htmlspecialchars($apt['country']); ?></p></div>
    <div><span class="field-label">Sponsor</span><p class="field-value"><?php echo htmlspecialchars($apt['sponsor']); ?></p></div>
    <div><span class="field-label">Motivation</span><p class="field-value"><?php echo htmlspecialchars($apt['motivation']); ?></p></div>
    <div><span class="field-label">Active Years</span><p class="field-value"><?php echo $apt['active_from']; ?>–<?php echo $apt['active_to'] ?: "Present"; ?></p></div>
  </div>
</section>

<style>
.field-label { @apply text-xs text-slate-500 uppercase tracking-wider; }
.field-value { @apply font-medium text-primary; }
</style>


<section class="grid md:grid-cols-3 gap-4 mb-6 text-sm">
    <div class="md:col-span-2 space-y-4">
        <div class="bg-white border border-slate-200 rounded-lg p-4">
            <h2 class="text-sm font-semibold mb-2">Attack Lifecycle & TTPs</h2>
            <p class="text-sm text-slate-700 whitespace-pre-line"><?php echo htmlspecialchars($apt['ttp_summary']); ?></p>
        </div>

        <div class="bg-white border border-slate-200 rounded-lg p-4 grid md:grid-cols-2 gap-4">
            <div>
                <h3 class="text-xs font-semibold mb-1">Targeted Industries</h3>
                <p class="text-sm text-slate-700 whitespace-pre-line"><?php echo htmlspecialchars($apt['targeted_industries']); ?></p>
            </div>
            <div>
                <h3 class="text-xs font-semibold mb-1">Targeted Countries</h3>
                <p class="text-sm text-slate-700 whitespace-pre-line"><?php echo htmlspecialchars($apt['targeted_countries']); ?></p>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-lg p-4 grid md:grid-cols-2 gap-4">
            <div>
                <h3 class="text-xs font-semibold mb-1">Malware Families Used</h3>
                <p class="text-sm text-slate-700 whitespace-pre-line"><?php echo htmlspecialchars($apt['malware_families']); ?></p>
            </div>
            <div>
                <h3 class="text-xs font-semibold mb-1">Tools & Frameworks</h3>
                <p class="text-sm text-slate-700 whitespace-pre-line"><?php echo htmlspecialchars($apt['tools']); ?></p>
            </div>
        </div>

<div class="bg-white border border-border rounded-xl shadow-sm p-4">
  <h2 class="text-xs font-bold uppercase text-slate-500 mb-3">Mapped MITRE Techniques</h2>
  <div class="flex flex-wrap gap-2">
    <?php
      // Extract MITRE technique IDs from the long TTP summary text
      $summary = $apt['ttp_summary'] ?? '';

      // Regex finds: T1566, T1059, TA0001, etc.
      preg_match_all('/\b(T\d{4}|TA\d{4,5})\b/i', $summary, $matches);
      $techniques = array_unique($matches[0]);

      foreach ($techniques as $t):
    ?>
        <span class="px-3 py-1 border border-slate-300 bg-slate-50 text-primary rounded-full font-mono text-[11px] hover:bg-slate-100 transition cursor-pointer">
          <?php echo strtoupper(htmlspecialchars($t)); ?>
        </span>
    <?php endforeach; ?>

    <?php if (empty($techniques)): ?>
        <span class="text-xs text-slate-400">No MITRE techniques detected</span>
    <?php endif; ?>
  </div>
</div>




        <div class="bg-white border border-slate-200 rounded-lg p-4">
            <h3 class="text-xs font-semibold mb-1">Notable Past Attacks</h3>
            <p class="text-sm text-slate-700 whitespace-pre-line"><?php echo htmlspecialchars($apt['notable_attacks']); ?></p>
        </div>

        <div class="bg-white border border-slate-200 rounded-lg p-4">
            <h3 class="text-xs font-semibold mb-1">Detection Opportunities</h3>
            <p class="text-sm text-slate-700 whitespace-pre-line"><?php echo htmlspecialchars($apt['detection_opportunities']); ?></p>
        </div>

        <div class="bg-white border border-slate-200 rounded-lg p-4">
            <h3 class="text-xs font-semibold mb-1">References</h3>
            <p class="text-xs text-slate-700 whitespace-pre-line"><?php echo htmlspecialchars($apt['references_section']); ?></p>
        </div>
    </div>

    <aside class="space-y-4">
        <div class="bg-white border border-slate-200 rounded-lg p-4 text-xs space-y-2">
            <div class="flex justify-between items-center mb-2">
                <h2 class="text-sm font-semibold">IOC Panels</h2>
                <button onclick="exportProfile()"
                        class="border border-slate-300 rounded px-2 py-1 text-xs">
                    Export as TXT/MD
                </button>
            </div>

            <div>
                <div class="flex justify-between items-center mb-1">
                    <span class="font-semibold">Domains</span>
                    <button class="underline" onclick="copyText('ioc_domains')">Copy</button>
                </div>
<textarea id="ioc_domains"
  class="w-full bg-slate-50 border border-border rounded-md p-3 font-mono text-xs text-slate-800"
  rows="3" readonly><?php echo $apt['ioc_domains']; ?></textarea>

            </div>

            <div>
                <div class="flex justify-between items-center mb-1">
                    <span class="font-semibold">IPs</span>
                    <button class="underline" onclick="copyText('ioc_ips')">Copy</button>
                </div>
                <textarea id="ioc_ips" class="w-full border border-slate-200 rounded p-1.5"
                          rows="3" readonly><?php echo $apt['ioc_ips']; ?></textarea>
            </div>

            <div>
                <div class="flex justify-between items-center mb-1">
                    <span class="font-semibold">File hashes</span>
                    <button class="underline" onclick="copyText('ioc_hashes')">Copy</button>
                </div>
                <textarea id="ioc_hashes" class="w-full border border-slate-200 rounded p-1.5"
                          rows="3" readonly><?php echo $apt['ioc_hashes']; ?></textarea>
            </div>

            <div>
                <div class="flex justify-between items-center mb-1">
                    <span class="font-semibold">Emails / Patterns</span>
                    <button class="underline" onclick="copyText('ioc_emails')">Copy</button>
                </div>
                <textarea id="ioc_emails" class="w-full border border-slate-200 rounded p-1.5"
                          rows="3" readonly><?php echo $apt['ioc_emails']; ?></textarea>
            </div>

            <div>
                <div class="flex justify-between items-center mb-1">
                    <span class="font-semibold">Registry paths</span>
                    <button class="underline" onclick="copyText('ioc_registry')">Copy</button>
                </div>
                <textarea id="ioc_registry" class="w-full border border-slate-200 rounded p-1.5"
                          rows="3" readonly><?php echo $apt['ioc_registry_paths']; ?></textarea>
            </div>

            <div>
                <div class="flex justify-between items-center mb-1">
                    <span class="font-semibold">YARA / YARA-like rules</span>
                    <button class="underline" onclick="copyText('ioc_yara')">Copy</button>
                </div>
                <textarea id="ioc_yara" class="w-full border border-slate-200 rounded p-1.5 font-mono"
                          rows="6" readonly><?php echo $apt['ioc_yara']; ?></textarea>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-lg p-4 text-xs space-y-3">
            <h2 class="text-sm font-semibold">Notes & Comments</h2>
            <form method="post" class="space-y-2">
                <input type="text" name="author_name" placeholder="Name (optional)"
                       class="w-full border border-slate-300 rounded px-2 py-1 text-xs">
                <textarea name="comment_body" rows="3" required
                          placeholder="Hypothesis, detection ideas, notes..."
                          class="w-full border border-slate-300 rounded px-2 py-1 text-xs"></textarea>
                <button type="submit"
                        class="border border-slate-400 rounded px-3 py-1 text-xs">
                    Add comment
                </button>
            </form>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                <?php foreach ($comments as $c): ?>
                    <div class="border border-slate-200 rounded p-2">
                        <p class="text-[11px] text-slate-700 whitespace-pre-line">
                            <?php echo htmlspecialchars($c['body']); ?>
                        </p>
                        <p class="text-[10px] text-slate-500 mt-1">
                            <?php echo htmlspecialchars($c['author_name'] ?: 'Anon'); ?>
                            · <?php echo $c['created_at']; ?>
                        </p>
                    </div>
                <?php endforeach; ?>
                <?php if (!$comments): ?>
                    <p class="text-[11px] text-slate-500">No comments yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </aside>
</section>

<script>
function copyText(id) {
    const el = document.getElementById(id);
    if (!el) return;
    navigator.clipboard.writeText(el.value || el.innerText || '').then(() => {
        alert('Copied to clipboard.');
    }).catch(() => {
        alert('Copy failed.');
    });
}

function exportProfile() {
    const data = {
        name: "<?php echo addslashes($apt['name']); ?>",
        aliases: "<?php echo addslashes($apt['aliases']); ?>",
        country: "<?php echo addslashes($apt['country']); ?>",
        sponsor: "<?php echo addslashes($apt['sponsor']); ?>",
        active: "<?php echo addslashes($apt['active_from'] . '–' . ($apt['active_to'] ?: 'Present')); ?>",
        motivation: "<?php echo addslashes($apt['motivation']); ?>",
        ttp: `<?php echo addslashes($apt['ttp_summary']); ?>`,
        industries: `<?php echo addslashes($apt['targeted_industries']); ?>`,
        countries: `<?php echo addslashes($apt['targeted_countries']); ?>`,
        malware: `<?php echo addslashes($apt['malware_families']); ?>`,
        tools: `<?php echo addslashes($apt['tools']); ?>`,
        attacks: `<?php echo addslashes($apt['notable_attacks']); ?>`,
        detection: `<?php echo addslashes($apt['detection_opportunities']); ?>`,
        refs: `<?php echo addslashes($apt['references_section']); ?>`,
        risk: "<?php echo (int)$apt['risk_score']; ?>",
        confidence: "<?php echo addslashes($apt['confidence_level']); ?>",
        iocs: {
            domains: `<?php echo addslashes($apt['ioc_domains']); ?>`,
            ips: `<?php echo addslashes($apt['ioc_ips']); ?>`,
            hashes: `<?php echo addslashes($apt['ioc_hashes']); ?>`,
            emails: `<?php echo addslashes($apt['ioc_emails']); ?>`,
            registry: `<?php echo addslashes($apt['ioc_registry_paths']); ?>`,
            yara: `<?php echo addslashes($apt['ioc_yara']); ?>`
        }
    };

    const md = [
        `# ${data.name}`,
        ``,
        `**Aliases:** ${data.aliases}`,
        `**Country:** ${data.country}`,
        `**Sponsor:** ${data.sponsor}`,
        `**Active:** ${data.active}`,
        `**Motivation:** ${data.motivation}`,
        `**Risk:** ${data.risk}/10 (${data.confidence} confidence)`,
        ``,
        `## Attack Lifecycle & TTPs`,
        data.ttp,
        ``,
        `## Targeting`,
        `**Industries:**`,
        data.industries,
        ``,
        `**Countries:**`,
        data.countries,
        ``,
        `## Malware Families`,
        data.malware,
        ``,
        `## Tools`,
        data.tools,
        ``,
        `## Notable Attacks`,
        data.attacks,
        ``,
        `## Detection Opportunities`,
        data.detection,
        ``,
        `## Indicators of Compromise`,
        `**Domains:**`,
        data.iocs.domains,
        ``,
        `**IPs:**`,
        data.iocs.ips,
        ``,
        `**Hashes:**`,
        data.iocs.hashes,
        ``,
        `**Emails / Patterns:**`,
        data.iocs.emails,
        ``,
        `**Registry Paths:**`,
        data.iocs.registry,
        ``,
        `**YARA / YARA-like:**`,
        '```yara',
        data.iocs.yara,
        '```',
        ``,
        `## References`,
        data.refs,
        ``
    ].join('\n');

    const blob = new Blob([md], {type: 'text/markdown'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = data.name.replace(/\s+/g, '_') + '_profile.md';
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
}
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>

</body>
</html>
