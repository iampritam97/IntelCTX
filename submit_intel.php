<?php include 'partials/header.php'; ?>

<section class="max-w-4xl mx-auto px-6 py-12 space-y-10">

    <!-- Header -->
    <div>
        <h1 class="text-3xl font-extrabold text-ht_blue tracking-tight">Submit Threat Intelligence</h1>
        <p class="text-xs text-ht_muted mt-1">
            Contribute structured intelligence used by defenders, researchers, and detection engineers.
        </p>
    </div>

    <!-- FORM CONTAINER -->
    <form method="POST" action="admin/insert_submission.php"
        class="backdrop-blur-xl bg-ht_bg2/60 border border-ht_border rounded-2xl shadow-lg p-8 space-y-8">

        <!-- SECTION: CATEGORY -->
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="w-1.5 h-5 bg-ht_blue rounded-full"></span>
                <label class="form-label text-ht_muted">Category</label>
            </div>

            <select name="category" class="input-field" required>
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

        <!-- SECTION: NAME -->
        <div>
            <label class="form-label">Threat / Entity Name</label>
            <input type="text" name="entity_name" 
                placeholder="APT28, Emotet, Cobalt Strike…" 
                class="input-field" required>
        </div>

        <!-- SECTION: ALIASES -->
        <div>
            <label class="form-label">Aliases / Tags (Optional)</label>
            <input type="text" name="aliases" 
                placeholder="Fancy Bear; Sednit; Sofacy…" 
                class="input-field">
        </div>

        <!-- SECTION: NARRATIVE -->
        <div>
            <label class="form-label">Notable Attacks / Narrative Intelligence</label>
            <textarea name="narrative" rows="3"
                class="input-field textarea-field"
                placeholder="Short case study, intrusion chain summary, or threat overview…"></textarea>
        </div>

        <!-- SECTION: IOCs -->
        <div>
            <label class="form-label">Indicators of Compromise</label>
            <textarea name="ioc" rows="4"
                class="input-field textarea-field font-mono text-xs"
                placeholder="Domains, IPs, Hashes, Emails, Registry, YARA… one per line"></textarea>
        </div>

        <!-- SECTION: DETECTION -->
        <div>
            <label class="form-label">Defensive Opportunity / Detection Ideas</label>
            <textarea name="detection" rows="3"
                class="input-field textarea-field"
                placeholder="EDR/SIEM detection ideas, hunting logic, log pivot notes…"></textarea>
        </div>

        <!-- SECTION: REFERENCES -->
        <div>
            <label class="form-label">References / Source</label>
            <textarea name="reference" rows="2"
                class="input-field textarea-field"
                placeholder="Report name, CVE ID, source URL, vendor analysis…" required></textarea>
        </div>

        <!-- SUBMIT -->
        <div class="flex justify-end pt-4">
            <button type="submit"
                class="px-5 py-2 bg-ht_blue text-white rounded-lg text-xs font-semibold hover:bg-ht_blue2 transition">
                Submit Intelligence
            </button>
        </div>

    </form>
</section>

<style>
/* ENTERPRISE INPUT STYLE */

.form-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .4px;
    font-weight: 600;
    color: #9CA3AF;
    margin-bottom: 6px;
}

.input-field {
    width: 100%;
    background: rgba(255,255,255,0.03);
    border: 1px solid #1F242C;
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 13px;
    color: #E5E7EB;
    transition: 0.2s;
}

.input-field:focus {
    outline: none;
    border-color: #3B82F6;
    box-shadow: 0 0 0 2px rgba(59,130,246,0.25);
}

.textarea-field {
    resize: vertical;
}
</style>
</main>

<?php include 'partials/footer.php'; ?>
