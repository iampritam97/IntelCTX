<?php
require_once 'db.php';
include 'partials/header.php';
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



<!-- Hero Section -->
<section class="text-center py-10">
  <h1 class="text-4xl font-extrabold text-primary mb-3">IntelCTX</h1>
  <p class="text-slate-600 text-md max-w-2xl mx-auto">
    Structured threat intelligence platform delivering deep insights on APT groups, malware families, and attack
    tradecraft for SOC, DFIR, and Intel teams.
  </p>
</section>

<!-- Filters & Search -->
<section class="bg-white border border-border rounded-xl shadow-sm p-5 mb-8 max-w-5xl mx-auto">
  <form method="GET" class="grid md:grid-cols-4 gap-4 text-sm">

    <div class="md:col-span-2">
      <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Threat Search</label>
      <input type="text" name="q" value="<?php echo htmlspecialchars($search) ?>"
        placeholder="APT Name, Alias, Malware, Tool, or Country…"
        class="w-full border border-border rounded px-3 py-2 bg-slate-50">
    </div>

    <div>
      <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Origin Country</label>
      <select name="country" class="w-full border border-border rounded px-3 py-2 bg-slate-50">
        <option value="">Any Country</option>
        <?php foreach($pdo->query("SELECT DISTINCT country FROM apt_groups ORDER BY country")->fetchAll() as $c): ?>
          <option value="<?php echo $c['country']; ?>" <?php if($country==$c['country']) echo 'selected'; ?>>
            <?php echo $c['country']; ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Motivation</label>
      <select name="motivation" class="w-full border border-border rounded px-3 py-2 bg-slate-50">
        <option value="">All Motivations</option>
        <?php foreach($pdo->query("SELECT DISTINCT motivation FROM apt_groups ORDER BY motivation")->fetchAll() as $m): ?>
          <option value="<?php echo $m['motivation']; ?>" <?php if($motivation==$m['motivation']) echo 'selected'; ?>>
            <?php echo $m['motivation']; ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- More Filters -->
    <div>
      <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Industry</label>
      <input type="text" name="industry" value="<?php echo htmlspecialchars($industry) ?>"
        placeholder="e.g. Finance, Govt…"
        class="w-full border border-border rounded px-3 py-2 bg-slate-50">
    </div>

    <div>
      <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Malware</label>
      <input type="text" name="malware" value="<?php echo htmlspecialchars($malware) ?>"
        placeholder="e.g. ShadowPad…"
        class="w-full border border-border rounded px-3 py-2 bg-slate-50">
    </div>

    <div>
      <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Threat Tool</label>
      <input type="text" name="tool" value="<?php echo htmlspecialchars($tool) ?>"
        placeholder="e.g. Cobalt Strike…"
        class="w-full border border-border rounded px-3 py-2 bg-slate-50">
    </div>

    <div>
      <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Activity</label>
      <input type="text" name="activity" placeholder="Active 2018–Present"
        class="w-full border border-border rounded px-3 py-2 bg-slate-50">
    </div>

    <!-- Sort -->
    <div class="md:col-span-4 flex justify-between items-end">
      <div>
        <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Sort Intelligence By</label>
        <select name="sort" class="border border-border rounded px-3 py-2 bg-slate-50">
          <option value="risk_desc" <?php if($sort=='risk_desc') echo 'selected'; ?>>Highest Risk</option>
          <option value="newest" <?php if($sort=='newest') echo 'selected'; ?>>Newest Added</option>
          <option value="active_desc" <?php if($sort=='active_desc') echo 'selected'; ?>>Most Active</option>
          <option value="country_asc" <?php if($sort=='country_asc') echo 'selected'; ?>>Sponsor Country</option>
        </select>
      </div>

      <button type="submit"
        class="bg-accent text-white px-5 py-2 rounded-md font-medium hover:bg-blue-700 transition">
        Apply Filters
      </button>
    </div>

  </form>
</section>

<!-- APT Grid Listing -->
<section class="max-w-6xl mx-auto">
  <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">

    <?php foreach($groups as $g): ?>
      <div class="bg-white border border-border rounded-xl p-5 shadow-sm hover:shadow-md transition">

        <div class="flex justify-between items-start">
          <div>
            <h2 class="text-lg font-bold text-primary"><?php echo htmlspecialchars($g['name']) ?></h2>
            <?php if($g['aliases']): ?>
              <p class="text-xs text-slate-500">Aliases: <?php echo htmlspecialchars($g['aliases']) ?></p>
            <?php endif; ?>
          </div>
          <span class="text-2xl font-extrabold text-accent"><?php echo (int)$g['risk_score']; ?></span>
        </div>

        <div class="flex flex-wrap gap-1 mt-3 mb-3">
          <?php if($g['country']): ?><span class="pill"><?php echo $g['country']; ?></span><?php endif; ?>
          <?php if($g['motivation']): ?><span class="pill"><?php echo $g['motivation']; ?></span><?php endif; ?>
          <?php foreach(array_slice(explode("\n",$g['targeted_industries']), 0,2) as $ind): ?>
            <?php if(trim($ind)): ?><span class="pill"><?php echo trim($ind); ?></span><?php endif; ?>
          <?php endforeach; ?>
        </div>

        <p class="text-xs text-slate-700 line-clamp-3"><?php echo htmlspecialchars($g['ttp_summary']); ?></p>

        <div class="mt-4 flex justify-between text-xs text-slate-500">
          <a href="apt.php?id=<?php echo $g['id']; ?>" class="text-accent underline font-medium">View Intel</a>
          <span><?php echo date('d M Y',strtotime($g['updated_at'])); ?></span>
        </div>

      </div>
    <?php endforeach; ?>

  </div>
</section>

<?php include 'partials/footer.php'; ?>
