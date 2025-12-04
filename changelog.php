<?php include 'partials/header.php'; ?>
<style>
    .section-title {
  @apply text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-gray-600;
}
.input-box {
  @apply border border-border bg-slate-50 dark:bg-gray-800 rounded-md px-3 py-2 font-mono text-xs focus:ring-1 ring-accent transition;
}
/* Slight glow on timeline hover */
.group:hover .ring-ht_blue\/20 {
    box-shadow: 0 0 18px rgba(59,130,246,0.35);
}

</style>
<section class="max-w-5xl mx-auto px-6 py-12 space-y-12">

    <!-- Header -->
    <div>
        <h1 class="text-4xl font-extrabold tracking-tight text-ht_blue">Changelog</h1>
        <p class="text-xs text-ht_muted mt-1">Version releases, improvements & update history</p>
    </div>

    <!-- TIMELINE WRAPPER -->
    <div class="relative border-l border-ht_border/40 pl-8 space-y-12">

        <?php 
        $logs = [
            ["IntelCTX 1.0 — MVP Launch", "2025-11-30", [
                "Introduced APT Encyclopedia module with full profile structure",
                "Added secure Admin Panel with CRUD permissions",
                "IOC extraction and copy utilities added",
                "Introduced Malwarepedia & Threat Tool Registry",
                "Launched platform-wide audit logging engine"
            ]],
            ["IntelCTX 1.1 — Research Expansion", "2025-12-01", [
                "Added MITRE ATT&CK group mapping & enrichments",
                "New Malware Family detail view with capability matrices",
                "TTP Explorer with interaction & MITRE detection alignment",
                "Threat Hunt Query Library introduced",
                "Compliance & governance documentation pages added",
                "Initial Changelog panel added"
            ]],
            ["IntelCTX 1.2 — Enterprise UI Pass", "2025-12-04", [
                "Global dark theme upgrade with improved component contrast",
                "Hunt Query Builder with multi-IOC parsing & MITRE guidance",
                "APT Profile page redesigned with lifecycle graph",
                "Added PDF export engine using DomPDF",
                "Footer versioning, header redesign, and UX smoothing"
            ]],
            ["IntelCTX 1.3 — Current Development", "2025-12-05", [
                "Added unified search experience across APT, Malware, and Tools",
                "Glassmorphism UI applied to core analytic panels",
                "Live threat ticker prepared for integration",
                "Improved spacing, radii, and enterprise typography",
                "Stability improvements & backend cleanup"
            ]],
        ];
        ?>

        <?php foreach($logs as $l): ?>
        <div class="relative group">

            <!-- Dot -->
            <div class="absolute -left-3 w-3 h-3 rounded-full bg-ht_blue
                        ring-4 ring-ht_blue/20 shadow-md"></div>

            <!-- Card -->
            <div class="backdrop-blur-xl bg-ht_bg2/60 border border-ht_border 
                        rounded-xl p-6 shadow transition-all group-hover:border-ht_blue/40">

                <div class="flex justify-between items-start">
                    <h2 class="text-sm font-bold uppercase tracking-tight text-ht_blue">
                        <?= $l[0] ?>
                    </h2>

                    <time class="text-[11px] font-mono text-ht_muted">
                        <?= date("d M Y", strtotime($l[1])) ?>
                    </time>
                </div>

                <ul class="list-disc list-inside text-xs text-ht_muted mt-3 space-y-1">
                    <?php foreach($l[2] as $line): ?>
                        <li><?= htmlspecialchars($line) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endforeach; ?>

    </div>

</section>
                    </main>

<?php include 'partials/footer.php'; ?>
