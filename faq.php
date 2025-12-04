<?php include 'partials/header.php'; ?>
<section class="max-w-4xl mx-auto px-6 py-10 space-y-6 text-sm text-primary">

  <h1 class="text-2xl font-bold text-accent">Frequently Asked Questions</h1>

  <div class="space-y-4">

    <details class="faq-item">
      <summary>What is the purpose of APT Intel?</summary>
      <p>It provides structured OSINT intelligence on APT groups, malware families,
         detection hypothesis, and threat hunt query templates to support modern defenders.</p>
    </details>

    <details class="faq-item">
      <summary>Is the data verified?</summary>
      <p>All content is based on public reports. Defender teams must validate intelligence
         before production usage.</p>
    </details>

    <details class="faq-item">
      <summary>Does this platform attribute attackers?</summary>
      <p>It shows suspected sponsor data from OSINT. It does not guarantee attribution accuracy.</p>
    </details>

    <details class="faq-item">
      <summary>Can profiles be exported?</summary>
      <p>Yes, you can export profiles as TXT/MD for documentation purposes.</p>
    </details>

    <details class="faq-item">
      <summary>Can I use the intel to build SIEM detections?</summary>
      <p>Yes, under research and detection engineering scope — not for offensive usage.</p>
    </details>

  </div>
</section>

<style>
.faq-item {
  @apply bg-white border border-border rounded-xl p-5 shadow-sm;
}
.faq-item summary {
  @apply font-semibold text-sm cursor-pointer text-accent;
}
.faq-item p {
  @apply mt-2 text-xs text-slate-600;
}
</style>
</main>

<?php include 'partials/footer.php'; ?>
