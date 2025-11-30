<?php
require_once 'db.php';
include 'partials/header.php';
$pdo = get_db();
$rows = $pdo->query("SELECT id,name,aliases,country,motivation,active_from,active_to,notable_attacks,updated_at FROM apt_groups ORDER BY active_from ASC")->fetchAll();
?>

<section class="max-w-5xl mx-auto space-y-8 text-sm">

  <!-- Title -->
  <div class="flex items-center gap-2 text-primary mb-4">
    <h1 class="text-2xl font-bold">APT Activity Timeline</h1>
    <span class="text-xs bg-slate-200 text-slate-700 px-3 py-1 rounded-full">MVP</span>
  </div>

  <!-- Timeline Container -->
  <div class="relative border-l-2 border-slate-300 ml-4 pl-6 space-y-10">

  <?php foreach($rows as $r): ?>
    <div class="relative group">

      <!-- Timeline Dot -->
      <div class="absolute -left-[34px] mt-1 w-4 h-4 bg-white border-2 border-accent rounded-full"></div>

      <!-- APT Profile Card -->
      <div class="bg-white border border-border rounded-xl p-5 shadow-sm hover:shadow-md transition">

        <div class="flex justify-between items-start">

          <!-- APT Name + Alias -->
          <div>
            <a href="apt.php?id=<?php echo $r['id']; ?>" class="text-lg font-semibold text-primary hover:underline">
              <?php echo htmlspecialchars($r['name']); ?>
            </a>
            <?php if($r['aliases']): ?>
              <p class="text-xs text-slate-500">Aliases: <?php echo htmlspecialchars($r['aliases']); ?></p>
            <?php endif; ?>
          </div>

          <!-- Risk & Last Update -->
          <div class="text-right">
            <p class="text-[10px] text-slate-400 uppercase">Last Updated</p>
            <p class="font-medium text-[12px]"><?php echo date('d M Y', strtotime($r['updated_at'])); ?></p>
          </div>

        </div>

        <!-- Intel Block -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4 text-xs">

          <div>
            <span class="block uppercase text-slate-400">Origin</span>
            <span class="font-medium text-primary"><?php echo htmlspecialchars($r['country']); ?></span>
          </div>

          <div>
            <span class="block uppercase text-slate-400">Motivation</span>
            <span class="font-medium text-primary"><?php echo htmlspecialchars($r['motivation']); ?></span>
          </div>

          <div>
            <span class="block uppercase text-slate-400">Active Years</span>
            <span class="font-medium text-primary"><?php echo $r['active_from']; ?>–<?php echo $r['active_to'] ?: 'Present'; ?></span>
          </div>

          <div>
            <span class="block uppercase text-slate-400">Updated</span>
            <span class="font-medium text-primary"><?php echo date('Y-m-d', strtotime($r['updated_at'])); ?></span>
          </div>

        </div>

        <!-- Notable Attacks (if exists) -->
        <?php if($r['notable_attacks']): ?>
        <div class="mt-4">
            <span class="text-[10px] font-bold text-accent uppercase tracking-wide">Notable Attacks</span>
            <p class="text-xs text-slate-700 mt-1 whitespace-pre-line"><?php echo htmlspecialchars($r['notable_attacks']); ?></p>
        </div>
        <?php endif; ?>

      </div>

    </div>
  <?php endforeach; ?>

  </div>

  <?php if(!$rows): ?>
    <p class="text-sm text-slate-500">No APT entries available in timeline.</p>
  <?php endif; ?>
</section>

<?php include 'partials/footer.php'; ?>
