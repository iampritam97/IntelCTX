<?php include 'partials/header.php'; ?>

<section class="max-w-7xl mx-auto px-6 py-12 space-y-10">

  <!-- Title -->
  <div>
    <h1 class="text-2xl font-bold text-white">Threat Hunt Query Builder</h1>
    <p class="text-xs text-ht_muted mt-1">Build multi-SIEM hunt queries using IOCs, patterns, MITRE techniques & behavioural signatures.</p>
  </div>

  <!-- Builder Panel -->
  <div class="apt-card p-6 rounded-xl space-y-8">

    <!-- Top Grid -->
    <div class="grid md:grid-cols-3 gap-6">

      <div>
        <label class="label">IOC Type</label>
        <select id="iocType" class="input-modern">
          <option value="ip">IP Address</option>
          <option value="domain">Domain</option>
          <option value="hash">File Hash</option>
          <option value="process">Process Name</option>
          <option value="registry">Registry Path</option>
          <option value="email">Email</option>
          <option value="url">URL</option>
        </select>
      </div>

      <div>
        <label class="label">IOC List</label>
        <textarea id="iocValues" rows="3" class="input-modern resize-y" placeholder="Enter one IOC per line…"></textarea>
      </div>

      <div>
        <label class="label">Log Source</label>
        <select id="logSource" class="input-modern">
          <option value="network">Network Logs</option>
          <option value="sysmon">Sysmon / EDR</option>
          <option value="dns">DNS Logs</option>
          <option value="proxy">Proxy Logs</option>
          <option value="email">Email Security</option>
          <option value="all">All Sources</option>
        </select>
      </div>

    </div>

    <!-- MITRE Technique -->
    <div>
      <label class="label">MITRE Technique</label>
      <select id="mitreTechnique" class="input-modern">
        <option value="">None</option>
        <option value="T1059">T1059 — Script Execution</option>
        <option value="T1071">T1071 — C2 Communications</option>
        <option value="T1566">T1566 — Phishing</option>
        <option value="T1021">T1021 — Lateral Movement</option>
        <option value="T1055">T1055 — Process Injection</option>
      </select>
    </div>

    <!-- Threat Pattern -->
    <div>
      <label class="label">Threat Pattern</label>
      <select id="patternType" class="input-modern">
        <option value="">None</option>
        <option value="powershell">Suspicious PowerShell</option>
        <option value="dll">DLL Side Loading</option>
        <option value="beacon">C2 Beaconing</option>
        <option value="inject">Process Injection</option>
        <option value="exfil">Data Exfiltration</option>
      </select>
    </div>

    <!-- Checkboxes -->
    <div class="flex gap-6 text-xs text-ht_muted">
      <label class="flex items-center gap-1">
        <input type="checkbox" id="caseInsensitive"> Case-insensitive
      </label>
      <label class="flex items-center gap-1">
        <input type="checkbox" id="wildcardMode"> Wildcards
      </label>
      <label class="flex items-center gap-1">
        <input type="checkbox" id="addTimeRange"> Add 7-day time range
      </label>
    </div>

    <!-- Build button -->
    <button onclick="buildHunt()" class="btn-primary">
      Build Query
    </button>

  </div>


  <!-- OUTPUT PANEL -->
  <div class="apt-card p-6 rounded-xl space-y-4">

    <!-- Tabs -->
    <div class="flex gap-3 text-xs">
      <button class="huntTab activeTab" onclick="switchTab('splunk')">Splunk</button>
      <button class="huntTab" onclick="switchTab('elastic')">Elastic DSL</button>
      <button class="huntTab" onclick="switchTab('kql')">KQL</button>
      <button class="huntTab" onclick="switchTab('sql')">SQL-like</button>
    </div>

    <textarea id="output_splunk" class="huntOutput"></textarea>
    <textarea id="output_elastic" class="huntOutput hidden"></textarea>
    <textarea id="output_kql" class="huntOutput hidden"></textarea>
    <textarea id="output_sql" class="huntOutput hidden"></textarea>

    <button onclick="copyCurrent()" class="text-xs text-ht_blue underline">Copy Query</button>

    <p id="analysisBox" class="text-[11px] text-ht_muted"></p>
  </div>

  <!-- Hypothesis -->
  <div id="hypothesisPanel" class="apt-card p-6 rounded-xl space-y-3 hidden">
      <h3 class="text-sm font-bold text-ht_blue">Detection Hypothesis</h3>
      <p id="hypothesisText" class="text-xs text-gray-300"></p>

      <div>
        <h4 class="hypo-label">Reasoning</h4>
        <p id="hypothesisReason" class="text-xs text-ht_muted"></p>
      </div>

      <div>
        <h4 class="hypo-label">Expected Findings</h4>
        <p id="hypothesisFindings" class="text-xs text-ht_muted"></p>
      </div>
  </div>

</section>


<style>
/* --- UNIVERSAL DARK INPUT STYLING (NO @apply used) --- */

.input-base {
  width: 100%;
  padding: 8px 10px;
  font-size: 12px;
  font-family: monospace;
  background-color: #1e1e1e; /* matches ht_bg */
  color: #e5e7eb; /* ht_text */
  border: 1px solid #374151; /* ht_border */
  border-radius: 6px;
}

.input-base:focus {
  outline: none;
  border-color: #2563eb; /* ht_blue */
}

/* Select dropdown */
.input-select {
  appearance: none;
  background-position: right 8px center;
  background-repeat: no-repeat;
  background-image: url("data:image/svg+xml;utf8,<svg fill='white' viewBox='0 0 20 20'><path d='M5.25 7.5l4.75 4.75 4.75-4.75'/></svg>");
}

/* Textarea */
.input-textarea {
  resize: vertical;
}

.label {
  font-size: 11px;
  text-transform: uppercase;
  font-weight: 600;
  color: #9ca3af; /* ht_muted */
  margin-bottom: 4px;
  display: block;
}

/* Tabs */
.huntTab {
  padding: 8px 14px;
  border: 1px solid #374151;
  background: #1e1e1e;
  color: #e5e7eb;
  border-radius: 6px;
  cursor: pointer;
  font-size: 12px;
}

.huntTab:hover {
  background: #2a2a2a;
}

.activeTab {
  background: #2563eb !important;
  color: white !important;
  border-color: #2563eb !important;
}

/* Output panels */
.huntOutput {
  width: 100%;
  height: 180px;
  padding: 12px;
  background: #111827;
  color: #e5e7eb;
  border: 1px solid #374151;
  border-radius: 8px;
  font-family: monospace;
  font-size: 12px;
}

/* Checkbox line */
.checkbox-row {
  display: flex;
  align-items: center;
  gap: 20px;
  color: #9ca3af;
  font-size: 12px;
}

/* Glass card */
.apt-card {
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.06);
  backdrop-filter: blur(12px);
  border-radius: 14px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.3);
}

/* Modern input */
.input-modern {
  width: 100%;
  padding: 10px 12px;
  font-size: 12px;
  font-family: monospace;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  color: #e5e7eb;
  border-radius: 8px;
  transition: 0.2s;
}
.input-modern:focus {
  border-color: #2563eb;
  background: rgba(255,255,255,0.08);
  outline: none;
}

/* Label */
.label {
  font-size: 11px;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  margin-bottom: 4px;
}

/* Primary button */
.btn-primary {
  background: #2563eb;
  color: white;
  padding: 10px 18px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  transition: 0.2s;
}
.btn-primary:hover {
  background: #1e4fcc;
}

/* Tabs */
.huntTab {
  padding: 8px 16px;
  font-size: 12px;
  border-radius: 6px;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.08);
  color: #e5e7eb;
  transition: 0.2s;
}
.huntTab:hover {
  background: rgba(255,255,255,0.08);
}
.activeTab {
  background: #2563eb !important;
  border-color: #2563eb !important;
  color: white !important;
}

/* Output textarea */
.huntOutput {
  width: 100%;
  height: 180px;
  background: rgba(0,0,0,0.45);
  border: 1px solid rgba(255,255,255,0.08);
  padding: 12px;
  border-radius: 10px;
  font-family: monospace;
  font-size: 12px;
  color: #e5e7eb;
  resize: vertical;
}

/* Hypothesis labels */
.hypo-label {
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: #9ca3af;
  margin-top: 6px;
}


</style>

<script>
function switchTab(t) {
  document.querySelectorAll('.huntOutput').forEach(x => x.classList.add('hidden'));
  document.getElementById('output_'+t).classList.remove('hidden');

  document.querySelectorAll('.huntTab').forEach(x => x.classList.remove('activeTab'));
  event.target.classList.add('activeTab');
}

function buildHunt() {
  const type = document.getElementById("iocType").value;
  const values = document.getElementById("iocValues").value.trim().split("\n").filter(x=>x.trim()!=="");
  const log = document.getElementById("logSource").value;
  const mitre = document.getElementById("mitreTechnique").value;
  const pattern = document.getElementById("patternType").value;
  const ci = document.getElementById("caseInsensitive").checked;
  const wc = document.getElementById("wildcardMode").checked;
  const tr = document.getElementById("addTimeRange").checked;

  let field = {
    ip: "dest_ip",
    domain: "domain",
    hash: "file_hash",
    process: "process_name",
    registry: "registry_path",
    email: "email",
    url: "url"
  }[type];

  let conds = values.map(v => {
    if (wc) v = `*${v}*`;
    return `${field}="${v}"`;
  }).join(" OR ");

  let timeCond = tr ? " earliest=-7d@d latest=now " : "";
  let caseCmd = ci ? " | eval _raw=lower(_raw)" : "";

  // Base Splunk Query
  let splunk = `index=${log} (${conds})${caseCmd}${timeCond}`;
  if (mitre) splunk += ` | search mitre_technique="${mitre}"`;

  // Pattern Add-ons
  const patternMap = {
    powershell: ` | search process_name="powershell.exe" OR CommandLine="*EncodedCommand*"`,
    dll: ` | search ImageLoaded="*.dll"`,
    beacon: ` | stats count by dest_ip dest_port | where count > 20`,
    inject: ` | search EventCode=7 ImageLoaded="*ntdll.dll"`,
    exfil: ` | search bytes_out > 5000000`
  };
  if (pattern && patternMap[pattern]) splunk += patternMap[pattern];

  // Generate All 4 Query Types
  document.getElementById("output_splunk").value = splunk;
  document.getElementById("output_elastic").value =
`{
  "query": {
    "bool": {
      "should": [
        ${values.map(v=>`{ "match_phrase": { "${field}": "${v}" } }`).join(",\n        ")}
      ]
    }
  }
}`;
  document.getElementById("output_kql").value =
    values.map(v => `${field}: "${v}"`).join(" or ");
  document.getElementById("output_sql").value =
    `SELECT * FROM logs WHERE ${conds.replace(/ OR /g, " OR ")}`;
generateHypothesis(values, type, log, mitre, pattern);

  document.getElementById("analysisBox").innerText =
    `Generated hunt based on ${values.length} IOC(s), ${log} logs, ${pattern?pattern:"no pattern"}, ${mitre?("MITRE "+mitre):"no technique"}.`;
}
function generateHypothesis(iocs, type, log, mitre, pattern) {

  const firstIOC = iocs[0];

  const iocMap = {
    ip: "IP address",
    domain: "malicious domain",
    hash: "malware file hash",
    process: "suspicious process",
    registry: "registry modification",
    email: "malicious email sender",
    url: "URL indicator"
  };

  const logMap = {
    network: "network telemetry",
    sysmon: "endpoint & Sysmon logs",
    dns: "DNS query logs",
    proxy: "proxy logs",
    email: "email gateway logs",
    all: "multiple log sources"
  };

  const patternReadable = {
    powershell: "potential malicious PowerShell activity",
    dll: "DLL side-loading behavior",
    beacon: "command-and-control beaconing pattern",
    inject: "process injection behavior",
    exfil: "possible data exfiltration"
  }[pattern] || "adversary activity";

  // Hypothesis sentence
  const hypothesis = `
    If an adversary is active inside the environment, then the presence of 
    ${iocMap[type]} '${firstIOC}' will produce detectable ${pattern ? patternReadable : "anomalous"} events 
    within ${logMap[log]}${mitre ? ` associated with MITRE technique ${mitre}` : ""}.
  `;

  const reasoning = `
    The selected indicator '${firstIOC}' is commonly observed in threat activity associated 
    with ${patternReadable}. By enriching this IOC with log telemetry from ${logMap[log]},
    defenders can identify behavioral traces left by the attacker.
  `;

  const findings = `
    Detection should reveal events matching IOC '${firstIOC}', correlated with 
    process, network, or user activity anomalies. Additional pivot fields such as 
    parent process, destination ports, rare DNS queries, or unusual data transfers 
    may support the hypothesis.
  `;

  // Update UI
  document.getElementById("hypothesisPanel").classList.remove("hidden");
  document.getElementById("hypothesisText").innerText = hypothesis.trim();
  document.getElementById("hypothesisReason").innerText = reasoning.trim();
  document.getElementById("hypothesisFindings").innerText = findings.trim();
}

function copyCurrent() {
  const visible = document.querySelector('.huntOutput:not(.hidden)').value;
  navigator.clipboard.writeText(visible);
}
</script>

</main>
<?php include 'partials/footer.php'; ?>
