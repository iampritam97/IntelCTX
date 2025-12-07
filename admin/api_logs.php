<?php
require_once __DIR__ . '/../auth.php';
require_login();
$pdo = get_db();

$token = $_GET['token'] ?? null;

// Fetch token metadata
$metaStmt = $pdo->prepare("SELECT * FROM api_tokens WHERE token = ?");
$metaStmt->execute([$token]);
$tokenInfo = $metaStmt->fetch();

// Fetch logs
$stmt = $pdo->prepare("SELECT * FROM api_logs WHERE token = ? ORDER BY id DESC");
$stmt->execute([$token]);
$logs = $stmt->fetchAll();

include __DIR__ . '/../partials/header.php';
?>

<section class="max-w-5xl mx-auto px-6 py-10 text-sm">

    <!-- PAGE TITLE -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-ht_blue">API Logs</h1>
            <p class="text-xs text-ht_muted">
                Viewing request activity for a specific API token.
            </p>
        </div>

        <a href="api_tokens.php"
           class="text-xs underline text-ht_muted hover:text-white transition">
            ← Back to Token List
        </a>
    </div>

    <!-- TOKEN INFO PANEL -->
    <div class="backdrop-blur-xl bg-white/5 border border-ht_border rounded-xl p-6 shadow-xl mb-6">

        <?php if ($tokenInfo): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">

            <div>
                <div class="text-ht_muted uppercase text-[10px]">Token</div>
                <div class="font-mono text-white bg-white/10 border border-white/20 rounded px-2 py-1 inline-block">
                    <?= substr($tokenInfo['token'], 0, 12) . "••••••"; ?>
                </div>
            </div>

            <div>
                <div class="text-ht_muted uppercase text-[10px]">Owner</div>
                <div class="text-white"><?= htmlspecialchars($tokenInfo['owner']); ?></div>
            </div>

            <div>
                <div class="text-ht_muted uppercase text-[10px]">Scopes</div>
                <span class="px-2 py-1 bg-ht_bg border border-ht_border rounded text-[10px]">
                    <?= htmlspecialchars($tokenInfo['scopes']); ?>
                </span>
            </div>

            <div>
                <div class="text-ht_muted uppercase text-[10px]">Status</div>
                <?php if ($tokenInfo['active']): ?>
                    <span class="px-2 py-1 bg-green-600/30 border border-green-600/40 rounded text-green-300 text-[10px]">
                        Active
                    </span>
                <?php else: ?>
                    <span class="px-2 py-1 bg-red-600/30 border border-red-600/40 rounded text-red-300 text-[10px]">
                        Disabled
                    </span>
                <?php endif; ?>
            </div>

            <div>
                <div class="text-ht_muted uppercase text-[10px]">Expiry</div>
                <div class="text-white">
                    <?= $tokenInfo['expires_at'] ?: 'No Expiry'; ?>
                </div>
            </div>

            <div>
                <div class="text-ht_muted uppercase text-[10px]">Created</div>
                <div class="text-white">
                    <?= $tokenInfo['created_at']; ?>
                </div>
            </div>

        </div>
        <?php else: ?>
            <p class="text-red-400 text-xs">Invalid token provided.</p>
        <?php endif; ?>

    </div>

    <!-- LOGS TABLE -->
    <div class="backdrop-blur-xl bg-white/5 border border-ht_border rounded-xl p-6 shadow-xl">

        <table class="w-full text-xs">
            <thead>
                <tr class="text-ht_muted border-b border-ht_border uppercase tracking-wide text-[10px]">
                    <th class="pb-2 text-left">Endpoint</th>
                    <th class="pb-2 text-left">IP Address</th>
                    <th class="pb-2 text-left">User Agent</th>
                    <th class="pb-2 text-left">Timestamp</th>
                </tr>
            </thead>

            <tbody>

            <?php foreach ($logs as $l): ?>
                <tr class="border-b border-white/10 hover:bg-white/10 transition">

                    <!-- ENDPOINT -->
                    <td class="py-3">
                        <span class="px-2 py-1 bg-ht_bg border border-ht_border rounded text-[10px] text-ht_blue">
                            <?= htmlspecialchars($l['endpoint']); ?>
                        </span>
                    </td>

                    <!-- IP -->
                    <td>
                        <span class="px-2 py-1 bg-white/10 rounded text-[10px] font-mono">
                            <?= htmlspecialchars($l['ip_address']); ?>
                        </span>
                    </td>

                    <!-- USER AGENT (Expandable) -->
                    <td>
                        <span class="cursor-pointer underline text-ht_muted hover:text-white transition"
                              onclick="toggleUA(<?= $l['id']; ?>)">
                            <?= substr($l['user_agent'], 0, 20); ?>…
                        </span>

                        <div id="ua_<?= $l['id']; ?>"
                             class="hidden mt-2 p-2 bg-black/40 border border-ht_border rounded text-[10px]">
                            <?= htmlspecialchars($l['user_agent']); ?>
                        </div>
                    </td>

                    <!-- DATE -->
                    <td class="text-ht_muted">
                        <?= date('d M Y H:i:s', strtotime($l['created_at'])); ?>
                    </td>

                </tr>
            <?php endforeach; ?>

            </tbody>
        </table>

    </div>

</section>

<script>
function toggleUA(id) {
    document.getElementById("ua_" + id).classList.toggle("hidden");
}
</script>
</main>
<?php include __DIR__ . '/../partials/footer.php'; ?>
