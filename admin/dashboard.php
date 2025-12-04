<?php
require_once __DIR__ . '/../auth.php';
require_login();
$pdo = get_db();

// Filters / search / paging from GET
$search = trim($_GET['s'] ?? '');
$filter_country = trim($_GET['country'] ?? '');
$filter_motivation = trim($_GET['motivation'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page-1)*$perPage;

// Build where clauses safely
$where = [];
$params = [];
if ($search !== '') { $where[] = "(name LIKE ? OR aliases LIKE ? OR country LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }
if ($filter_country !== '') { $where[] = "country = ?"; $params[] = $filter_country; }
if ($filter_motivation !== '') { $where[] = "motivation = ?"; $params[] = $filter_motivation; }
$where_sql = $where ? 'WHERE '.implode(' AND ', $where) : '';

// total count
$total = $pdo->prepare("SELECT COUNT(*) FROM apt_groups $where_sql");
$total->execute($params);
$totalRows = $total->fetchColumn();
$totalPages = max(1, ceil($totalRows / $perPage));

// fetch page
$q = $pdo->prepare("SELECT id, name, country, motivation, risk_score, updated_at, knowledge_score FROM apt_groups $where_sql ORDER BY updated_at DESC LIMIT ? OFFSET ?");
foreach ($params as $i=>$v) $q->bindValue($i+1, $v);
$q->bindValue(count($params)+1, $perPage, PDO::PARAM_INT);
$q->bindValue(count($params)+2, $offset, PDO::PARAM_INT);
$q->execute();
$groups = $q->fetchAll();

// fetch lists for filters
$countries = $pdo->query("SELECT DISTINCT country FROM apt_groups WHERE country<>'' ORDER BY country")->fetchAll(PDO::FETCH_COLUMN);
$motivations = $pdo->query("SELECT DISTINCT motivation FROM apt_groups WHERE motivation<>'' ORDER BY motivation")->fetchAll(PDO::FETCH_COLUMN);

include __DIR__ . '/../partials/header.php';
?>

<section class="space-y-8 text-sm">

    <!-- ADMIN HEADER -->
    <div class="flex items-center justify-between bg-ht_bg2 border border-ht_border rounded-xl p-5 shadow">
        
        <!-- Left -->
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ht_blue">Admin Console</h1>
            <p class="text-xs text-ht_muted mt-1">Manage threat data, enrichment modules & intelligence workflows</p>
        </div>

        <!-- Right: Admin Identity -->
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-ht_blue/20 text-ht_blue flex items-center justify-center font-bold">
                A
            </div>
            <div class="text-right leading-tight">
                <div class="text-sm font-semibold">Administrator</div>
                <div class="text-[10px] text-ht_muted">intelctx.local</div>
            </div>
        </div>
    </div>


    <!-- TOP MODULE BUTTONS -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="apt_edit.php" 
           class="admin-tile">
            <span class="tile-title">➕ New APT Group</span>
        </a>

        <a href="malware_list.php" class="admin-tile">
            <span class="tile-title">🧬 Malware Master</span>
        </a>

        <a href="tools.php" class="admin-tile">
            <span class="tile-title">🛠 Threat Tools</span>
        </a>

        <a href="logout.php" class="admin-tile bg-red-500/10 border-red-500/20">
            <span class="tile-title text-red-400">⛔ Logout</span>
        </a>
    </div>


    <!-- FILTERS BAR -->
    <div class="bg-ht_bg2 border border-ht_border rounded-xl p-5 shadow space-y-4">

        <div class="grid md:grid-cols-4 gap-4">

            <!-- Search -->
            <div class="col-span-2">
                <label class="block text-[11px] uppercase font-semibold text-ht_muted mb-1">Search</label>
                <input id="liveSearch" 
                    type="text"
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Search by name, aliases, country..."
                    class="input-dark w-full">
            </div>

            <!-- Country -->
            <div>
                <label class="filter-label">Country</label>
                <select id="filterCountry" class="input-dark w-full">
                    <option value="">All</option>
                    <?php foreach($countries as $c): ?>
                        <option value="<?= htmlspecialchars($c) ?>"
                                <?= $filter_country===$c?'selected':'' ?>>
                            <?= htmlspecialchars($c) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Motivation -->
            <div>
                <label class="filter-label">Motivation</label>
                <select id="filterMotivation" class="input-dark w-full">
                    <option value="">All</option>
                    <?php foreach($motivations as $m): ?>
                        <option value="<?= htmlspecialchars($m) ?>"
                                <?= $filter_motivation===$m?'selected':'' ?>>
                            <?= htmlspecialchars($m) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

        </div>

        <div class="flex justify-between">
            <button id="applyFilters" 
                class="px-4 py-2 bg-ht_blue rounded-lg text-white text-xs hover:opacity-90 transition">
                Apply Filters
            </button>

            <div class="text-[11px] text-ht_muted">Total Records: <?= $totalRows ?></div>
        </div>
    </div>


    <!-- TABLE -->
    <div class="bg-ht_bg2 border border-ht_border rounded-xl p-5 shadow overflow-x-auto">

        <table id="aptTable" class="min-w-full text-xs">
            <thead class="border-b border-ht_border/50 text-ht_muted">
                <tr>
                    <th class="th-sort" data-sort="name">Name</th>
                    <th class="th-basic">Country</th>
                    <th class="th-basic">Motivation</th>
                    <th class="th-sort" data-sort="risk_score">Risk</th>
                    <th class="th-basic">Knowledge</th>
                    <th class="th-basic">Updated</th>
                    <th class="text-right py-2">Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach($groups as $g): ?>
                <tr class="border-b border-ht_border/30 hover:bg-ht_bg/40 transition">
                    <td class="td-text"><?= htmlspecialchars($g['name']) ?></td>
                    <td class="td-text"><?= htmlspecialchars($g['country']) ?></td>
                    <td class="td-text"><?= htmlspecialchars($g['motivation']) ?></td>

                    <!-- Risk Badge -->
                    <td class="py-2 pr-4">
                        <span class="px-2 py-1 rounded-md text-[10px] 
                            <?= $g['risk_score'] >= 7 ? 'bg-red-500/20 text-red-300' : 
                               ($g['risk_score'] >=4 ? 'bg-yellow-500/20 text-yellow-300' :
                                                        'bg-green-500/20 text-green-300') ?>">
                            <?= (int)$g['risk_score'] ?>
                        </span>
                    </td>

                    <td class="td-text"><?= (int)$g['knowledge_score'] ?></td>
                    <td class="td-text"><?= $g['updated_at'] ?></td>

                    <td class="py-2 text-right">
                        <a href="apt_edit.php?id=<?= $g['id'] ?>" 
                           class="text-ht_blue underline mr-2">Edit</a>
                        <a href="apt_delete.php?id=<?= $g['id'] ?>" 
                           onclick="return confirm('Delete?')"
                           class="text-red-400 underline">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="flex items-center justify-between mt-4">
            <span class="text-[11px] text-ht_muted">Page <?= $page ?> / <?= $totalPages ?></span>

            <div class="flex gap-2">
                <?php if($page > 1): ?>
                    <a class="page-btn" href="?<?= http_build_query(array_merge($_GET,['page'=>$page-1])) ?>">‹ Prev</a>
                <?php endif; ?>

                <?php if($page < $totalPages): ?>
                    <a class="page-btn" href="?<?= http_build_query(array_merge($_GET,['page'=>$page+1])) ?>">Next ›</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

</section>
<style>
  .admin-tile {
    @apply bg-ht_bg2 border border-ht_border rounded-xl p-4 shadow-sm 
           hover:border-ht_blue/40 hover:shadow-md transition flex items-center justify-center;
}
.tile-title {
    @apply text-xs font-semibold text-ht_text tracking-wide;
}

.input-dark {
    @apply px-3 py-2 border border-ht_border rounded-lg bg-ht_bg text-ht_text text-xs 
           focus:outline-none focus:ring-1 focus:ring-ht_blue;
}

.filter-label {
    @apply block text-[11px] uppercase font-semibold text-ht_muted mb-1;
}

.th-sort {
    @apply text-left py-2 pr-4 cursor-pointer hover:text-ht_blue transition;
}
.th-basic {
    @apply text-left py-2 pr-4;
}
.td-text {
    @apply py-2 pr-4 text-ht_text;
}

.page-btn {
    @apply px-3 py-1 border border-ht_border rounded text-xs hover:border-ht_blue hover:text-ht_blue transition;
}

</style>

<?php include __DIR__ . '/../partials/footer.php'; ?>

<script>
// Live search / filters apply (client-side -> reload with GET)
document.getElementById('applyFilters').addEventListener('click', () => {
  const s = document.getElementById('liveSearch').value.trim();
  const country = document.getElementById('filterCountry').value;
  const motivation = document.getElementById('filterMotivation').value;
  const params = new URLSearchParams(window.location.search);
  if (s) params.set('s', s); else params.delete('s');
  if (country) params.set('country', country); else params.delete('country');
  if (motivation) params.set('motivation', motivation); else params.delete('motivation');
  params.delete('page');
  window.location.search = params.toString();
});

// Small client-side sort (ascending/descending toggle)
document.querySelectorAll('#aptTable thead th[data-sort]').forEach(th => {
  th.addEventListener('click', () => {
    const col = th.dataset.sort;
    const tbody = document.querySelector('#aptTable tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const idx = Array.from(th.parentNode.children).indexOf(th);
    const asc = !th.classList.contains('sorted-asc');
    rows.sort((a,b) => {
      const va = a.children[idx].innerText.trim();
      const vb = b.children[idx].innerText.trim();
      const na = parseFloat(va) || va.toLowerCase();
      const nb = parseFloat(vb) || vb.toLowerCase();
      if (na < nb) return asc ? -1:1;
      if (na > nb) return asc ? 1:-1;
      return 0;
    });
    tbody.innerHTML = '';
    rows.forEach(r => tbody.appendChild(r));
    document.querySelectorAll('#aptTable thead th').forEach(x => x.classList.remove('sorted-asc','sorted-desc'));
    th.classList.add(asc ? 'sorted-asc' : 'sorted-desc');
  });
});
</script>
