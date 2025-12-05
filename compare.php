<?php
require_once 'db.php';
include 'partials/header.php';
$pdo = get_db();

$groups = $pdo->query("SELECT name, country, risk_score, motivation, active_from, active_to FROM apt_groups ORDER BY name ASC")->fetchAll();
?>

<section class="max-w-3xl mx-auto space-y-6 text-sm">

  <h1 class="text-2xl font-extrabold tracking-tight text-ht_blue">
    APT Threat Comparison
  </h1>
  <p class="text-xs text-ht_muted">
    Compare key attributes between two threat actors.
  </p>

  <!-- Compare Selection Form -->
  <div class="bg-ht_bg2 border border-ht_border rounded-xl shadow-lg p-6 space-y-6">

    <!-- Dropdown Pair -->
    <div class="grid md:grid-cols-2 gap-5">
      
      <div>
        <label class="block text-[11px] font-semibold uppercase text-ht_muted mb-2">
          APT Group 1
        </label>
        <select id="apt1" class="w-full bg-ht_bg border border-ht_border text-ht_text px-3 py-2 rounded-lg text-sm focus:ring-1 ring-ht_blue">
          <option value="">Select APT Group</option>
          <?php foreach ($groups as $g): ?>
            <option value="<?= htmlspecialchars($g['name']); ?>">
              <?= htmlspecialchars($g['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block text-[11px] font-semibold uppercase text-ht_muted mb-2">
          APT Group 2
        </label>
        <select id="apt2" class="w-full bg-ht_bg border border-ht_border text-ht_text px-3 py-2 rounded-lg text-sm focus:ring-1 ring-ht_blue">
          <option value="">Select APT Group</option>
          <?php foreach ($groups as $g): ?>
            <option value="<?= htmlspecialchars($g['name']); ?>">
              <?= htmlspecialchars($g['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

    </div>

    <button onclick="compareAPT()" 
      class="px-5 py-2 bg-ht_blue text-white rounded-lg text-xs font-semibold hover:opacity-90 transition">
      Compare
    </button>

  </div>

  <!-- Comparison Output Area -->
  <div id="compareOutput" 
       class="hidden bg-ht_bg2 border border-ht_border rounded-xl p-6 shadow-lg overflow-x-auto">

    <h2 class="text-sm font-bold uppercase text-ht_muted mb-3">
      Comparison Result
    </h2>

    <table class="min-w-full text-xs">
      <thead class="border-b border-ht_border text-ht_muted">
        <tr>
          <th class="text-left py-2 pr-6">Property</th>
          <th class="text-left py-2 pr-6">APT 1</th>
          <th class="text-left py-2 pr-6">APT 2</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-ht_border" id="compareTable"></tbody>
    </table>

    <button onclick="copyComparison()" 
      class="border border-ht_border text-ht_text rounded-md px-4 py-2 hover:bg-ht_bg transition text-xs mt-4">
      Copy Summary
    </button>
  </div>

</section>

<script>
let allGroups = <?= json_encode($groups); ?>;

function compareAPT() {
  const a1 = document.getElementById('apt1').value;
  const a2 = document.getElementById('apt2').value;

  if (!a1 || !a2) {
    alert("Select both APT groups to compare.");
    return;
  }

  const aptA = allGroups.find(x => x.name === a1);
  const aptB = allGroups.find(x => x.name === a2);

  let rows = `
    <tr><td class="font-medium py-2">Country</td><td>${aptA.country}</td><td>${aptB.country}</td></tr>
    <tr><td class="font-medium py-2">Motivation</td><td>${aptA.motivation}</td><td>${aptB.motivation}</td></tr>
    <tr><td class="font-medium py-2">Active Years</td><td>${aptA.active_from}–${aptA.active_to || 'Present'}</td><td>${aptB.active_from}–${aptB.active_to || 'Present'}</td></tr>
    <tr><td class="font-medium py-2">Risk Score</td><td>${aptA.risk_score}</td><td>${aptB.risk_score}</td></tr>
  `;

  document.getElementById('compareTable').innerHTML = rows;
  document.getElementById('compareOutput').classList.remove('hidden');
}

function copyComparison() {
  const a1 = document.getElementById('apt1').value;
  const a2 = document.getElementById('apt2').value;

  const aptA = allGroups.find(x => x.name === a1);
  const aptB = allGroups.find(x => x.name === a2);

  const summary = `
${aptA.name} vs ${aptB.name}
Origin: ${aptA.country} vs ${aptB.country}
Motivation: ${aptA.motivation} vs ${aptB.motivation}
Active Years: ${aptA.active_from}–${aptA.active_to || 'Present'} vs ${aptB.active_from}–${aptB.active_to || 'Present'}
Risk Score: ${aptA.risk_score} vs ${aptB.risk_score}
  `.trim();

  navigator.clipboard.writeText(summary);
  alert("Comparison summary copied!");
}
</script>
</main>
<?php include 'partials/footer.php'; ?>
