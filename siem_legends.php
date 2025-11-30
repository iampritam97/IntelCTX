<?php include 'partials/header.php'; ?>
<section class="max-w-6xl mx-auto px-6 py-12 space-y-8 text-primary dark:text-gray-100">
  <div>
    <h1 class="text-2xl font-extrabold tracking-tight text-accent">SIEM Event ID Legends</h1>
    <p class="text-xs text-slate-500 dark:text-gray-400 mt-1">Important Windows + Linux log event IDs used in threat detection.</p>
  </div>
  <?php $events = [
    ["4624","Successful login"],
    ["4625","Failed login attempt (Brute force detection)"],
    ["4672","Admin login"],
    ["4688","Process creation (important for endpoint TTP detection)"],
    ["10","Sysmon: Process access (Credential dumping via LSASS)"],
    ["11","Sysmon: FileCreate"],
    ["3","Sysmon: Network connect"],
    ["5888","Windows Pipe creation"],
    ["770","Linux audit: File integrity change"],
    ["4800/4801","Screen locked/unlocked"]
  ]; ?>
  <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    <?php foreach($events as $e): ?>
      <div class="bg-white dark:bg-gray-900 border border-border rounded-xl p-4 shadow-sm">
        <h2 class="font-mono text-xs font-bold text-blue-600"><?php echo $e[0]; ?></h2>
        <p class="text-[11px] text-slate-600 dark:text-gray-400 mt-1"><?php echo htmlspecialchars($e[1]); ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php include 'partials/footer.php'; ?>
