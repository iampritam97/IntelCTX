<?php include 'partials/header.php'; ?>

<section class="max-w-4xl mx-auto px-6 py-12 space-y-8 text-sm text-primary">

  <!-- Header -->
  <div class="space-y-1">
    <h1 class="text-3xl font-extrabold tracking-tight text-accent">Threat Intelligence Governance</h1>
    <p class="text-xs text-ht_muted">Standards for CTI quality, OSINT attribution, tagging discipline, and contribution rules.</p>
  </div>

  <!-- Governance Panel -->
  <div class="bg-ht_bg2 border border-ht_border rounded-xl p-6 shadow-sm space-y-6">

    <!-- Section -->
    <div class="gov-section">
      <h2 class="gov-title text-ht_blue">Intel Source Assurance</h2>
      <p class="gov-text">
        All intelligence within IntelCTX is sourced exclusively from OSINT:  
        vendor reports, CERT advisories, vulnerability disclosures, threat blogs,
        and public research papers. No classified or proprietary intel is included.
      </p>
    </div>

    <!-- Section -->
    <div class="gov-section">
      <h2 class="gov-title text-ht_blue">Attribution & Sponsor Sensitivity</h2>
      <p class="gov-text">
        APT sponsor attribution is <b>never definitive</b>.  
        All sponsor/country labels must remain OSINT-backed, conservative, and must avoid
        making claims not supported by credible public research.
      </p>
      <p class="gov-text">
        Analysts must include references when modifying attribution fields.
      </p>
    </div>

    <!-- Section -->
    <div class="gov-section">
      <h2 class="gov-title text-ht_blue">Tagging & Taxonomy Discipline</h2>
      <ul class="gov-list">
        <li>Malware families, tools, industries, and countries must use controlled vocabulary.</li>
        <li>Admin-maintained master lists ensure consistent categorization across the platform.</li>
        <li>New tags must be approved before introduction to maintain taxonomy integrity.</li>
      </ul>
    </div>

    <!-- Section -->
    <div class="gov-section">
      <h2 class="gov-title text-ht_blue">OSINT Usage & Legal Boundaries</h2>
      <p class="gov-text">
        IntelCTX is designed for defensive threat intelligence work only:  
        SOC investigations, DFIR, detection engineering, threat hunting,
        campaign correlation, and intelligence enrichment.
      </p>
      <p class="gov-text">
        It must not be used for offensive operations, exploitation support, targeting,
        or any activity violating organizational, regional, or international laws.
      </p>
    </div>

    <!-- Section -->
    <div class="gov-section">
      <h2 class="gov-title text-ht_blue">IOC Handling & Quality Controls</h2>
      <ul class="gov-list">
        <li>Domains, IPs, hashes, registry paths, and URLs must be validated before submission.</li>
        <li>Do not include live malicious URLs without defanging (e.g. <code>hxxp://</code>, <code>[.]</code>).</li>
        <li>Batch IOCs must be grouped by type for enrichment consistency.</li>
        <li>Analysts must avoid bulk copying from unverified sources.</li>
      </ul>
    </div>

    <!-- Section -->
    <div class="gov-section">
      <h2 class="gov-title text-ht_blue">Intel Contribution Workflow</h2>
      <ul class="gov-list">
        <li>All contributions must include at least one public reference.</li>
        <li>Narratives should remain factual, evidence-based, and non-speculative.</li>
        <li>No defamatory statements or geopolitically sensitive claims beyond OSINT.</li>
        <li>Every edit triggers audit logging for transparency and compliance.</li>
      </ul>
    </div>

    <!-- Section -->
    <div class="gov-section">
      <h2 class="gov-title text-ht_blue">Analyst Responsibility</h2>
      <p class="gov-text">
        CTI analysts are responsible for ensuring correct interpretation of OSINT,
        avoiding misattribution, and maintaining accuracy when tagging campaigns,
        families, or actor relationships.
      </p>
    </div>

  </div>
</section>

<style>
.gov-section {
  @apply space-y-2 pb-4 border-b border-ht_border/40;
}

.gov-title text-ht_blue {
  @apply text-sm font-semibold text-ht_blue tracking-wide;
}

.gov-text {
  @apply text-xs leading-relaxed text-ht_muted;
}

.gov-list {
  @apply list-disc list-inside text-xs text-ht_muted space-y-1;
}

/* Last item no border */
.gov-section:last-child {
  @apply border-none;
}
</style>

</main>
<?php include 'partials/footer.php'; ?>
