<?php include 'partials/header.php'; ?>

<section class="max-w-5xl mx-auto px-6 py-14 space-y-12 text-primary">

    <!-- HEADER -->
    <!-- <div class="space-y-2">
        <h1 class="text-4xl font-extrabold tracking-tight text-ht_blue">
            About IntelCTX
        </h1>
        <p class="text-xs text-ht_muted">Modern Cyber Threat Intelligence for Modern Defenders</p>
    </div> -->

        <div>
    <h1 class="text-2xl font-bold text-white">About IntelCTX</h1>
    <p class="text-xs text-ht_muted mt-1">Modern Cyber Threat Intelligence for Modern Defenders</p>
  </div>

    <!-- INTRO CARD -->
    <div class="backdrop-blur-xl bg-ht_bg2/70 border border-ht_border/40 rounded-2xl shadow-lg p-8 space-y-6">
        <p class="text-sm text-ht_text leading-relaxed">
            IntelCTX is a cloud-native Cyber Threat Intelligence (CTI) platform built 
            for SOC teams, DFIR responders, threat hunters, and detection engineers. 
            Its mission is simple — to make high-quality intelligence accessible, 
            structured, and operationally ready for real-world defense.
        </p>

        <div class="flex flex-wrap gap-3 pt-2">
            <span class="px-3 py-1 text-[10px] uppercase rounded-full bg-ht_blue/20 text-ht_blue font-semibold">
                CTI Platform
            </span>
            <span class="px-3 py-1 text-[10px] uppercase rounded-full bg-white/10 text-ht_muted border border-white/20">
                OSINT Enriched
            </span>
            <span class="px-3 py-1 text-[10px] uppercase rounded-full bg-green-500/20 text-green-400 font-semibold">
                Defender First
            </span>
        </div>
    </div>

    <!-- OUR MISSION -->
    <div class="space-y-4">
        <h2 class="text-xl font-bold text-ht_blue">Our Mission</h2>
        <div class="backdrop-blur-xl bg-ht_bg2/60 border border-ht_border/40 p-6 rounded-xl space-y-3">
            <p class="text-sm text-ht_muted leading-relaxed">
                To empower defenders with actionable, structured, and context-rich threat data that 
                enhances detection engineering, incident response, and proactive cyber defense.
            </p>
            <p class="text-sm text-ht_muted leading-relaxed">
                IntelCTX bridges the gap between public OSINT and enterprise SOC workflows, turning 
                raw intelligence into something measurable, pivotable, and operational.
            </p>
        </div>
    </div>

    <!-- WHAT WE PROVIDE -->
    <div class="space-y-4">
        <h2 class="text-xl font-bold text-ht_blue">What We Provide</h2>

        <div class="grid md:grid-cols-2 gap-6">

            <div class="bg-ht_bg2/70 border border-ht_border/40 rounded-xl p-6 backdrop-blur-lg">
                <h3 class="text-sm font-semibold text-ht_text mb-2">APT Encyclopedia</h3>
                <p class="text-xs text-ht_muted">
                    Fully enriched attacker profiles, mapped to MITRE ATT&CK, with IOCs, campaigns,
                    tools, malware, and detection opportunities.
                </p>
            </div>

            <div class="bg-ht_bg2/70 border border-ht_border/40 rounded-xl p-6 backdrop-blur-lg">
                <h3 class="text-sm font-semibold text-ht_text mb-2">Malware & Tool Intelligence</h3>
                <p class="text-xs text-ht_muted">
                    Deep-dive intelligence on malware families and adversary tools used across the kill chain.
                </p>
            </div>

            <div class="bg-ht_bg2/70 border border-ht_border/40 rounded-xl p-6 backdrop-blur-lg">
                <h3 class="text-sm font-semibold text-ht_text mb-2">Threat Hunt Query Builder</h3>
                <p class="text-xs text-ht_muted">
                    Multi-SIEM query generator for Splunk, Elastic, Sentinel/KQL, and SQL-style hunts.
                </p>
            </div>

            <div class="bg-ht_bg2/70 border border-ht_border/40 rounded-xl p-6 backdrop-blur-lg">
                <h3 class="text-sm font-semibold text-ht_text mb-2">Operational CTI</h3>
                <p class="text-xs text-ht_muted">
                    Detection hypotheses, IOCs, narrative intelligence, and analyst notes designed 
                    to integrate directly into SOC workflows.
                </p>
            </div>

        </div>
    </div>

    <!-- CORE VALUES -->
    <div class="space-y-4">
        <h2 class="text-xl font-bold text-ht_blue">Our Core Values</h2>

        <div class="grid md:grid-cols-3 gap-6">
            <div class="valueBox">
                <h3 class="valueTitle">Accuracy</h3>
                <p class="valueText">Every intel item is backed by public research, verified sources, or dominant consensus.</p>
            </div>

            <div class="valueBox">
                <h3 class="valueTitle">Transparency</h3>
                <p class="valueText">We display confidence levels, reference chains, and mapping logic to avoid misattribution.</p>
            </div>

            <div class="valueBox">
                <h3 class="valueTitle">Defender-Focused</h3>
                <p class="valueText">Everything is built for SOC, DFIR, CTI, and detection engineers — not attackers.</p>
            </div>
        </div>
    </div>

    <!-- CONTACT -->
    <div class="space-y-3 pt-4">
        <h2 class="text-xl font-bold text-ht_blue">Contact & Support</h2>
        <p class="text-sm text-ht_muted leading-relaxed">
            For collaboration, research requests, or integration opportunities, reach out at:
        </p>

        <p class="text-sm font-mono text-ht_text bg-ht_bg2/80 border border-ht_border/40 rounded-lg p-3">
            pritamdash1997@gmail.com
        </p>
    </div>

</section>
</main>
<?php include 'partials/footer.php'; ?>

<style>
.valueBox {
    @apply bg-ht_bg2/60 border border-ht_border/40 backdrop-blur-xl rounded-xl p-6;
}
.valueTitle {
    @apply text-sm font-semibold text-ht_text mb-1;
}
.valueText {
    @apply text-xs text-ht_muted leading-relaxed;
}
</style>
