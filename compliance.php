<?php include 'partials/header.php'; ?>

<section class="max-w-5xl mx-auto px-6 py-12 space-y-10 text-primary">

    <!-- Page Header -->
    <div class="space-y-1">
        <h1 class="text-3xl font-extrabold tracking-tight text-ht_blue">
            Compliance & Acceptable Use
        </h1>
        <p class="text-xs text-ht_muted">Operational governance guidelines for IntelCTX users</p>
    </div>

    <!-- Compliance Card -->
    <div class="backdrop-blur-xl bg-ht_bg2/70 border border-ht_border/50 
                rounded-2xl shadow-lg p-8 space-y-8">

        <!-- Badge Row -->
        <div class="flex items-center gap-3">
            <span class="px-3 py-1 text-[10px] uppercase rounded-full 
                         bg-ht_blue/20 text-ht_blue font-semibold tracking-wide">
                Policy Framework
            </span>
            <span class="px-3 py-1 text-[10px] uppercase rounded-full 
                         bg-white/10 border border-white/20 text-ht_muted font-mono">
                Updated <?= date("d M Y") ?>
            </span>
        </div>

        <!-- Section Block -->
        <div class="compliance-section">
            <h2 class="compliance-title">Intended Users</h2>
            <p class="compliance-text">
                SOC Analysts, DFIR Responders, Threat Intelligence Teams, 
                Detection Engineers, and Cyber Defense Practitioners.
            </p>
        </div>

        <div class="compliance-section">
            <h2 class="compliance-title">Acceptable Use</h2>
            <p class="compliance-text">
                IntelCTX may be used for defensive cyber operations including:
                threat research, detection hypothesis development, incident preparedness, 
                correlation analysis, and intelligence-driven defense.
            </p>
        </div>

        <div class="compliance-section">
            <h2 class="compliance-title">Unacceptable Use</h2>
            <ul class="compliance-list">
                <li>Offensive cyber operations or adversary emulation without authorization.</li>
                <li>Exploit weaponization or malware development using platform intel.</li>
                <li>Attempting to map real-world targets for cyber-attacks.</li>
                <li>Behavior that resembles attacker reconnaissance or PCA (Pre-Compromise Activity).</li>
            </ul>
        </div>

        <div class="compliance-section">
            <h2 class="compliance-title">Intel Confidence</h2>
            <p class="compliance-text">
                IntelCTX aggregates OSINT & CTI sources. Attribution, indicators, 
                and TTP details must be validated against upstream vendor reports 
                before applying them in production environments.
            </p>
        </div>

        <div class="compliance-section">
            <h2 class="compliance-title">Data Contribution Rules</h2>
            <p class="compliance-text">
                All submitted notes, comments, metadata updates, YARA patterns, or 
                references must be factual, non-defamatory, and backed by 
                verifiable OSINT or threat research documentation.
            </p>
        </div>

    </div>

</section>
                </main>
<?php include 'partials/footer.php'; ?>

<style>
.compliance-section {
    @apply space-y-2;
}

.compliance-title {
    @apply text-sm font-bold uppercase tracking-wide text-ht_blue;
}

.compliance-text {
    @apply text-xs leading-relaxed text-ht_muted;
}

.compliance-list {
    @apply list-disc list-inside text-xs text-ht_muted space-y-1;
}
</style>
