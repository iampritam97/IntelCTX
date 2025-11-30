<?php include 'partials/header.php'; ?>

<section class="max-w-6xl mx-auto px-6 py-10 space-y-8 text-primary">

  <div class="text-center">
    <h1 class="text-4xl font-extrabold tracking-tight text-accent">Detection Hypothesis Library</h1>
    <p class="text-sm text-slate-500 mt-2 max-w-2xl mx-auto">
      Defensive detection insights mapped to attacker behaviors across the MITRE ATT&CK lifecycle.
    </p>
  </div>

  <!-- TTP Filter + Search -->
  <form method="GET" class="flex flex-col md:flex-row gap-3 bg-white border border-border rounded-xl p-4 shadow-sm">
    <input type="text" name="q" placeholder="Search tactic, technique or behavior keyword…" value="<?php echo htmlspecialchars($_GET['q'] ?? '') ?>"
      class="flex-1 border border-border rounded-md px-3 py-2 bg-slate-50 text-sm font-mono focus:bg-white focus:ring-1 ring-accent transition">
    <button class="border border-border rounded-md px-5 py-2 hover:bg-slate-100 transition text-sm font-medium">Search</button>
  </form>

  <?php
  // Sample hypothesis list (No DB dependency)
  $hypotheses = [
    ["TA0001 — Initial Access", "Monitor spear-phishing, supply-chain implants, drive-by compromises, and script-hosted payload downloads."],
    ["TA0006 — Credential Access", "Detect LSASS access, SAM/NTDS reads, suspicious token duplication, credential dumping utilities, and brute login spikes."],
    ["TA0003 — Persistence", "Track suspicious registry autoruns, scheduled tasks, service/driver creation, startup folder changes, and WinLogon modifications."],
    ["TA0008 — Lateral Movement", "Hunt internal service access anomalies over RDP/SMB/SSH, login failure bursts, and cross-host execution patterns."],
    ["TA0011 — Command and Control", "Identify long-lived connections to rare domains/IP clusters, protocol tunneling, fallback channels, and beacon-like jitter."],
    ["TA0010 — Exfiltration", "Monitor abnormal outbound volume to external/cloud destinations, renamed archive artifacts, chunked file transfer, and reverse-C2 egress."],
    ["T1003 — OS Credential Dumping", "EDR or Sysmon 10 on LSASS, handle-dup inspection, memory access frequency, and process ancestry correlation."],
    ["T1105 — Ingress Tool Transfer", "Script interpreters spawning network download cradles + unsigned/renamed binaries written to temp/system paths."],
    ["T1547.001 — Registry Run Keys", "Autorun key delta inspection + Jira/ITSM audit checks when registry values change in system/user hives."],
    ["T1566.001 — Phishing Attachment", "Mail client → child spawn detection, file write inspection, MOTW evaluation, and user-entropy heuristics."]
  ];

  // Apply simple filter client-side if needed
  ?>
  
  <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

    <?php foreach ($hypotheses as $hp): ?>
      <div class="bg-white border border-border rounded-xl p-5 shadow-sm hover:shadow-md transition">

        <!-- Title -->
        <h2 class="text-sm font-bold uppercase text-primary tracking-tight">
          <?php echo htmlspecialchars($hp[0]); ?>
        </h2>

        <!-- Description -->
        <p class="text-xs text-slate-600 mt-3 leading-relaxed whitespace-pre-line">
          <?php echo htmlspecialchars($hp[1]); ?>
        </p>

        <!-- Copy utility -->
        <button onclick="navigator.clipboard.writeText(`<?php echo addslashes($hp[0] . ' → ' . $hp[1]); ?>`)"
          class="mt-4 text-[11px] underline text-accent hover:opacity-80 transition">
          Copy Summary
        </button>

      </div>
    <?php endforeach; ?>

  </div>

</section>

<?php include 'partials/footer.php'; ?>
