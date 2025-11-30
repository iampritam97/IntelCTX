<?php
require_once __DIR__ . '/../auth.php';
require_login();
$pdo = get_db();
$rows = $pdo->query("SELECT * FROM audit_logs ORDER BY created_at DESC")->fetchAll();
include __DIR__ . '/../partials/header.php';
?>
<section class="max-w-6xl mx-auto space-y-6 text-sm">
  <h1 class="text-lg font-bold text-primary">Audit Logs</h1>
  <div class="bg-white border border-border rounded-xl p-4 overflow-x-auto">
    <table class="min-w-full text-xs">
      <thead class="border-b border-border text-slate-500"><tr><th>Action</th><th>User</th><th>APT</th><th>Timestamp</th></tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach($rows as $r): ?>
          <tr><td><?php echo $r['action']; ?></td><td><?php echo $r['actor']; ?></td><td><?php echo $r['apt_name']; ?></td><td><?php echo $r['created_at']; ?></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
<?php include __DIR__ . '/../partials/footer.php'; ?>
