<!-- UPDATE MODAL BACKDROP -->
<div id="updateModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur">

    <div class="flex items-center justify-center h-full px-4">

        <!-- MODAL CARD -->
        <div class="bg-ht_bg2 border border-ht_border rounded-2xl shadow-2xl w-full max-w-xl p-6 animate-scaleIn">

            <!-- HEADER -->
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-bold text-ht_blue">IntelCTX has been updated 🎉</h2>
                    <p class="text-xs text-ht_muted mt-1">
                        Welcome to Version <span class="font-semibold text-white">2.0</span>
                    </p>
                </div>
                <button onclick="closeUpdateModalTemp()" class="text-ht_muted hover:text-white transition text-xl font-bold">
                    ×
                </button>
            </div>

            <!-- CONTENT -->
            <div class="space-y-4 text-sm text-ht_text max-h-[60vh] overflow-y-auto pr-2">

                <div class="bg-white/5 border border-white/10 p-3 rounded-lg">
                    <h3 class="text-ht_blue font-semibold mb-1 text-sm">🌐 All-New APT Knowledge Graph</h3>
                    <ul class="list-disc ml-5 text-xs text-ht_muted space-y-1">
                        <li>Interactive APT ↔ Malware ↔ Tools visualization</li>
                        <li>Similarity mode powered by malware-overlap algorithms</li>
                        <li>Layouts: Force / Hierarchical / Circular</li>
                        <li>Advanced filtering & clustering</li>
                        <li>Export: PNG, JSON, CSV</li>
                    </ul>
                </div>

                <div class="bg-white/5 border border-white/10 p-3 rounded-lg">
                    <h3 class="text-ht_blue font-semibold mb-1 text-sm">🔐 Enterprise API Token System</h3>
                    <ul class="list-disc ml-5 text-xs text-ht_muted space-y-1">
                        <li>Token scopes & expiry</li>
                        <li>Regenerate & delete tokens</li>
                        <li>Enable / Disable tokens</li>
                        <li>Secure masked token preview</li>
                    </ul>
                </div>

                <div class="bg-white/5 border border-white/10 p-3 rounded-lg">
                    <h3 class="text-ht_blue font-semibold mb-1 text-sm">📊 API Logs + Analytics Dashboard</h3>
                    <ul class="list-disc ml-5 text-xs text-ht_muted space-y-1">
                        <li>Daily request charts</li>
                        <li>Browser & OS detection</li>
                        <li>Top endpoints, tokens, IPs</li>
                        <li>Per-token log viewer</li>
                    </ul>
                </div>

            </div>

            <!-- FOOTER BUTTONS -->
            <div class="mt-6 flex justify-between items-center">

                <!-- LEFT SIDE: See What's New -->
                <a href="/CHANGELOG.php" 
                   class="text-xs text-ht_blue underline hover:text-ht_blue2 transition">
                    👉 See What’s New
                </a>

                <!-- RIGHT SIDE BUTTONS -->
                <div class="flex gap-3">
                    <button onclick="remindLater()"
                        class="px-3 py-2 bg-white/10 border border-white/20 text-xs text-ht_muted rounded-lg hover:bg-white/20 transition">
                        Remind me later
                    </button>

                    <button onclick="dismissForVersion()"
                        class="px-4 py-2 bg-ht_blue text-white rounded-lg text-xs hover:bg-ht_blue2 transition shadow">
                        Continue →
                    </button>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- Animation -->
<style>
@keyframes scaleIn {
    from { transform: scale(0.85); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
.animate-scaleIn { animation: scaleIn .25s ease-out; }
</style>

<script>
const INTELCTX_VERSION = "2.0";

// ----------------------------
// Close for *this session only*
// ----------------------------
function closeUpdateModalTemp() {
    document.getElementById("updateModal").classList.add("hidden");
}

// ----------------------------
// Remind later (hide for 24 hours)
// ----------------------------
function remindLater() {
    const expireAt = Date.now() + (24 * 60 * 60 * 1000); // 24 hours
    localStorage.setItem("intelctx_remind_until", expireAt.toString());
    closeUpdateModalTemp();
}

// ----------------------------
// Dismiss permanently for this version
// ----------------------------
function dismissForVersion() {
    localStorage.setItem("intelctx_version", INTELCTX_VERSION);
    closeUpdateModalTemp();
}

// ----------------------------
// Show modal only when appropriate
// ----------------------------
document.addEventListener("DOMContentLoaded", () => {
    const lastSeenVersion = localStorage.getItem("intelctx_version") || "0";
    const remindUntil = parseInt(localStorage.getItem("intelctx_remind_until") || "0");

    // If user dismissed this version → do not show
    if (lastSeenVersion === INTELCTX_VERSION) return;

    // If "remind me later" period is active
    if (Date.now() < remindUntil) return;

    // Show modal
    document.getElementById("updateModal").classList.remove("hidden");
});
</script>
