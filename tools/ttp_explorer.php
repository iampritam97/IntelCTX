<?php include '../partials/header.php'; ?>

<section class="max-w-6xl mx-auto px-6 py-10 space-y-8 text-primary dark:text-gray-200">

  <div class="text-center mb-8">
    <h1 class="text-4xl font-extrabold text-accent tracking-tight">MITRE ATT&CK TTP Explorer</h1>
    <p class="text-sm text-slate-500 mt-2 max-w-xl mx-auto">
      Explore attacker behaviors by clicking a tactic or technique to pivot across APT intelligence.
    </p>
  </div>

  <!-- TTP Categories -->
  <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-6">

    <?php $categories = [
      "Initial Access",
      "Execution",
      "Persistence",
      "Defense Evasion",
      "Credential Access",
      "Discovery",
      "Lateral Movement",
      "Collection",
      "Command & Control",
      "Exfiltration",
      "Impact"
    ]; ?>

    <?php foreach ($categories as $cat): ?>
      <a href="../index.php?q=<?php echo urlencode($cat); ?>"
        class="block bg-white dark:bg-gray-900 border border-border dark:border-gray-800 
               rounded-xl p-5 shadow-sm hover:shadow-md transition text-center">
        <div class="text-md font-bold uppercase tracking-tight text-primary dark:text-gray-200">
          <?php echo htmlspecialchars($cat); ?>
        </div>
        <div class="text-[10px] text-slate-500 dark:text-gray-500 mt-2">Pivot → APTs</div>
      </a>
    <?php endforeach; ?>

  </div>

  <!-- Common Techniques Section -->
  <div>
    <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-gray-500 mb-3">
      Common MITRE Techniques
    </h2>

    <?php $techniques = [
      "T1566","T1105","T1059","T1003","T1021","T1547","T1082","T1574","T1071","T1041"
    ]; ?>

    <div class="flex flex-wrap justify-center md:justify-start gap-2">
      <?php foreach ($techniques as $t): ?>
        <a href="../index.php?q=<?php echo urlencode($t); ?>" class="ttp-pill"><?php echo strtoupper($t); ?></a>
      <?php endforeach; ?>
    </div>
  </div>

</section>

<style>
.ttp-pill {
  border:1px solid #2563EB;
  background:#EFF6FF;
  padding:6px 14px;
  border-radius:18px;
  font-size:12px;
  font-family:ui-monospace, SFMono-Regular, Menlo, Monaco;
  color:#2563EB;
  text-decoration:none;
  font-weight:600;
  text-align:center;
  transition:0.2s;
}
.dark .ttp-pill {
  background:#1F2937;
  color:#60A5FA;
  border-color:#2563EB20;
}
.ttp-pill:hover {
  background:#DBEAFE;
}
</style>

<?php include '../partials/footer.php'; ?>
