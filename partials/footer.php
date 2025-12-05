<footer class="w-full mt-16 border-t border-ht_border bg-ht_bg2">

  <div class="max-w-7xl mx-auto px-6 py-10 grid md:grid-cols-3 gap-10 text-sm">

    <!-- Column 1 -->
    <div class="space-y-3">
      <div class="flex items-center gap-2">
        <span class="text-ht_blue font-mono text-xl">></span>
        <span class="text-white font-semibold text-lg">IntelCTX</span>
        <span class="text-[10px] px-2 py-1 bg-white/5 border border-white/10 text-gray-300 rounded-md">
        v1.4.0
      </span>
      </div>

      

      <p class="text-ht_muted text-[11px] leading-relaxed">
        © <?= date('Y') ?> IntelCTX — Cloud-native Threat Intelligence Platform for SOC, DFIR & Incident Response Teams.
      </p>
    </div>

    <!-- Column 2 -->
    <div>
      <h4 class="text-xs uppercase font-semibold text-ht_muted mb-3 tracking-wider">
        Company
      </h4>
      <ul class="space-y-1 text-[13px]">
        <li><a href="about.php" class="hover:text-ht_blue transition">About</a></li>
        <li><a href="compliance.php" class="hover:text-ht_blue transition">Compliance</a></li>
        <li><a href="terms.php" class="hover:text-ht_blue transition">Terms</a></li>
        <li><a href="faq.php" class="hover:text-ht_blue transition">FAQs</a></li>
        <li><a href="changelog.php" class="hover:text-ht_blue transition">Changelog</a></li>
      </ul>
    </div>

    <!-- Column 3 -->
    <div class="text-right md:text-left">
      <h4 class="text-xs uppercase font-semibold text-ht_muted mb-3 tracking-wider">
        Designed For
      </h4>

      <div class="flex flex-wrap md:justify-start justify-end gap-2">
        <span class="footer-pill">SOC</span>
        <span class="footer-pill">DFIR</span>
        <span class="footer-pill">Threat Intel</span>
      </div>
    </div>

  </div>
</footer>

<style>
.footer-pill {
  border:1px solid rgba(255,255,255,0.1);
  background: rgba(255,255,255,0.05);
  padding:4px 12px;
  border-radius: 14px;
  font-size:11px;
  color:#E5E7EB;
}
</style>



<!-- <style>
.footer-pill {
  border:1px solid #E5E7EB;
  background:#F1F5F9;
  padding:3px 10px;
  border-radius:16px;
  font-size:11px;
  color:#1E293B;
  margin-top:4px;
  font-weight:500;
}
.dark .footer-pill {
  background:#1F2937;
  color:#D1D5DB;
  border-color:#374151;
}
.footer-link {
  color: #d1d5db;
  transition: 0.2s;
}
.footer-link:hover {
  color: #3B82F6;
  text-decoration: underline;
}

/* Pills */
.footer-pill {
  padding: 4px 12px;
  font-size: 11px;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.12);
  color: #e5e7eb;
  border-radius: 9999px;
  backdrop-filter: blur(4px);
  transition: 0.2s;
}
.footer-pill:hover {
  background: rgba(255,255,255,0.12);
}

/* Dark still applies but smoother */
.dark .footer-pill {
  background: rgba(255,255,255,0.08);
  color: #f3f4f6;
}

</style> -->
