<?php include 'partials/header.php'; ?>

<section class="max-w-4xl mx-auto px-6 py-12 space-y-8 text-sm text-primary">

  <h1 class="text-3xl font-extrabold text-accent tracking-tight">Frequently Asked Questions</h1>
  <p class="text-xs text-ht_muted">Common questions about IntelCTX, data usage, features, and platform policies.</p>

  <div class="space-y-4">

    <!-- ================= CORE FAQs ================= -->

    <details class="faq-item">
      <summary>What is IntelCTX used for?</summary>
      <p>
        IntelCTX is a cloud-native cyber threat intelligence workspace built for SOC, DFIR,
        Threat Intel, and Detection Engineering teams. It aggregates OSINT-based intelligence,
        provides APT profiles, malware data, tools, detection guidance, and hunt query templates
        to support defensive operations.
      </p>
    </details>

    <details class="faq-item">
      <summary>Is the intelligence verified?</summary>
      <p>
        IntelCTX curates data exclusively from publicly available reports, advisories,
        and vendor research. While curated, users must validate intelligence before using it
        in production environments.
      </p>
    </details>

    <details class="faq-item">
      <summary>Does IntelCTX perform attribution?</summary>
      <p>
        No. Attribution is complex and outside our scope. Platform displays only OSINT-based
        alleged sponsor information for research purposes and should not be considered final.
      </p>
    </details>

    <details class="faq-item">
      <summary>Can I export APT or malware profiles?</summary>
      <p>
        Yes — profiles support export in TXT/MD format and PDF (enterprise mode). Exports are
        useful for reporting, briefings, case files, or internal knowledge bases.
      </p>
    </details>

    <details class="faq-item">
      <summary>Can I build SIEM detections with the platform?</summary>
      <p>
        Yes. IntelCTX provides detection hypotheses, TTP-driven guidance, and a Hunt Query Builder
        designed to generate Splunk, Elastic DSL, KQL, and SQL-like queries.  
        <br><b>Note:</b> Usage must remain defensive-only.
      </p>
    </details>

    <!-- ================= SECURITY & USAGE POLICY ================= -->

    <details class="faq-item">
      <summary>Is IntelCTX allowed for offensive cybersecurity use?</summary>
      <p>
        No. IntelCTX strictly prohibits offensive exploitation, red-team weaponization, or
        activity that violates law or organizational policy. The platform is intended for
        defensive security research, threat analysis, and incident response.
      </p>
    </details>

    <details class="faq-item">
      <summary>How often is threat intelligence updated?</summary>
      <p>
        Updates occur continuously as new OSINT publications, advisories, campaigns,
        and malware analyses become available. A “Last Updated Threat Feed” ticker is displayed
        in the footer for transparency.
      </p>
    </details>

    <details class="faq-item">
      <summary>Does IntelCTX store or track user search activity?</summary>
      <p>
        No personally identifiable logs are collected. Search activity is used solely for improving
        relevance and never shared or sold.
      </p>
    </details>

    <!-- ================= PLATFORM FEATURES ================= -->

    <details class="faq-item">
      <summary>What features does IntelCTX offer?</summary>
      <p>
        The current feature set includes:
        <br>• APT Encyclopedia  
        • Malwarepedia  
        • Threat Tool Registry  
        • Detection Hypotheses  
        • Hunt Query Builder  
        • Timeline of threat activities  
        • IOC and TTP summarization  
        • Admin panel with CRUD for intel management  
      </p>
    </details>

    <details class="faq-item">
      <summary>Does the platform support custom intel uploads?</summary>
      <p>
        Yes. Analysts can submit APT groups, malware families, tools, IOC packages,
        hunt templates, case studies, and more through the structured submission form.
      </p>
    </details>

    <details class="faq-item">
      <summary>Is there an API for automation?</summary>
      <p>
        API integration is part of the upcoming Enterprise Roadmap (Q2 Release).
        It will allow ingestion of CTI, IOCs, and automated enrichment pipelines.
      </p>
    </details>

    <!-- ================= DATA HANDLING ================= -->

    <details class="faq-item">
      <summary>Does IntelCTX host malware samples?</summary>
      <p>
        No malware binaries, payloads, or harmful content are ever hosted. Only textual
        intelligence and behavioral summaries are provided.
      </p>
    </details>

    <details class="faq-item">
      <summary>Can I scrape the platform?</summary>
      <p>
        Automated scraping is restricted unless approved. Please refer to the Acceptable
        Use Policy for allowed data workflows.
      </p>
    </details>

    <!-- ================= ORGANIZATION / SUPPORT ================= -->

    <details class="faq-item">
      <summary>How can I suggest improvements or report issues?</summary>
      <p>
        You can submit feature requests or report issues via the admin contact channel or the
        GitHub repository associated with IntelCTX.
      </p>
    </details>

    <details class="faq-item">
      <summary>Does IntelCTX offer enterprise support?</summary>
      <p>
        Enterprise support, SLA-based enhancements, and dedicated CTI feeds are offered in
        the upcoming IntelCTX Pro edition.
      </p>
    </details>

  </div>
</section>

<style>
.faq-item {
  @apply bg-ht_bg2 border border-ht_border rounded-xl p-5 shadow-sm transition hover:border-ht_blue;
}
.faq-item summary {
  @apply font-semibold text-sm cursor-pointer text-accent;
}
.faq-item[open] summary {
  @apply text-ht_blue;
}
.faq-item p {
  @apply mt-2 text-xs text-ht_muted leading-relaxed;
}
</style>

</main>
<?php include 'partials/footer.php'; ?>
