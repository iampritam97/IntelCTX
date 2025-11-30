<?php
require_once 'db.php';
include 'partials/header.php';
$pdo = get_db();

$q = $_GET['q'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM threat_tools WHERE name LIKE :q OR description LIKE :q ORDER BY name ASC");
$stmt->execute([':q' => "%$q%"]);
$tools = $stmt->fetchAll();
?>

<section class="max-w-6xl mx-auto space-y-6 text-sm dark:text-darktext">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold text-primary dark:text-darktext tracking-tight">Threat Tools & Frameworks</h1>
    <span class="text-xs bg-slate-200 text-slate-700 dark:bg-gray-800 dark:text-gray-300 px-2 py-1 rounded-lg">MVP</span>
  </div>

  <!-- Search -->
  <form method="GET" class="flex gap-2">
    <input type="text" name="q" value="<?php echo htmlspecialchars($q) ?>"
      placeholder="Search threat tools…" 
      class="w-full border border-border dark:border-gray-700 rounded-md px-3 py-2 text-sm bg-white dark:bg-gray-900">
    <button type="submit" class="border border-border dark:border-gray-700 rounded-md px-4 py-2 hover:bg-slate-100 dark:hover:bg-gray-800 transition">
      Search
    </button>
  </form>

  <!-- Tools Grid -->
  <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    <?php foreach($tools as $t): ?>
      <div class="bg-white dark:bg-gray-900 border border-border dark:border-gray-800 rounded-xl p-5 shadow-sm hover:shadow-md transition">
        <h2 class="text-sm font-semibold text-primary dark:text-darktext"><?php echo htmlspecialchars($t['name']); ?></h2>
        <p class="text-xs text-slate-600 dark:text-gray-400 mt-2 line-clamp-3"><?php echo htmlspecialchars($t['description']); ?></p>

        <div class="mt-4 flex flex-wrap gap-2">
          <?php 
            // Optional MITRE tags related to tools (future pivoting)
            $tags = ["Defense","Execution","Credential Access"];
            foreach($tags as $tag): ?>
              <span class="text-[10px] px-2 py-1 bg-slate-50 dark:bg-gray-800 
                        border border-border dark:border-gray-700 
                        rounded-full text-slate-600 dark:text-gray-300 font-mono">
                <?php echo strtoupper($tag); ?>
              </span>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <?php if(!$tools): ?>
    <p class="text-sm text-slate-500 dark:text-gray-500">No tools match your search.</p>
  <?php endif; ?>
</section>

<?php include 'partials/footer.php'; ?>
