<?php
require_once __DIR__ . '/../auth.php';
require_login();
$pdo = get_db();

// $debug = $pdo->query("SELECT * FROM api_logs ORDER BY id DESC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);

// echo "<pre style='color:white; background:black; padding:10px;'>";
// print_r($debug);
// echo "</pre>";

// Date filter
// Auto-range detection
$range = $pdo->query("
    SELECT 
        MIN(DATE(created_at)) AS min_date,
        MAX(DATE(created_at)) AS max_date
    FROM api_logs
")->fetch(PDO::FETCH_ASSOC);

$from = $_GET['from'] ?? $range['min_date'];
$to   = $_GET['to']   ?? $range['max_date'];


// Fetch daily request counts
// Fetch daily request counts
$daily = $pdo->prepare("
    SELECT DATE(created_at) AS day, COUNT(*) AS total
    FROM api_logs
    WHERE created_at BETWEEN ? AND ?
    GROUP BY DATE(created_at)
    ORDER BY day ASC
");
$daily->execute([$from . " 00:00:00", $to . " 23:59:59"]);
$dailyData = $daily->fetchAll(PDO::FETCH_ASSOC);

// Top endpoints
$topEndpoints = $pdo->prepare("
    SELECT endpoint, COUNT(*) AS total
    FROM api_logs
    WHERE created_at BETWEEN ? AND ?
    GROUP BY endpoint
    ORDER BY total DESC
    LIMIT 10
");
$topEndpoints->execute([$from . " 00:00:00", $to . " 23:59:59"]);
$topEndpointsData = $topEndpoints->fetchAll(PDO::FETCH_ASSOC);

// Top tokens
$topTokens = $pdo->prepare("
    SELECT token, COUNT(*) AS total
    FROM api_logs
    WHERE created_at BETWEEN ? AND ?
    GROUP BY token
    ORDER BY total DESC
    LIMIT 10
");
$topTokens->execute([$from . " 00:00:00", $to . " 23:59:59"]);
$topTokensData = $topTokens->fetchAll(PDO::FETCH_ASSOC);

// Top IPs
$topIps = $pdo->prepare("
    SELECT ip_address, COUNT(*) AS total
    FROM api_logs
    WHERE created_at BETWEEN ? AND ?
    GROUP BY ip_address
    ORDER BY total DESC
    LIMIT 10
");
$topIps->execute([$from . " 00:00:00", $to . " 23:59:59"]);
$topIpsData = $topIps->fetchAll(PDO::FETCH_ASSOC);


include __DIR__ . '/../partials/header.php';
?>

<section class="max-w-6xl mx-auto px-6 py-10 text-sm">

    <div class="flex justify-between items-center mb-8">
        <h1 class="text-xl font-bold text-ht_blue">API Analytics Dashboard</h1>
    </div>

    <!-- Date Filters -->
    <form method="GET" class="flex gap-4 mb-6 text-xs">
        <div>
            <label class="text-ht_muted">From:</label>
            <input type="date" name="from" value="<?= $from ?>"
                   class="bg-ht_bg border border-ht_border px-2 py-1 rounded text-white">
        </div>

        <div>
            <label class="text-ht_muted">To:</label>
            <input type="date" name="to" value="<?= $to ?>"
                   class="bg-ht_bg border border-ht_border px-2 py-1 rounded text-white">
        </div>

        <button class="px-4 py-2 bg-ht_blue text-white rounded hover:bg-ht_blue2 transition">
            Apply
        </button>
    </form>

    <!-- Chart -->
    <div class="bg-ht_bg2 border border-ht_border rounded-xl p-6 shadow mb-8">
        <h2 class="text-lg font-semibold text-white mb-3">Daily API Requests</h2>
        <canvas id="dailyChart" height="100"></canvas>
    </div>

    <!-- Top endpoints -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <div class="bg-ht_bg2 border border-ht_border rounded-xl p-6 shadow">
            <h3 class="text-sm font-bold text-white mb-3">Top Endpoints</h3>
            <ul class="text-xs">
                <?php foreach ($topEndpointsData as $ep): ?>
                <li class="flex justify-between py-1 border-b border-ht_border">
                    <span><?= htmlspecialchars($ep['endpoint']); ?></span>
                    <span class="text-ht_blue"><?= $ep['total']; ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="bg-ht_bg2 border border-ht_border rounded-xl p-6 shadow">
            <h3 class="text-sm font-bold text-white mb-3">Top Tokens</h3>
            <ul class="text-xs">
                <?php foreach ($topTokensData as $tk): ?>
                <li class="flex justify-between py-1 border-b border-ht_border">
                    <span><?= substr($tk['token'], 0, 6) . "••••••"; ?></span>
                    <span class="text-ht_blue"><?= $tk['total']; ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="bg-ht_bg2 border border-ht_border rounded-xl p-6 shadow">
            <h3 class="text-sm font-bold text-white mb-3">Top IP Addresses</h3>
            <ul class="text-xs">
                <?php foreach ($topIpsData as $ip): ?>
                <li class="flex justify-between py-1 border-b border-ht_border">
                    <span><?= htmlspecialchars($ip['ip_address']); ?></span>
                    <span class="text-ht_blue"><?= $ip['total']; ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

    </div>

</section>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Prepare chart data from PHP arrays
const labels = <?= json_encode(array_column($dailyData, 'day')); ?>;
const values = <?= json_encode(array_column($dailyData, 'total')); ?>;

const ctx = document.getElementById('dailyChart').getContext('2d');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'API Requests',
            data: values,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.3)',
            borderWidth: 2,
            tension: 0.25,
            fill: true,
        }]
    },
    options: {
        scales: {
            y: {
                ticks: { color: '#9ca3af' },
                grid: { color: 'rgba(255,255,255,0.05)' }
            },
            x: {
                ticks: { color: '#9ca3af' },
                grid: { color: 'rgba(255,255,255,0.05)' }
            }
        },
        plugins: {
            legend: { labels: { color: '#fff' } }
        }
    }
});
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
