<?php include 'partials/header.php'; ?>

<section class="max-w-6xl mx-auto px-6 py-10 space-y-8 text-primary dark:text-gray-100">

  <!-- Header -->
  <div>
    <h1 class="text-3xl font-extrabold tracking-tight text-accent">Threat Hunt Query Builder</h1>
    <p class="text-xs text-slate-500 dark:text-gray-400 mt-1">
      Generate copy-ready hunting queries using IOC and TTP templates for SIEM/EDR.
    </p>
  </div>

  <!-- Builder Grid -->
  <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5 bg-white dark:bg-gray-900 border border-border dark:border-gray-800 rounded-xl p-6 shadow-sm">

    <!-- Intel Type -->
    <div>
      <label class="block text-[11px] font-semibold uppercase text-slate-500 mb-2">Intel Type</label>
      <select id="intelType" class="w-full border border-border rounded-md px-3 py-2 bg-slate-50 dark:bg-gray-800 text-xs font-mono focus:ring-1 ring-accent transition">
        <option value="ip">IP Address</option>
        <option value="domain">Domain</option>
        <option value="hash">File Hash</option>
        <option value="registry">Registry Path</option>
        <option value="email">Email Pattern</option>
        <option value="process">Process Name</option>
        <option value="mutex">Mutex / Named Pipe</option>
      </select>
    </div>

    <!-- IOC Value -->
    <div>
      <label class="block text-[11px] font-semibold uppercase text-slate-500 mb-2">IOC / Value</label>
      <input id="iocValue" type="text" placeholder="Enter IP, hash, domain, process etc."
        class="w-full border border-border rounded-md px-3 py-2 bg-slate-50 dark:bg-gray-800 text-xs font-mono focus:bg-white dark:focus:bg-gray-700 focus:ring-1 ring-accent transition">
      <p id="iocHint" class="text-[10px] text-slate-400 dark:text-gray-600 mt-1">Hint will appear based on selection</p>
    </div>

    <!-- Hunt Scope -->
    <div>
      <label class="block text-[11px] font-semibold uppercase text-slate-500 mb-2">Hunt Scope</label>
      <select id="huntScope" class="w-full border border-border rounded-md px-3 py-2 bg-slate-50 dark:bg-gray-800 text-xs">
        <option value="network">Network Logs</option>
        <option value="endpoint">Endpoint / Sysmon</option>
        <option value="both">Both</option>
      </select>
    </div>

    <!-- Tactic -->
    <div>
      <label class="block text-[11px] font-semibold uppercase text-slate-500 mb-2">MITRE Tactic (Optional)</label>
      <select id="mitreTactic" class="w-full border border-border rounded-md px-3 py-2 bg-slate-50 dark:bg-gray-800 text-xs">
        <option value="">None</option>
        <option value="TA0001">Initial Access</option>
        <option value="TA0002">Execution</option>
        <option value="TA0003">Persistence</option>
        <option value="TA0005">Defense Evasion</option>
        <option value="TA0006">Credential Access</option>
        <option value="TA0007">Discovery</option>
        <option value="TA0008">Lateral Movement</option>
        <option value="TA0010">Exfiltration</option>
        <option value="TA0011">Command & Control</option>
        <option value="TA0009">Collection</option>
        <option value="TA0004">Privilege Escalation</option>
        <option value="TA0001">Impact</option>
      </select>
    </div>

    <!-- Condition -->
    <div>
      <label class="block text-[11px] font-semibold uppercase text-slate-500 mb-2">Additional Condition (Optional)</label>
      <input id="huntCondition" type="text" placeholder="event_id=4625 OR resp_size>20MB"
        class="w-full border border-border rounded-md px-3 py-2 bg-slate-50 dark:bg-gray-800 text-[11px] font-mono focus:ring-1 ring-accent transition">
    </div>

    <!-- Build -->
    <div class="flex items-end">
      <button type="button" onclick="buildQuery()" class="w-full bg-accent text-white px-5 py-2 rounded-lg text-xs font-medium hover:opacity-90 transition">
        Build Hunt Query
      </button>
    </div>

  </div>

  <!-- Output -->
  <div>
    <label class="block text-[11px] font-semibold uppercase text-slate-500 mb-2">Generated Query</label>

    <textarea id="huntOutput" rows="4" readonly
      class="w-full bg-light dark:bg-gray-900 border border-border dark:border-gray-800 rounded-xl p-4 font-mono text-[12px] shadow-sm"></textarea>

    <div class="flex justify-between items-center pt-2">
      <button onclick="copyQuery()" class="text-xs underline text-accent hover:opacity-80 transition">Copy to clipboard</button>
      <button onclick="clearQuery()" class="text-[11px] text-slate-500 dark:text-gray-500 hover:underline transition">Clear</button>
    </div>

  </div>

</section>

<script>
const hints = {
  ip: "Example: 192.168.1.10 or CIDR 192.168.1.0/24",
  domain: "Example: evil.com or *.evil.com for wildcard match",
  hash: "Example: MD5/SHA1/SHA256 (paste full hash)",
  registry: "Example: HKCU\\Software\\...",
  email: "Example: *@company.com OR attacker@*",
  process: "Example: powershell.exe OR *loader*",
  mutex: "Example: Global\\MutexName OR \\\\.\\pipe\\Name"
};

document.getElementById('intelType').addEventListener('change', (e) => {
  const type = e.target.value;
  document.getElementById('iocHint').innerText = hints[type] || "";
});

function buildQuery() {
  const type = document.getElementById('intelType').value;
  const value = document.getElementById('iocValue').value.trim();
  const scope = document.getElementById('huntScope').value;
  const tact  = document.getElementById('mitreTactic').value;
  const cond  = document.getElementById('huntCondition').value.trim();

  let queryBase = "";

  if (scope === "network") {
    queryBase = `index=network_logs dest_ip="${value}" OR domain="${value}"`;
  } else if (scope === "endpoint") {
    queryBase = `index=sysmon_logs process_name="${value}" OR file_hash="${value}"`;
  } else {
    queryBase = `(index=network_logs dest_ip="${value}" OR domain="${value}") OR (index=sysmon_logs process_name="${value}" OR file_hash="${value}")`;
  }

  if (tact) {
    queryBase += ` | search mitre_tactic="${tact}"`;
  }

  if (cond) {
    queryBase += ` | search (${cond})`;
  }

  document.getElementById('huntOutput').value = queryBase;
}

function copyQuery() {
  navigator.clipboard.writeText(document.getElementById('huntOutput').value);
}

function clearQuery() {
  document.getElementById('huntOutput').value = "";
}

function clearQuery() {
  document.getElementById('huntOutput').value = "";
}
</script>
