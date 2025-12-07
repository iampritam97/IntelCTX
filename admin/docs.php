<?php include __DIR__ . '/../partials/header.php'; ?>

<section class="max-w-4xl mx-auto px-6 py-12 text-sm">

    <h1 class="text-3xl font-bold text-ht_blue mb-6">IntelCTX API Documentation</h1>

    <p class="text-ht_muted mb-6">
        This API provides programmatic access to APT profiles, malware families,
        tools, and exports. All requests require a valid API token.
    </p>

    <div class="space-y-6">

        <div class="bg-ht_bg2 p-4 rounded-lg border border-ht_border">
            <h2 class="text-lg font-bold text-white">Authentication</h2>
            <p class="text-ht_muted text-xs mt-2">
                Include your token in every request:
            </p>
            <pre class="bg-black/40 p-3 rounded text-xs mt-2 font-mono">
GET /api/?resource=apt&token=YOUR_TOKEN
            </pre>
        </div>

        <div class="bg-ht_bg2 p-4 rounded-lg border border-ht_border">
            <h2 class="text-lg font-bold text-white">Resources</h2>

            <ul class="list-disc pl-5 text-ht_muted text-xs mt-2">
                <li><strong>apt</strong> — Advanced Persistent Threat groups</li>
                <li><strong>malware</strong> — Malware families</li>
                <li><strong>tools</strong> — Adversary tools</li>
                <li><strong>all</strong> — Export everything (requires scope: export)</li>
            </ul>
        </div>

        <div class="bg-ht_bg2 p-4 rounded-lg border border-ht_border">
            <h2 class="text-lg font-bold text-white">Scopes</h2>
            <pre class="bg-black/40 p-3 rounded text-xs mt-2 font-mono">
read        — Read-only access  
write       — Modify (future use)  
export      — Download full dataset  
admin       — Full privileges
            </pre>
        </div>

        <div class="bg-ht_bg2 p-4 rounded-lg border border-ht_border">
            <h2 class="text-lg font-bold text-white">Examples</h2>

            <pre class="bg-black/40 p-3 rounded text-xs mt-3 font-mono">
GET /api/?resource=malware&token=XYZ123
GET /api/?resource=apt&token=XYZ123
GET /api/?resource=all&token=XYZ123
            </pre>
        </div>

    </div>

</section>
</main>
<?php include __DIR__ . '/../partials/footer.php'; ?>
