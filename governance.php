<?php include 'partials/header.php'; ?>
<section class="max-w-4xl mx-auto px-6 py-10 space-y-6 text-sm text-primary">

  <h1 class="text-2xl font-bold tracking-tight text-accent">Threat Intelligence Governance</h1>
  <p class="text-sm text-slate-500">Compliance, tagging standards, OSINT usage and intel contribution policies</p>

  <div class="bg-white border border-border rounded-xl p-6 shadow-sm space-y-4">
    <p><strong>Intel Source:</strong> All intelligence is OSINT-based from public research reports.</p>
    <p><strong>Tag Normalization:</strong> Malware, tools, country, industries should be consistently tagged from admin master lists.</p>
    <p><strong>Attribution Risk:</strong> Sponsor country is suspected and must always be reference-backed.</p>
    <p><strong>Acceptable Usage:</strong> Research, detection engineering, SOC/IR pivoting only — not for offensive usage.</p>
    <p><strong>IOC Handling:</strong> Always validate domain/IP/hash/registry patterns before contributions.</p>
  </div>

</section>
<?php include 'partials/footer.php'; ?>
