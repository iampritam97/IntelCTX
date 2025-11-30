<?php include 'partials/header.php'; ?>

<section class="max-w-6xl mx-auto px-6 py-12 space-y-10 text-primary dark:text-gray-100">

  <div>
    <h1 class="text-3xl font-extrabold tracking-tight text-accent">CTI Terminology Guide</h1>
    <p class="text-xs text-slate-500 mt-1 dark:text-gray-400">A quick reference glossary for threat intel concepts used in the platform.</p>
  </div>

  <?php
  $terms = [
    ["APT", "Advanced Persistent Threat — long-term attackers often backed by nations or large crime orgs."],
    ["TTP", "Tactics, Techniques, and Procedures — describes how attackers behave."],
    ["IOC", "Indicators of Compromise — evidence like IPs, hashes, domains showing a device was attacked."],
    ["YARA", "A rule language used to identify and classify malware samples."],
    ["Attribution", "Identifying who might be behind an attack, based on evidence + intelligence."],
    ["Threat Actor", "An individual or group performing cyberattacks."],
    ["Campaign", "A set of related attacks executed by one threat actor to achieve a goal."],
    ["LOLBins", "Legitimate built-in Windows tools abused by attackers for stealth."],
    ["C2 Server", "Command & Control — remote system attackers use to send commands and receive data from infected devices."],
    ["Data Exfiltration", "Unauthorized transfer of data from a compromised system to attacker-controlled infrastructure."],
    ["Risk Score", "A simple threat severity rating from 1 (low) to 10 (critical)."],
    ["Confidence Level", "Accuracy of attribution — Low/Medium/High depending on OSINT or evidence strength."],
    ["Playbook", "A documented guide defenders use to detect or respond to a threat."],
    ["Hunting Query", "Search queries used in SIEM/EDR tools to find hidden threats."],
    ["Malware Family", "A category of related malware sharing behavior and code similarities."]
  ];
  ?>

  <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($terms as $t): ?>
      <div class="bg-white dark:bg-gray-900 border border-border rounded-xl p-5 shadow-sm space-y-3">
        <h2 class="text-sm font-bold uppercase tracking-tight"><?php echo htmlspecialchars($t[0]); ?></h2>
        <p class="text-xs text-slate-600 dark:text-gray-400"><?php echo htmlspecialchars($t[1]); ?></p>
      </div>
    <?php endforeach; ?>
  </div>

</section>

<?php include 'partials/footer.php'; ?>
