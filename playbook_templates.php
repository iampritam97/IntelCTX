<?php include 'partials/header.php'; ?>

<section class="max-w-6xl mx-auto px-6 py-10 space-y-6 text-primary">

  <div class="mb-6">
    <h1 class="text-3xl font-bold tracking-tight text-accent">Detection Playbook Templates</h1>
    <p class="text-sm text-slate-500">Defender-ready detection hypothesis mapped to common APT tradecraft</p>
  </div>

  <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    <?php $templates = [
      ["Credential Access (T1003)", "Monitor LSASS memory access via Sysmon Event 10 or EDR telemetry", "event_id=10 AND target_process=lsass.exe"],
      ["Phishing (T1566)", "Detect suspicious child process spawn from mail clients", "parent_process=outlook.exe AND child IN [powershell.exe,cmd.exe]"],
      ["Persistence (T1547)", "Registry autorun key modification monitoring", "reg_path=*Run* OR reg_path=*WinLogon*"],
      ["Command & Control (T1071)", "Monitor known protocol abuse with suspicious domains", "dest_port IN [80,443] AND domain=*"],
      ["Lateral Movement (T1021)", "RDP/SSH session anomalies & brute attempts", "event_id=4625 OR ssh_fail=*"],
      ["Exfiltration (T1041)", "Detect data transfer to cloud storage by unknown agents", "Process naming mismatch + outbound traffic spike"]
    ]; ?>

    <?php foreach($templates as $t): ?>
      <div class="bg-white border border-border rounded-xl shadow-sm p-5 hover:shadow-md transition">
        <h2 class="text-md font-bold uppercase text-primary"><?php echo $t[0]; ?></h2>
        <p class="text-xs text-slate-600 mt-2"><?php echo $t[1]; ?></p>
        
        <textarea id="box_<?php echo md5($t[0]); ?>" rows="2" readonly
          class="w-full font-mono bg-slate-50 border border-border rounded-md p-3 text-[11px] mt-3"><?php echo $t[2]; ?></textarea>

        <button onclick="navigator.clipboard.writeText(document.getElementById('box_<?php echo md5($t[0]); ?>').value)"
          class="mt-2 text-xs underline text-accent hover:text-blue-700 transition">Copy Template</button>
      </div>
    <?php endforeach; ?>
  </div>

</section>

<?php include 'partials/footer.php'; ?>
