<?php include 'partials/header.php'; ?>
<section class="max-w-6xl mx-auto px-6 py-12 space-y-8 text-primary dark:text-gray-100">
  <div>
    <h1 class="text-2xl font-extrabold tracking-tight text-accent">Threat Intel Sources Guide</h1>
    <p class="text-xs text-slate-500 dark:text-gray-400 mt-1">Types of reports, sources, and vendors referenced in threat research.</p>
  </div>
  <?php $sources = [
    ["Mandiant","Enterprise breach reports and APT attributions"],
    ["CrowdStrike","Nation-state attacker research"],
    ["Kaspersky GReAT","APT malware deep-dive reporting"],
    ["MITRE ATT&CK","Threat classification standards & TTP mapping"],
    ["UpGuard","Breach analysis case studies"],
    ["NSA / CISA","Official advisory & detection guidance"],
    ["ESET Research","LOLBins & malware research"],
    ["Cybereason Nocturnus","Nation-state attack documentation"]
  ]; ?>
  <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    <?php foreach($sources as $s): ?>
      <div class="bg-white dark:bg-gray-900 border border-border rounded-xl p-4 shadow-sm hover:shadow-md transition">
        <h2 class="font-mono text-xs font-bold text-primary dark:text-gray-100"><?php echo $s[0]; ?></h2>
        <p class="text-[11px] text-slate-600 dark:text-gray-400 mt-1"><?php echo htmlspecialchars($s[1]); ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php include 'partials/footer.php'; ?>

