<footer class="mt-16 border-t border-border bg-light dark:bg-darkbg dark:border-gray-800 transition">
  <div class="max-w-7xl mx-auto px-6 py-8 grid md:grid-cols-3 gap-6 text-sm">

    <!-- Column 1 -->
    <div class="space-y-2">
      <h3 class="text-lg font-semibold text-primary dark:text-darktext">IntelCTX</h3>  <span class="text-[10px] px-2 py-1 bg-slate-200 dark:bg-gray-800 text-slate-800 dark:text-gray-300 rounded-md font-semibold uppercase">
    Version: 1.0.0
  </span>
      <!-- <p class="text-slate-600 dark:text-gray-400 text-xs">
        Threat Intelligence for Modern Defenders
      </p> -->
      <p class="text-slate-500 dark:text-gray-500 text-[11px]">
        © <?php echo date('Y'); ?> IntelCTX - Cloud-native CTI platform designed for SOC, DFIR & Incident Response teams.
      </p>
    </div>

    <!-- Column 2 -->
    <div class="space-y-2">
      <h4 class="text-xs uppercase font-semibold text-slate-400 dark:text-gray-500 tracking-wide">Core Modules</h4>
      <ul class="text-[12px] text-primary dark:text-gray-300 space-y-1">
        <li><a href="index.php" class="hover:underline hover:text-accent transition">APT Encyclopedia</a></li>
        <li><a href="malware.php" class="hover:underline hover:text-accent transition">Malwarepedia</a></li>
        <li><a href="tools.php" class="hover:underline hover:text-accent transition">Threat Tool Registry</a></li>
        <li><a href="timeline.php" class="hover:underline hover:text-accent transition">Activity Timeline</a></li>
        <li><a href="hunter.php" class="hover:underline hover:text-accent transition">Hunt Query Builder</a></li>
      </ul>
    </div>

    <!-- Column 3 -->
    <div class="space-y-3 text-right md:text-left">
      <h4 class="text-xs uppercase font-semibold text-slate-400 dark:text-gray-500 tracking-wide">Designed For</h4>
      <div class="flex flex-wrap md:justify-start justify-end gap-2">
        <span class="footer-pill">SOC</span>
        <span class="footer-pill">DFIR</span>
        <span class="footer-pill">Threat Intel</span>
      </div>

      <!-- <button onclick="toggleTheme()" 
        class="text-[12px] mt-3 border border-border dark:border-gray-700 rounded px-3 py-1 hover:bg-slate-100 dark:hover:bg-gray-800 transition">
        Toggle Theme
      </button> -->
    </div>

  </div>
</footer>

<style>
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
</style>
