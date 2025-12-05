<?php
require_once 'db.php';
include 'partials/header.php';
include __DIR__ . '/partials/tw_components.php';

$pdo = get_db();



// Search + Filters input
$search = $_GET['q'] ?? '';
$country = $_GET['country'] ?? '';
$motivation = $_GET['motivation'] ?? '';
$industry = $_GET['industry'] ?? '';
$malware = $_GET['malware'] ?? '';
$tool = $_GET['tool'] ?? '';
$sort = $_GET['sort'] ?? 'risk_desc';

// Build query
$where = [];
$params = [];

if ($search !== '') {
  $where[] = "(name LIKE :q OR aliases LIKE :q OR malware_families LIKE :q OR tools LIKE :q OR country LIKE :q)";
  $params[':q'] = "%$search%";
}
if ($country !== '') {
  $where[] = "country = :country";
  $params[':country'] = $country;
}
if ($motivation !== '') {
  $where[] = "motivation = :motivation";
  $params[':motivation'] = $motivation;
}
if ($industry !== '') {
  $where[] = "targeted_industries LIKE :industry";
  $params[':industry'] = "%$industry%";
}
if ($malware !== '') {
  $where[] = "malware_families LIKE :malware";
  $params[':malware'] = "%$malware%";
}
if ($tool !== '') {
  $where[] = "tools LIKE :tool";
  $params[':tool'] = "%$tool%";
}

// Sorting logic
$orderBy = "risk_score DESC";
if ($sort === 'newest') $orderBy = "created_at DESC";
if ($sort === 'country_asc') $orderBy = "country ASC";
if ($sort === 'active_desc') $orderBy = "active_to IS NULL DESC, active_to DESC";

// Combine SQL
$sql = "SELECT * FROM apt_groups";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY $orderBy";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$groups = $stmt->fetchAll();
?>



<style>
@layer base {
  html {
    scroll-behavior: smooth;
  }
}
</style>

<!-- Hero Section -->
<!-- HERO SECTION -->
<section class="relative text-center py-24 bg-ht_bg text-ht_text">

  <!-- Terminal Prompt -->
  <div class="inline-flex items-center space-x-2 bg-ht_bg2 border border-ht_border px-4 py-2 rounded-lg text-ht_blue font-mono text-sm mb-6 shadow">
    <span>>_ root@intelctx:~$</span>
    <span class="text-ht_muted">Threat Intelligence Console</span>
  </div>

  <!-- Title -->
  <h1 class="text-5xl md:text-4xl font-bold tracking-tight">
    <span class="text-ht_blue font-mono">></span>
    <span class="text-ht_blue">IntelCTX</span>  
    <span class="text-gray-400">INTELLIGENCE</span>
  </h1>

  <!-- Subtext -->
  <p class="text-ht_muted font-mono text-lg mt-6 max-w-3xl mx-auto">
    APT groups, malware families, adversary tools & TTP tradecraft.  
    <br>Threat research built for SOC, DFIR, and CTI teams.
  </p>

  <!-- Explore Button -->
  <a href="#searchSection"
     class="mt-10 inline-flex items-center space-x-2 bg-ht_blue px-6 py-3 rounded-lg text-white font-bold text-sm hover:bg-ht_blue2 transition">
    ⚡ <span>Explore APT Encyclopedia</span>
  </a>

</section>




<!-- Filters & Search -->
<section id="searchSection" class="bg-ht_bg2 border border-ht_border rounded-xl shadow-md p-6 mb-10 max-w-6xl mx-auto text-ht_text">

  <h2 class="text-xl font-semibold mb-4 text-ht_blue">Advanced Threat Filters</h2>

  <form method="GET" class="grid md:grid-cols-4 gap-4 text-xs font-mono">

    <!-- Search -->
    <div class="md:col-span-2">
      <label class="block mb-1 text-ht_muted">THREAT SEARCH</label>
      <input type="text" name="q"
        value="<?= htmlspecialchars($search) ?>"
        placeholder="APT Name, Alias, Malware, or Tool…"
        class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-xs focus:border-ht_blue focus:ring-ht_blue">
    </div>

    <!-- Country -->
    <div>
      <label class="block mb-1 text-ht_muted">COUNTRY</label>
      <select name="country" class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-xs">
        <option value="">Any</option>
        <?php foreach($pdo->query("SELECT DISTINCT country FROM apt_groups ORDER BY country")->fetchAll() as $c): ?>
          <option value="<?= $c['country'] ?>" <?= $country==$c['country']?'selected':'' ?>>
            <?= $c['country'] ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Motivation -->
    <div>
      <label class="block mb-1 text-ht_muted">MOTIVATION</label>
      <select name="motivation" class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-xs">
        <option value="">Any</option>
        <?php foreach($pdo->query("SELECT DISTINCT motivation FROM apt_groups ORDER BY motivation")->fetchAll() as $m): ?>
          <option value="<?= $m['motivation'] ?>" <?= $motivation==$m['motivation']?'selected':'' ?>>
            <?= $m['motivation'] ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Apply Search Button -->
    <div class="md:col-span-4 flex justify-end">
      <button type="submit"
        class="bg-ht_blue text-white px-5 py-2 rounded-lg text-xs font-bold hover:bg-ht_blue2 transition">
        Apply Filters
      </button>
    </div>

  </form>
</section>



<!-- APT Grid Listing -->
<section class="max-w-6xl mx-auto grid md:grid-cols-2 lg:grid-cols-3 gap-6">

<?php foreach($groups as $g): ?>
  <div class="bg-ht_bg2 border border-ht_border rounded-xl p-5 shadow hover:border-ht_blue transition">

    <div class="flex justify-between items-start">
      <h2 class="text-lg font-bold text-ht_blue"><?= htmlspecialchars($g['name']) ?></h2>
      <span class="text-ht_muted text-sm">Risk: <?= (int)$g['risk_score'] ?></span>
    </div>

    <p class="text-xs text-ht_muted mt-2 line-clamp-3"><?= htmlspecialchars($g['ttp_summary']) ?></p>

    <div class="mt-4 flex justify-between items-center text-xs text-ht_muted">
      <a href="apt.php?id=<?= $g['id'] ?>" class="text-ht_blue font-bold hover:underline">View Profile →</a>
      <span><?= date('d M Y', strtotime($g['updated_at'])) ?></span>
    </div>

  </div>
<?php endforeach; ?>

</section>
</main>

<?php include 'partials/footer.php'; ?>
