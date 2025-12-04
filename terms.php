<?php include 'partials/header.php'; ?> 

<section class="max-w-5xl mx-auto px-6 py-14 space-y-12">

    <!-- Page Header -->
    <div class="space-y-2">
        <h1 class="text-3xl font-extrabold tracking-tight text-white">Terms of Service</h1>
        <p class="text-xs text-ht_muted">
            Last updated: <?= date('F Y'); ?>
        </p>
    </div>

    <!-- TOC -->
    <div class="bg-ht_bg2/70 backdrop-blur-xl border border-ht_border rounded-xl shadow p-6">
        <h2 class="text-sm font-semibold text-ht_blue mb-3">Contents</h2>
        <ul class="text-xs text-ht_muted space-y-1 leading-5">
            <li><a href="#usage" class="hover:text-ht_blue">Platform Usage</a></li>
            <li><a href="#accuracy" class="hover:text-ht_blue">Data Accuracy</a></li>
            <li><a href="#intent" class="hover:text-ht_blue">Non-Malicious Intent</a></li>
            <li><a href="#responsibility" class="hover:text-ht_blue">User Responsibility</a></li>
            <li><a href="#contribution" class="hover:text-ht_blue">Contribution Guidelines</a></li>
            <li><a href="#restrictions" class="hover:text-ht_blue">Restrictions</a></li>
            <li><a href="#liability" class="hover:text-ht_blue">Liability</a></li>
        </ul>
    </div>

    <!-- Terms Content -->
    <div class="bg-ht_bg2/70 backdrop-blur-xl border border-ht_border rounded-xl shadow-lg p-10 space-y-12 leading-relaxed text-[13px] text-ht_text">

        <!-- SECTION TEMPLATE -->
        <div id="usage">
            <div class="flex items-center gap-3 mb-2">
                <span class="w-1.5 h-6 bg-ht_blue rounded-full"></span>
                <h2 class="text-sm font-semibold text-ht_blue">Platform Usage</h2>
            </div>
            <p class="text-ht_muted">
                IntelCTX is an OSINT-based cyber threat intelligence platform created for security research,
                detection engineering, incident response, and educational purposes only.
            </p>
        </div>

        <div class="border-t border-ht_border"></div>

        <div id="accuracy">
            <div class="flex items-center gap-3 mb-2">
                <span class="w-1.5 h-6 bg-ht_blue rounded-full"></span>
                <h2 class="text-sm font-semibold text-ht_blue">Data Accuracy</h2>
            </div>
            <p class="text-ht_muted">
                All intelligence is sourced from publicly available research. IntelCTX does not guarantee
                attribution precision, completeness, or real-time accuracy.
            </p>
        </div>

        <div class="border-t border-ht_border"></div>

        <div id="intent">
            <div class="flex items-center gap-3 mb-2">
                <span class="w-1.5 h-6 bg-ht_blue rounded-full"></span>
                <h2 class="text-sm font-semibold text-ht_blue">Non-Malicious Intent</h2>
            </div>
            <p class="text-ht_muted">
                The platform must not be used for offensive cyber operations, unauthorized system access,
                or any malicious activity.
            </p>
        </div>

        <div class="border-t border-ht_border"></div>

        <div id="responsibility">
            <div class="flex items-center gap-3 mb-2">
                <span class="w-1.5 h-6 bg-ht_blue rounded-full"></span>
                <h2 class="text-sm font-semibold text-ht_blue">User Responsibility</h2>
            </div>
            <p class="text-ht_muted">
                Users agree to verify any intelligence before deploying it into detection pipelines,
                automation systems, or security investigations.
            </p>
        </div>

        <div class="border-t border-ht_border"></div>

        <div id="contribution">
            <div class="flex items-center gap-3 mb-2">
                <span class="w-1.5 h-6 bg-ht_blue rounded-full"></span>
                <h2 class="text-sm font-semibold text-ht_blue">Contribution Guidelines</h2>
            </div>
            <p class="text-ht_muted">
                Notes, edits, or comments added to group profiles must be factual, non-defamatory,
                and supported by publicly verifiable research.
            </p>
        </div>

        <div class="border-t border-ht_border"></div>

        <div id="restrictions">
            <div class="flex items-center gap-3 mb-2">
                <span class="w-1.5 h-6 bg-ht_blue rounded-full"></span>
                <h2 class="text-sm font-semibold text-ht_blue">Restrictions</h2>
            </div>
            <ul class="list-disc list-inside text-ht_muted space-y-1">
                <li>Scraping IntelCTX data without permission</li>
                <li>Commercial resale or redistribution of platform intelligence</li>
                <li>Hosting exploits, malware, or operational attack content</li>
                <li>Publishing tutorials that directly enable offensive operations</li>
            </ul>
        </div>

        <div class="border-t border-ht_border"></div>

        <div id="liability">
            <div class="flex items-center gap-3 mb-2">
                <span class="w-1.5 h-6 bg-ht_blue rounded-full"></span>
                <h2 class="text-sm font-semibold text-ht_blue">Liability</h2>
            </div>
            <p class="text-ht_muted">
                IntelCTX is not liable for damages arising from the use of any threat intelligence,
                detections, or research provided through the platform.
            </p>
        </div>

    </div>

</section>

</main>
<?php include 'partials/footer.php'; ?> 
