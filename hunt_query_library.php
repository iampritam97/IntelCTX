<?php include 'partials/header.php'; ?>

<section class="max-w-6xl mx-auto px-6 py-12 space-y-8 text-primary">

  <div>
    <h1 class="text-2xl font-extrabold tracking-tight text-accent">Threat Hunt Query Library</h1>
    <p class="text-xs text-slate-500 mt-1">Common SIEM/EDR query patterns for intrusion triage.</p>
  </div>

  <?php 
  $queries = [
    ["C2 Domain Pivot", 'index=network_logs domain="<DOMAIN>" | stats count by src_ip'],
    ["Hash Artifact Hunt", 'index=sysmon_logs file_hash="<HASH>" | stats values(host) by user'],
    ["LOLBins Execution", 'index=sysmon_logs process_name="*" | search (parent=cmd.exe OR parent=powershell.exe)'],
    ["RDP Lateral Logon", 'index=windows_security event_id=4624 logon_type=10'],
    ["Service Persistence Writes", 'index=windows_registry path="*Services*"'],
    ["LSASS Dump Access", 'index=sysmon_logs event_id=10 target_process="lsass.exe"']
  ];
  ?>

  <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    <?php foreach($queries as $q): ?>
      <div class="bg-white border border-border rounded-xl p-4 shadow-sm hover:shadow-md transition">
        <h3 class="text-xs font-bold uppercase text-slate-400 mb-2"><?php echo $q[0]; ?></h3>
        <textarea readonly rows="2" class="w-full border border-border bg-slate-50 rounded-md p-2 font-mono text-[11px]"><?php echo htmlspecialchars($q[1]); ?></textarea>
      </div>
    <?php endforeach; ?>
  </div>

</section>

<?php include 'partials/footer.php'; ?>
