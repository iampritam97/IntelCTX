<?php include 'partials/header.php'; ?>

<section class="max-w-6xl mx-auto px-6 py-12 space-y-8 text-primary dark:text-gray-100">

  <!-- Page Header -->
  <div>
    <h1 class="text-3xl font-extrabold tracking-tight text-ht_blue">YARA Rule Library</h1>
    <p class="text-xs text-slate-500 dark:text-gray-400 mt-2">
      A curated collection of commonly used YARA-style rules for detecting malware, C2 patterns,
      credential theft, suspicious scripting, and behavioral anomalies.
    </p>
  </div>

  <?php
  $rules = [
    [
      'Suspicious PowerShell Execution',
      'Detection of PowerShell-based execution often used in malware loaders and post-exploitation.',
      'rule Suspicious_PS_Execution {
  strings:
    $s1 = "PowerShell" nocase
    $s2 = "EncodedCommand" nocase
  condition:
    any of ($s*)
}',
    ],
    [
      'Credential Dumping Artifacts',
      'Common LSASS access patterns used in credential dumping tools.',
      'rule LSASS_Access {
  strings:
    $m1 = "lsass.exe"
    $m2 = "SeDebugPrivilege"
  condition:
    all of them
}',
    ],
    [
      'C2 Beaconing Entropy',
      'High-entropy domain patterns commonly found in malware C2 infrastructures.',
      'rule C2_High_Entropy {
  strings:
    $e1 = /[a-z0-9]{32,}/
  condition:
    $e1
}',
    ],
    [
      'Office Macro Scripts',
      'Macro/VBA indicators commonly used in phishing and malware delivery.',
      'rule Office_Macro {
  strings:
    $a1 = "auto_open()" nocase
    $vbs = "CreateObject(\\"WScript.Shell\\")"
  condition:
    any of them
}',
    ],
  ];
  ?>

  <!-- Rules Grid -->
  <div class="grid sm:grid-cols-1 md:grid-cols-2 gap-6">
    <?php foreach ($rules as $r): ?>
      <div class="bg-[#0D0F12] dark:bg-gray-900 border border-gray-800 rounded-xl p-5 shadow-sm space-y-4">

        <!-- Title -->
        <div>
          <h2 class="text-sm font-semibold text-ht_blue">
            <?= htmlspecialchars($r[0]); ?>
          </h2>
          <p class="text-[11px] text-gray-500 mt-1">
            <?= htmlspecialchars($r[1]); ?>
          </p>
        </div>

        <!-- Code Block -->
        <div class="relative">
          <textarea 
            readonly 
            rows="8"
            id="yara_<?= md5($r[0]); ?>"
            class="w-full border border-gray-700 bg-[#0A0C10] text-gray-300 rounded-lg p-3 font-mono text-[11px] leading-relaxed">
<?= htmlspecialchars($r[2]); ?>
          </textarea>

          <!-- Copy Button -->
          <button onclick="copyYara('yara_<?= md5($r[0]); ?>')"
            class="absolute top-2 right-2 text-xs px-2 py-1 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-md border border-gray-700">
            Copy
          </button>
        </div>

      </div>
    <?php endforeach; ?>
  </div>

</section>

<script>
function copyYara(id) {
  let el = document.getElementById(id);
  navigator.clipboard.writeText(el.value);
  
  // Feedback
  const btn = event.target;
  btn.innerText = "Copied!";
  setTimeout(() => btn.innerText = "Copy", 1200);
}
</script>

<?php include 'partials/footer.php'; ?>
