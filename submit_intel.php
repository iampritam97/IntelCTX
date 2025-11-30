<?php include 'partials/header.php'; ?>

<section class="max-w-3xl mx-auto px-6 py-10 space-y-6 text-sm text-primary">

  <div>
    <h1 class="text-2xl font-bold text-accent tracking-tight">Submit Threat Intelligence</h1>
    <p class="text-xs text-slate-500">Contribute structured intel across multiple defensive research categories.</p>
  </div>

  <form method="POST" action="admin/insert_submission.php"
    class="bg-white border border-border rounded-2xl shadow-sm p-6 grid gap-5">

    <!-- Category Selector -->
    <div>
      <label class="form-label">Category</label>
      <select name="category" class="w-full input-box" required>
        <option value="">Select Category</option>
        <option value="APT Group">APT Group</option>
        <option value="Malware Family">Malware Family</option>
        <option value="Threat Tool">Threat Tool</option>
        <option value="IOC Package">IOC Package</option>
        <option value="Detection Hypothesis">Detection Hypothesis</option>
        <option value="Threat Hunt Template">Threat Hunt Template</option>
        <option value="Campaign Case Study">Campaign Case Study</option>
        <option value="CVE-to-APT Mapping">CVE-to-APT Mapping</option>
        <option value="YARA Rule">YARA Rule</option>
      </select>
    </div>

    <!-- Dynamic Inputs for Intel -->
    <div>
      <label class="form-label">Threat/Entity Name</label>
      <input type="text" name="entity_name" placeholder="APT28, Emotet, Cobalt Strike…" class="w-full input-box" required>
    </div>

    <div>
      <label class="form-label">Aliases / Tags (Optional)</label>
      <input type="text" name="aliases" placeholder="Comma/semicolon separated aliases or tags" class="w-full input-box">
    </div>

    <!-- Notable Attacks or Narrative Intelligence -->
    <div>
      <label class="form-label">Notable Attacks / Narrative Intel</label>
      <textarea name="narrative" rows="3"
        placeholder="Brief case study or notable intrusion details…"
        class="w-full input-box"></textarea>
    </div>

    <!-- IOC Input -->
    <div>
      <label class="form-label">Indicators of Compromise (Optional)</label>
      <textarea name="ioc" rows="3"
        placeholder="Domains, IPs, Hashes, Emails, Registry, YARA, one per line…"
        class="w-full font-mono text-xs input-box"></textarea>
    </div>

    <!-- Detection Hypothesis or Defensive Opportunity -->
    <div>
      <label class="form-label">Defensive Guidance / Detection Opportunity</label>
      <textarea name="detection" rows="2"
        placeholder="Log/EDR/MDE/SIEM detection opportunities…"
        class="w-full input-box"></textarea>
    </div>

    <!-- Reference -->
    <div>
      <label class="form-label">References / Source</label>
      <textarea name="reference" rows="2" placeholder="Report name, CVE ID, or author…" class="w-full input-box" required></textarea>
    </div>

    <!-- Submit -->
    <div class="flex justify-between pt-2">
      <button type="submit" class="bg-accent text-white px-5 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition">
        Submit
      </button>
    </div>

  </form>
</section>

<style>
.form-label {
  display:block;
  font-size:11px;
  text-transform:uppercase;
  font-weight:600;
  color:#64748b;
  margin-bottom:6px;
  letter-spacing:0.4px;
}
.input-box {
  border:1px solid #e2e8f0;
  background:#f8fafc;
  padding:10px 14px;
  border-radius:8px;
  font-size:14px;
  color:#0f172a;
  transition:0.2s;
}
.input-box:focus {
  border-color:#2563EB;
  outline:none;
  background:white;
  box-shadow:0 0 0 3px rgb(37 99 235 / 0.08);
}
</style>

<?php include 'partials/footer.php'; ?>
