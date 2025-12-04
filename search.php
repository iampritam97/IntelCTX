<?php
require_once 'db.php';
include 'partials/header.php';
$pdo = get_db();

$q = trim($_GET['q'] ?? '');
$like = "%$q%";
$results = ['apt'=>[], 'malware'=>[], 'tools'=>[]];

if ($q !== '') {
    // Detect if user pasted an IOC/hash
    $is_hash = preg_match('/^[a-f0-9]{32,128}$/i', $q);
    $is_ip = filter_var($q, FILTER_VALIDATE_IP);
    $is_domain = preg_match('/^[a-z0-9.-]+\.[a-z]{2,}$/i', $q);

    // APT search with scoring weights
    $aptRows = $pdo->prepare("
        SELECT id, name, aliases, ttp_summary, ioc_domains, ioc_ips, ioc_hashes,
        ( (name LIKE ?)*5 + (aliases LIKE ?)*3 + (ttp_summary LIKE ?)*1 + (ioc_domains LIKE ?)*2 + (ioc_ips LIKE ?)*2 + (ioc_hashes LIKE ?)*4 ) AS score
        FROM apt_groups
        WHERE name LIKE ? OR aliases LIKE ? OR ttp_summary LIKE ? OR ioc_domains LIKE ? OR ioc_ips LIKE ? OR ioc_hashes LIKE ?
        ORDER BY score DESC, updated_at DESC
        LIMIT 50
    ");
    $binds = [$like,$like,$like,$like,$like,$like,$like,$like,$like,$like,$like,$like];
    $aptRows->execute($binds);
    $aptRes = $aptRows->fetchAll();

    foreach ($aptRes as $a) {
        // highlight match in snippet
        $snippet = htmlspecialchars(substr($a['ttp_summary'],0,220));
        $highlighted = str_ireplace(htmlspecialchars($q), "<mark class='bg-yellow-200 text-black'>".htmlspecialchars($q)."</mark>", $snippet);
        $a['snippet'] = $highlighted;
        $results['apt'][] = $a;
    }

    // Malware weighted
    $malRows = $pdo->prepare("
        SELECT id, name, description,
        ((name LIKE ?)*5 + (description LIKE ?)*1 + (capabilities LIKE ?)*2) AS score
        FROM malware_families
        WHERE name LIKE ? OR description LIKE ? OR capabilities LIKE ?
        ORDER BY score DESC LIMIT 40
    ");
    $malRows->execute([$like,$like,$like,$like,$like,$like]);
    $results['malware'] = $malRows->fetchAll();

    // Tools weighted
    $toolRows = $pdo->prepare("
        SELECT id, name, description,
        ((name LIKE ?)*5 + (description LIKE ?)*1) AS score
        FROM threat_tools
        WHERE name LIKE ? OR description LIKE ?
        ORDER BY score DESC LIMIT 30
    ");
    $toolRows->execute([$like,$like,$like,$like]);
    $results['tools'] = $toolRows->fetchAll();
}
?>

<section class="max-w-6xl mx-auto px-6 py-10 space-y-10">

    <!-- Search header -->
    <div>
        <h1 class="text-2xl font-bold text-white">
            Search Results for: <span class="text-ht_blue"><?= htmlspecialchars($q) ?></span>
        </h1>

        <!-- IOC auto-detection -->
        <?php if ($q !== ''): ?>
        <div class="mt-2">
            <?php if ($is_ip): ?>
                <span class="px-2 py-1 text-[11px] bg-blue-500/20 border border-blue-500/40 text-blue-300 rounded">
                    IOC Detected: IP Address
                </span>
            <?php elseif ($is_hash): ?>
                <span class="px-2 py-1 text-[11px] bg-purple-500/20 border border-purple-500/40 text-purple-300 rounded">
                    IOC Detected: Hash
                </span>
            <?php elseif ($is_domain): ?>
                <span class="px-2 py-1 text-[11px] bg-green-500/20 border border-green-500/40 text-green-300 rounded">
                    IOC Detected: Domain
                </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>


    <!-- === APT SECTION === -->
    <?php if ($results['apt']): ?>
    <div>
        <h2 class="text-sm font-semibold text-ht_blue tracking-wide uppercase">APT Groups</h2>
        <div class="mt-3 space-y-3">

        <?php foreach ($results['apt'] as $a): ?>
            <a href="apt.php?id=<?= $a['id'] ?>" 
               class="block bg-ht_bg2 border border-ht_border hover:border-ht_blue transition rounded-xl p-4 shadow-sm group">

                <div class="flex justify-between items-start gap-4">

                    <!-- LEFT -->
                    <div class="space-y-1">
                        <div class="text-sm font-semibold text-white group-hover:text-ht_blue transition">
                            <?= htmlspecialchars($a['name']) ?>
                        </div>

                        <div class="text-xs text-ht_muted leading-snug">
                            <?= $a['snippet'] ?>
                        </div>
                    </div>

                    <!-- SCORE -->
                    <span class="text-[11px] px-2 py-1 rounded-md bg-white/5 border border-white/10 text-ht_muted">
                        Score: <?= (int)$a['score'] ?>
                    </span>
                </div>
            </a>
        <?php endforeach; ?>

        </div>
    </div>
    <?php endif; ?>


    <!-- === MALWARE SECTION === -->
    <?php if ($results['malware']): ?>
    <div>
        <h2 class="text-sm font-semibold text-pink-400 tracking-wide uppercase">Malware Families</h2>
        <div class="mt-3 space-y-3">

        <?php foreach ($results['malware'] as $m): ?>
            <a href="malware_view.php?id=<?= $m['id'] ?>" 
               class="block bg-ht_bg2 border border-ht_border hover:border-pink-400 transition rounded-xl p-4 shadow-sm group">

                <div class="flex justify-between items-start">
                    <div class="space-y-1">
                        <div class="text-sm font-semibold text-white group-hover:text-pink-400 transition">
                            <?= htmlspecialchars($m['name']) ?>
                        </div>
                        <div class="text-xs text-ht_muted leading-snug">
                            <?= htmlspecialchars(substr($m['description'],0,160)) ?>…
                        </div>
                    </div>

                    <span class="text-[11px] px-2 py-1 rounded-md bg-white/5 border border-white/10 text-ht_muted">
                        Score: <?= (int)$m['score'] ?>
                    </span>
                </div>

            </a>
        <?php endforeach; ?>

        </div>
    </div>
    <?php endif; ?>


    <!-- === TOOLS SECTION === -->
    <?php if ($results['tools']): ?>
    <div>
        <h2 class="text-sm font-semibold text-yellow-300 tracking-wide uppercase">Threat Tools</h2>
        <div class="mt-3 space-y-3">

        <?php foreach ($results['tools'] as $t): ?>
            <a href="tools_view.php?id=<?= $t['id'] ?>" 
               class="block bg-ht_bg2 border border-ht_border hover:border-yellow-300 transition rounded-xl p-4 shadow-sm group">

                <div class="flex justify-between items-start">
                    <div class="space-y-1">
                        <div class="text-sm font-semibold text-white group-hover:text-yellow-300 transition">
                            <?= htmlspecialchars($t['name']) ?>
                        </div>
                        <div class="text-xs text-ht_muted leading-snug">
                            <?= htmlspecialchars(substr($t['description'],0,140)) ?>…
                        </div>
                    </div>

                    <span class="text-[11px] px-2 py-1 rounded-md bg-white/5 border border-white/10 text-ht_muted">
                        Score: <?= (int)$t['score'] ?>
                    </span>
                </div>

            </a>
        <?php endforeach; ?>

        </div>
    </div>
    <?php endif; ?>


    <!-- No results -->
    <?php if (!$results['apt'] && !$results['malware'] && !$results['tools']): ?>
    <p class="text-sm text-ht_muted mt-6">No results found.</p>
    <?php endif; ?>

</section>

</main>
<?php include 'partials/footer.php'; ?>
