<?php
require_once __DIR__ . '/../auth.php';
require_login();
$pdo = get_db();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;

if ($editing) {
    $stmt = $pdo->prepare("SELECT * FROM apt_groups WHERE id = ?");
    $stmt->execute([$id]);
    $apt = $stmt->fetch();
    if (!$apt) {
        die("APT not found");
    }
} else {
    $apt = [
        'name' => '', 'aliases' => '', 'country' => '', 'mitre_group_id' => '', 'sponsor' => '',
        'active_from' => '', 'active_to' => null, 'motivation' => '',
        'targeted_industries' => '', 'targeted_countries' => '', 'ttp_summary' => '',
        'malware_families' => '', 'tools' => '', 'notable_attacks' => '',
        'ioc_domains' => '', 'ioc_ips' => '', 'ioc_hashes' => '', 'ioc_emails' => '',
        'ioc_registry_paths' => '', 'ioc_yara' => '', 'detection_opportunities' => '',
        'references_section' => '', 'risk_score' => 5, 'confidence_level' => 'Medium'
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'name','aliases','country', 'mitre_group_id', 'sponsor','active_from','active_to','motivation',
        'targeted_industries','targeted_countries','ttp_summary','malware_families',
        'tools','notable_attacks','ioc_domains','ioc_ips','ioc_hashes','ioc_emails',
        'ioc_registry_paths','ioc_yara','detection_opportunities','references_section',
        'risk_score','confidence_level'
    ];
    $data = [];
    foreach ($fields as $f) {
        $data[$f] = $_POST[$f] ?? null;
    }
    $data['risk_score'] = (int)$data['risk_score'];

    if ($editing) {
        $sql = "UPDATE apt_groups SET
          name=:name, aliases=:aliases, country=:country, mitre_group_id=:mitre_group_id, sponsor=:sponsor,
          active_from=:active_from, active_to=:active_to, motivation=:motivation,
          targeted_industries=:targeted_industries, targeted_countries=:targeted_countries,
          ttp_summary=:ttp_summary, malware_families=:malware_families, tools=:tools,
          notable_attacks=:notable_attacks, ioc_domains=:ioc_domains, ioc_ips=:ioc_ips,
          ioc_hashes=:ioc_hashes, ioc_emails=:ioc_emails, ioc_registry_paths=:ioc_registry_paths,
          ioc_yara=:ioc_yara, detection_opportunities=:detection_opportunities,
          references_section=:references_section, risk_score=:risk_score,
          confidence_level=:confidence_level
          WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $data['id'] = $id;
        $stmt->execute($data);
        $action = 'update';
    } else {
        $sql = "INSERT INTO apt_groups
        (name, aliases, country, mitre_group_id, sponsor, active_from, active_to, motivation,
        targeted_industries, targeted_countries, ttp_summary, malware_families, tools,
        notable_attacks, ioc_domains, ioc_ips, ioc_hashes, ioc_emails, ioc_registry_paths,
        ioc_yara, detection_opportunities, references_section, risk_score, confidence_level)
        VALUES
        (:name,:aliases,:country, :mitre_group_id, :sponsor,:active_from,:active_to,:motivation,
        :targeted_industries,:targeted_countries,:ttp_summary,:malware_families,:tools,
        :notable_attacks,:ioc_domains,:ioc_ips,:ioc_hashes,:ioc_emails,:ioc_registry_paths,
        :ioc_yara,:detection_opportunities,:references_section,:risk_score,:confidence_level)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        $id = $pdo->lastInsertId();
        $action = 'insert';
    }

    // Audit log
    $log = $pdo->prepare("INSERT INTO audit_logs (action, actor, apt_id, apt_name) VALUES (?,?,?,?)");
    $log->execute([$action, $_SESSION['admin_username'], $id, $data['name']]);

    header("Location: dashboard.php");
    exit;
}

include __DIR__ . '/../partials/header.php';
?>
<section class="space-y-4 text-sm">
    <h1 class="text-lg font-semibold">
        <?php echo $editing ? 'Edit APT Group' : 'New APT Group'; ?>
    </h1>
    <form method="post" class="bg-white border border-slate-200 rounded-lg p-4 grid md:grid-cols-2 gap-4 text-xs">
        <div class="space-y-3">
            <div>
                <label class="block mb-1">Name</label>
                <input name="name" required class="w-full border border-slate-300 rounded px-2 py-1.5"
                       value="<?php echo htmlspecialchars($apt['name']); ?>">
            </div>
            
            <div>
                <label class="block mb-1">Aliases (comma-separated)</label>
                <input name="aliases" class="w-full border border-slate-300 rounded px-2 py-1.5"
                       value="<?php echo htmlspecialchars($apt['aliases']); ?>">
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block mb-1">Country</label>
                    <input name="country" class="w-full border border-slate-300 rounded px-2 py-1.5"
                           value="<?php echo htmlspecialchars($apt['country']); ?>">
                </div>
                    <!-- MITRE Group ID -->
    <div class="space-y-2">
      <label class="block uppercase text-slate-400 text-[11px] font-bold">MITRE ATT&CK Group ID</label>
      <input name="mitre_group_id" placeholder="G0018 admin@338"
        class="w-full border border-border rounded-md px-3 py-2 bg-light font-mono"
        value="<?php echo htmlspecialchars($apt['mitre_group_id']); ?>">
      <p class="text-[10px] text-slate-400">Official Group ID + optional internal tag</p>
    </div>
                <div>
                    <label class="block mb-1">Sponsor</label>
                    <input name="sponsor" class="w-full border border-slate-300 rounded px-2 py-1.5"
                           value="<?php echo htmlspecialchars($apt['sponsor']); ?>">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block mb-1">Active from (year)</label>
                    <input name="active_from" class="w-full border border-slate-300 rounded px-2 py-1.5"
                           value="<?php echo htmlspecialchars($apt['active_from']); ?>">
                </div>
                <div>
                    <label class="block mb-1">Active to (year or empty)</label>
                    <input name="active_to" class="w-full border border-slate-300 rounded px-2 py-1.5"
                           value="<?php echo htmlspecialchars($apt['active_to']); ?>">
                </div>
            </div>
            <div>
                <label class="block mb-1">Motivation</label>
                <input name="motivation" placeholder="Espionage, Sabotage, Cybercrime..."
                       class="w-full border border-slate-300 rounded px-2 py-1.5"
                       value="<?php echo htmlspecialchars($apt['motivation']); ?>">
            </div>
            <div>
                <label class="block mb-1">Targeted industries</label>
                <textarea name="targeted_industries" rows="3"
                          class="w-full border border-slate-300 rounded px-2 py-1.5"><?php
                    echo htmlspecialchars($apt['targeted_industries']); ?></textarea>
            </div>
            <div>
                <label class="block mb-1">Targeted countries</label>
                <textarea name="targeted_countries" rows="3"
                          class="w-full border border-slate-300 rounded px-2 py-1.5"><?php
                    echo htmlspecialchars($apt['targeted_countries']); ?></textarea>
            </div>
            <div>
                <label class="block mb-1">TTP summary (MITRE ATT&CK, phases)</label>
                <textarea name="ttp_summary" rows="5"
                          class="w-full border border-slate-300 rounded px-2 py-1.5"><?php
                    echo htmlspecialchars($apt['ttp_summary']); ?></textarea>
            </div>
        </div>

        <div class="space-y-3">
            <div>
                <label class="block mb-1">Malware families</label>
                <textarea name="malware_families" rows="3"
                          class="w-full border border-slate-300 rounded px-2 py-1.5"><?php
                    echo htmlspecialchars($apt['malware_families']); ?></textarea>
            </div>
            <div>
                <label class="block mb-1">Tools</label>
                <textarea name="tools" rows="3"
                          class="w-full border border-slate-300 rounded px-2 py-1.5"><?php
                    echo htmlspecialchars($apt['tools']); ?></textarea>
            </div>
            <div>
                <label class="block mb-1">Notable attacks (short case notes)</label>
                <textarea name="notable_attacks" rows="4"
                          class="w-full border border-slate-300 rounded px-2 py-1.5"><?php
                    echo htmlspecialchars($apt['notable_attacks']); ?></textarea>
            </div>
            <div>
                <label class="block mb-1">Detection opportunities (log-based)</label>
                <textarea name="detection_opportunities" rows="4"
                          class="w-full border border-slate-300 rounded px-2 py-1.5"><?php
                    echo htmlspecialchars($apt['detection_opportunities']); ?></textarea>
            </div>
            <div>
                <label class="block mb-1">References (links, CVEs, reports)</label>
                <textarea name="references_section" rows="4"
                          class="w-full border border-slate-300 rounded px-2 py-1.5"><?php
                    echo htmlspecialchars($apt['references_section']); ?></textarea>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block mb-1">Risk score (1–10)</label>
                    <input type="number" min="1" max="10" name="risk_score"
                           class="w-full border border-slate-300 rounded px-2 py-1.5"
                           value="<?php echo (int)$apt['risk_score']; ?>">
                </div>
                <div>
                    <label class="block mb-1">Confidence</label>
                    <select name="confidence_level"
                            class="w-full border border-slate-300 rounded px-2 py-1.5">
                        <?php foreach (['Low','Medium','High'] as $c): ?>
                            <option value="<?php echo $c; ?>"
                                <?php if ($apt['confidence_level']===$c) echo 'selected'; ?>>
                                <?php echo $c; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="border-t border-slate-200 pt-2">
                <h2 class="text-xs font-semibold mb-1">IOCs</h2>
                <label class="block mb-1">Domains</label>
                <textarea name="ioc_domains" rows="2"
                          class="w-full border border-slate-300 rounded px-2 py-1.5"><?php
                    echo htmlspecialchars($apt['ioc_domains']); ?></textarea>

                <label class="block mb-1 mt-2">IPs</label>
                <textarea name="ioc_ips" rows="2"
                          class="w-full border border-slate-300 rounded px-2 py-1.5"><?php
                    echo htmlspecialchars($apt['ioc_ips']); ?></textarea>

                <label class="block mb-1 mt-2">File hashes</label>
                <textarea name="ioc_hashes" rows="2"
                          class="w-full border border-slate-300 rounded px-2 py-1.5"><?php
                    echo htmlspecialchars($apt['ioc_hashes']); ?></textarea>

                <label class="block mb-1 mt-2">Emails / patterns</label>
                <textarea name="ioc_emails" rows="2"
                          class="w-full border border-slate-300 rounded px-2 py-1.5"><?php
                    echo htmlspecialchars($apt['ioc_emails']); ?></textarea>

                <label class="block mb-1 mt-2">Registry paths</label>
                <textarea name="ioc_registry_paths" rows="2"
                          class="w-full border border-slate-300 rounded px-2 py-1.5"><?php
                    echo htmlspecialchars($apt['ioc_registry_paths']); ?></textarea>

                <label class="block mb-1 mt-2">YARA / YARA-like</label>
                <textarea name="ioc_yara" rows="4"
                          class="w-full border border-slate-300 rounded px-2 py-1.5 font-mono"><?php
                    echo htmlspecialchars($apt['ioc_yara']); ?></textarea>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <a href="dashboard.php" class="border border-slate-300 rounded px-3 py-1.5 text-xs">Cancel</a>
                <button type="submit" class="border border-slate-500 rounded px-3 py-1.5 text-xs">
                    Save
                </button>
            </div>
        </div>
    </form>
</section>
<?php include __DIR__ . '/../partials/footer.php'; ?>
