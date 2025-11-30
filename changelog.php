<?php include 'partials/header.php'; ?>
<style>
    .section-title {
  @apply text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-gray-600;
}
.input-box {
  @apply border border-border bg-slate-50 dark:bg-gray-800 rounded-md px-3 py-2 font-mono text-xs focus:ring-1 ring-accent transition;
}

</style>
<section class="max-w-5xl mx-auto px-6 py-12 space-y-8 text-primary">

  <!-- Page Header -->
  <div>
    <h1 class="text-3xl font-extrabold tracking-tight text-accent">Changelog</h1>
    <p class="text-xs text-slate-500 mt-1">Version history and platform enhancement timeline</p>
  </div>

  <!-- Timeline Container -->
  <div class="border-l-2 border-slate-200 dark:border-gray-800 pl-5 space-y-10">

    <?php 
    $logs = [
      ["MVP 1.0", "2025-11-30", [
        "Initial release of APT encyclopedia module",
        "Admin panel with secure login and CRUD",
        "IOC copy utilities & profile export (TXT/MD)",
        "Malware master list & threat tools list",
        "Audit logging for admin actions"
      ]],
      ["MVP 1.1", "2025-11-30", [
        "Added MITRE ATT&CK Group ID support",
        "Introduced Malware Family detail pages",
        "Added TTP click-through explorer UI",
        "Threat Hunt query library implemented",
        "Governance & compliance pages introduced",
        "Changelog UI added"
      ]],
      ["MVP 1.2 (Current)", "2025-11-30", [
        "Query Builder extended for multiple IOC types",
        "Dark mode root support (no toggle ✅ at core layer)",
        "UI polished for enterprise readability",
        "Footer version alignment updated"
      ]]
    ];
    ?>

    <?php foreach($logs as $l): ?>
    <div class="relative">
      <!-- Version Circle -->
      <div class="absolute -left-[30px] bg-white border border-slate-300 rounded-full w-3 h-3 mt-1.5"></div>

      <!-- Version Card -->
      <div class="bg-white dark:bg-gray-900 border border-border dark:border-gray-800 rounded-xl p-5 shadow-sm space-y-3">
        <div class="flex justify-between items-start">
          <h2 class="text-sm font-bold uppercase tracking-tight text-primary"><?php echo $l[0]; ?></h2>
          <time class="text-[10px] text-slate-400 dark:text-gray-600 font-mono"><?php echo date('d M Y', strtotime($l[1])); ?></time>
        </div>

        <!-- Changes List -->
        <ul class="list-disc list-inside text-xs text-slate-600 dark:text-gray-400 space-y-1">
          <?php foreach($l[2] as $line): ?>
            <li><?php echo htmlspecialchars($line); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
    <?php endforeach; ?>

  </div>

</section>

<?php include 'partials/footer.php'; ?>
