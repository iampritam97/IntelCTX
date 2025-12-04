<?php include 'partials/header.php'; ?>

<section class="max-w-6xl mx-auto px-6 py-12 space-y-10 text-primary dark:text-gray-100">

    <!-- Page Header -->
    <div class="space-y-1">
        <h1 class="text-3xl font-extrabold tracking-tight text-ht_blue">Detection Playbook Templates</h1>
        <p class="text-xs text-slate-500 dark:text-gray-400 font-mono">
            Defender-ready detections mapped to adversary tradecraft & MITRE ATT&CK.
        </p>
    </div>

    <?php 
    $templates = [
        [
            "Credential Access (T1003)",
            "Monitor unauthorized LSASS memory dumping activity via Sysmon Event ID 10 or EDR behavior telemetry.",
            "event_id=10 AND target_process=lsass.exe"
        ],
        [
            "Phishing (T1566)",
            "Detect suspicious command execution spawned by Outlook or Thunderbird processes.",
            "parent_process=outlook.exe AND child_proc IN [powershell.exe,cmd.exe]"
        ],
        [
            "Persistence (T1547)",
            "Look for suspicious autorun registry key modifications used for persistence.",
            "reg_path=*Run* OR reg_path=*WinLogon*"
        ],
        [
            "Command & Control (T1071)",
            "Monitor C2 communication patterns via protocol misuse and untrusted domains.",
            "dest_port IN [80,443] AND domain=*"
        ],
        [
            "Lateral Movement (T1021)",
            "Detect abnormal RDP/SSH authentication spikes and lateral movement patterns.",
            "event_id=4625 OR ssh_fail > 5"
        ],
        [
            "Data Exfiltration (T1041)",
            "Identify unauthorized data exfiltration to cloud services or unknown endpoints.",
            "bytes_out > 5000000 AND process_name!=expected_agent"
        ]
    ];
    ?>

    <!-- Grid -->
    <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <?php foreach($templates as $t): ?>
        <?php 
            $id = md5($t[0]); 
            preg_match('/\(T\d+\)/', $t[0], $tech);
        ?>

            <div class="template-card backdrop-blur-lg bg-white/5 dark:bg-white/5 
                        border border-white/10 dark:border-white/10 
                        rounded-xl p-5 shadow-lg hover:shadow-2xl transition transform 
                        hover:-translate-y-1 hover:border-ht_blue/50 group">

                <!-- Header -->
                <div class="flex justify-between items-start">
                    <h2 class="text-sm font-bold uppercase text-ht_blue tracking-wide leading-snug">
                        <?= htmlspecialchars($t[0]) ?>
                    </h2>

                    <!-- MITRE Badge -->
                    <span class="text-[10px] px-2 py-1 rounded-md bg-ht_blue/20 text-ht_blue font-mono">
                        <?= $tech[0] ?? '' ?>
                    </span>
                </div>

                <!-- Short Description -->
                <p class="text-xs text-slate-400 dark:text-gray-400 mt-3 leading-relaxed">
                    <?= htmlspecialchars($t[1]) ?>
                </p>

                <!-- Query Box -->
                <div class="relative mt-4">
                    <textarea id="query_<?= $id ?>" rows="3" readonly
                        class="w-full font-mono text-[11px] rounded-md bg-white/10 dark:bg-black/20 
                               border border-white/10 dark:border-white/10 p-3 shadow-inner
                               focus:outline-none focus:ring-1 focus:ring-ht_blue/40">
<?= htmlspecialchars($t[2]) ?></textarea>

                    <!-- Copy Icon -->
                    <button onclick="copyQuery('query_<?= $id ?>')"
                        class="absolute top-2 right-2 text-xs bg-black/30 backdrop-blur-sm 
                               px-2 py-1 rounded hover:bg-black/50 transition text-ht_blue">
                        Copy
                    </button>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center mt-3">
                    <button onclick="toggleDetails('<?= $id ?>')"
                        class="text-xs text-slate-400 hover:text-gray-200 transition font-mono">
                        More Details ▾
                    </button>
                </div>

                <!-- Slide-down Details -->
                <div id="details_<?= $id ?>" 
                     class="details-panel hidden mt-3 text-[11px] text-slate-400 dark:text-gray-300 leading-relaxed space-y-2">
                    <p><strong class="text-gray-300">Detection Goal:</strong> Identify behavior aligned with known adversary TTPs.</p>
                    <p><strong class="text-gray-300">Recommended Sources:</strong> Sysmon, EDR Telemetry, Firewall Logs, DNS Logs.</p>
                    <p><strong class="text-gray-300">Analyst Tip:</strong> Pivot using correlated fields (parent/child process, user, host, timeline).</p>
                </div>

            </div>

        <?php endforeach; ?>

    </div>

</section>

<script>
function toggleDetails(id) {
    const panel = document.getElementById("details_" + id);
    panel.classList.toggle("hidden");
    panel.classList.toggle("animate-fadeIn");
}

function copyQuery(id) {
    navigator.clipboard.writeText(document.getElementById(id).value);
}
</script>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
    animation: fadeIn 0.25s ease-out;
}
</style>

<?php include 'partials/footer.php'; ?>
