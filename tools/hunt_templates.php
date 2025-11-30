<?php include '../partials/header.php'; ?>

<section class="max-w-6xl mx-auto px-6 py-10 space-y-6 text-primary dark:text-darktext">

  <div class="mb-6">
    <h1 class="text-3xl font-extrabold tracking-tight text-accent">Threat Hunt Query Templates</h1>
    <p class="text-xs text-slate-500 dark:text-gray-400">Copy-paste defensive hunting queries for SOC, endpoint and network triage</p>
  </div>

  <?php $hunts = [
    [
      "Network IOC Hunt",
      "index=network_logs (dest_ip=<IP> OR domain=<DOMAIN>)",
      "General network telemetry pivot by domain/IP"
    ],
    [
      "Endpoint Hash Lookup",
      "process_name=* AND file_hash=<HASH>",
      "EDR/Sysmon lookup for known file artifacts"
    ],
    [
      "Registry Persistence Hunt",
      "reg_path=*Run* OR reg_path=*WinLogon* OR reg_path=*Services*",
      "Hunt suspicious registry changes related to persistence"
    ],
    [
      "Suspicious PowerShell Tree",
      "parent_process=powershell.exe AND child_process=*",
      "Detect anomalous PowerShell process trees"
    ],
    [
      "Brute Login Anomalies",
      "event_id=4625 OR ssh_fail=* OR failure_count>5",
      "Track brute login attempts on endpoint services"
    ],
    [
      "LSASS Access & Dumping",
      "event_id=10 AND target_process=lsass.exe",
      "Detect credential dumping behavior via process access"
    ]
  ]; ?>

  <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

    <?php foreach ($hunts as $h): 
      $id = "hunt_" . substr(md5($h[0]), 0, 8);
    ?>
      <div class="bg-white dark:bg-gray-900 border border-border dark:border-gray-800 rounded-xl shadow-sm p-5 hover:shadow-md transition">
        <h2 class="text-sm font-bold uppercase tracking-tight text-primary dark:text-darktext"><?php echo htmlspecialchars($h[0]); ?></h2>
        <textarea id="<?php echo $id; ?>" rows="2" readonly class="w-full font-mono bg-light dark:bg-gray-800 border border-border dark:border-gray-700 rounded-md p-3 text-[11px] mt-3"><?php echo htmlspecialchars($h[1]); ?></textarea>
        <p class="text-[11px] text-slate-600 dark:text-gray-400 mt-2"><?php echo htmlspecialchars($h[2]); ?></p>
        <button onclick="navigator.clipboard.writeText(document.getElementById('<?php echo $id; ?>').value)" class="mt-2 text-xs underline text-accent hover:opacity-80 transition">Copy Query</button>
      </div>
    <?php endforeach; ?>

  </div>

</section>

<?php include '../partials/footer.php'; ?>
