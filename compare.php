<?php
require_once 'db.php';
include 'partials/header.php';
$pdo = get_db();

$groups = $pdo->query("SELECT name, country, risk_score, motivation, active_from, active_to FROM apt_groups ORDER BY name ASC")->fetchAll();
?>

<section class="max-w-3xl mx-auto space-y-6 text-sm">
  <h1 class="text-xl font-bold text-primary">APT Threat Comparison</h1>

  <!-- Compare Selection Form -->
  <div class="bg-white border border-border rounded-xl shadow-sm p-5 grid gap-4">

    <!-- Option 1: Dropdown Select -->
    <div class="grid md:grid-cols-2 gap-3">
      <div>
        <label class="block text-xs font-semibold uppercase text-slate-500 mb-2">APT Group 1</label>
        <select id="apt1" class="w-full border border-border rounded-md px-3 py-2 text-sm">
          <option value="">Select APT Group</option>
          <?php foreach ($groups as $g): ?>
            <option value="<?php echo htmlspecialchars($g['name']); ?>"><?php echo htmlspecialchars($g['name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block text-xs font-semibold uppercase text-slate-500 mb-2">APT Group 2</label>
        <select id="apt2" class="w-full border border-border rounded-md px-3 py-2 text-sm">
          <option value="">Select APT Group</option>
          <?php foreach ($groups as $g): ?>
            <option value="<?php echo htmlspecialchars($g['name']); ?>"><?php echo htmlspecialchars($g['name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <!-- Option 2: OR search for APT names -->
    <div class="mt-2">
      <label class="block text-xs font-semibold uppercase text-slate-500 mb-2">Or Search & Compare</label>
      <input id="searchAPT" type="text"
        class="w-full border border-border rounded-md px-3 py-2 text-sm"
        placeholder="Type APT name or alias...">
      <div id="searchResults" class="hidden bg-slate-50 border border-border rounded-md p-3 mt-2 text-xs"></div>
    </div>

    <button onclick="compareAPT()" class="bg-accent text-white px-4 py-2 rounded-md font-medium text-sm md:w-fit">
      Compare
    </button>

  </div>

  <!-- Comparison Output Area -->
  <div id="compareOutput" class="hidden bg-white border border-border rounded-xl p-5 shadow-sm overflow-x-auto">
    <h2 class="text-sm font-bold uppercase text-slate-500 mb-3">Comparison Result</h2>

    <table class="min-w-full text-xs">
      <thead class="border-b border-border text-slate-500">
        <tr>
          <th class="text-left py-2 pr-6">Property</th>
          <th class="text-left py-2 pr-6">APT 1</th>
          <th class="text-left py-2 pr-6">APT 2</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100" id="compareTable"></tbody>
    </table>

    <button onclick="copyComparison()" class="border border-border rounded-md px-4 py-2 hover:bg-slate-100 transition text-xs md:w-fit mt-4">
      Copy Summary
    </button>
  </div>
</section>

<script>
let allGroups = <?php echo json_encode($groups); ?>;

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

<?php include 'partials/footer.php'; ?>
