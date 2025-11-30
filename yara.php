<?php include 'partials/header.php'; ?>

<section class="max-w-6xl mx-auto px-6 py-12 space-y-8 text-primary dark:text-gray-100">
  <div>
    <h1 class="text-2xl font-extrabold tracking-tight text-accent">Curated YARA Rule Library</h1>
    <p class="text-xs text-slate-500 dark:text-gray-400 mt-1">
      Copy-ready YARA-style examples commonly used to detect suspicious behavior and malware families.
    </p>
  </div>

  <?php
  // IMPORTANT: use single quotes so $s1, $m1, etc are not treated as PHP variables
  $rules = [
    [
      'Suspicious_PWRSH_Exec',
      'rule Suspicious_PS_Execution {
  strings:
    $s1 = "PowerShell" nocase
  condition:
    $s1
}'
    ],
    [
      'Credential_Dump_Artifacts',
      'rule LSASS_Access {
  strings:
    $m1 = "lsass.exe"
    $m2 = "SeDebugPrivilege"
  condition:
    all of them
}'
    ],
    [
      'C2_Beacon_Domains',
      'rule C2_High_Entropy {
  strings:
    $e1 = /[a-z0-9]{32,}/
  condition:
    $e1
}'
    ],
    [
      'Office_Macro_Scripts',
      'rule Office_Macro {
  strings:
    $a1 = "auto_open()" nocase
    $vbs = "CreateObject(\\"WScript.Shell\\")"
  condition:
    any of them
}'
    ],
  ];
  ?>

  <div class="grid sm:grid-cols-1 md:grid-cols-2 gap-6">
    <?php foreach ($rules as $r): ?>
      <div class="bg-white dark:bg-gray-900 border border-border rounded-xl p-5 shadow-sm space-y-3">
        <h2 class="text-[11px] font-bold uppercase text-slate-400">
          <?php echo htmlspecialchars($r[0]); ?>
        </h2>
        <textarea readonly rows="8"
          class="w-full border border-border bg-slate-50 dark:bg-gray-800 rounded-md p-3 font-mono text-[11px]"><?php
            echo htmlspecialchars($r[1]);
        ?></textarea>
      </div>
    <?php endforeach; ?>
  </div>

</section>

<?php include 'partials/footer.php'; ?>
