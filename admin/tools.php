<?php
require_once __DIR__ . '/../auth.php';
require_login();
$pdo = get_db();

// Insert
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    if ($name !== '') {
        $stmt = $pdo->prepare("INSERT INTO threat_tools (name, description) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE description=VALUES(description)");
        $stmt->execute([$name, $desc]);
    }
    header('Location: tools.php');
    exit;
}

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM threat_tools WHERE id=?")->execute([$id]);
    header('Location: tools.php');
    exit;
}

$rows = $pdo->query("SELECT * FROM threat_tools ORDER BY name")->fetchAll();
include __DIR__ . '/../partials/header.php';
?>
<section class="space-y-6 text-sm">

  <!-- Header -->
  <div class="flex justify-between items-center">
    <h1 class="text-xl font-bold text-primary">Threat Tools Inventory</h1>
    <a href="dashboard.php" class="border border-border rounded-lg px-3 py-1.5 text-xs hover:bg-white/10 transition">
      ← Back
    </a>
  </div>

  <!-- New Tool Form -->
  <form method="post"
        class="bg-ht_card border border-border rounded-xl p-5 grid md:grid-cols-3 gap-4 shadow-lg">

      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1">Tool Name</label>
        <input name="name"
               class="w-full bg-ht_input border border-ht_border rounded-lg px-3 py-2 text-sm focus:ring-1 ring-ht_blue"
               placeholder="Example: Cobalt Strike, Metasploit">
      </div>

      <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-400 mb-1">Description</label>
        <input name="description"
               class="w-full bg-ht_input border border-ht_border rounded-lg px-3 py-2 text-sm"
               placeholder="Short description of the threat tool">
      </div>

      <div class="md:col-span-3 flex justify-end">
        <button
          class="bg-ht_blue text-white px-4 py-2 rounded-lg text-xs font-semibold hover:bg-ht_blue/80 transition">
          + Save Tool
        </button>
      </div>
  </form>

  <!-- Tools List -->
  <div class="bg-ht_card border border-border rounded-xl p-5 shadow-lg">

    <h2 class="text-sm font-semibold text-ht_blue mb-3">Existing Tools</h2>

    <table class="min-w-full text-xs">
      <thead class="border-b border-ht_border text-slate-400">
        <tr>
          <th class="text-left py-2 pr-4">Name</th>
          <th class="text-left py-2 pr-4">Description</th>
          <th class="text-right py-2">Actions</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-ht_border/60">
        <?php foreach ($rows as $r): ?>
          <tr class="hover:bg-white/5 transition">
            <td class="py-2 pr-4 font-medium text-primary">
              <?= htmlspecialchars($r['name']); ?>
            </td>

            <td class="py-2 pr-4 text-ht_muted">
              <?= htmlspecialchars($r['description']); ?>
            </td>

            <td class="py-2 text-right">
              <a href="?delete=<?= $r['id']; ?>"
                 class="text-red-500 hover:text-red-400 underline"
                 onclick="return confirm('Delete this tool?');">
                 Delete
              </a>
            </td>
          </tr>
        <?php endforeach; ?>

        <?php if (!$rows): ?>
          <tr>
            <td colspan="3" class="py-4 text-center text-ht_muted">
              No threat tools added yet.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</section>

<?php include __DIR__ . '/../partials/footer.php'; ?>
