<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../functions/knowledge_score.php';

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
        'references_section' => '', 'risk_score' => 5, 'confidence_level' => 'Medium', 'knowledge_score' => 0
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'name','aliases','country', 'mitre_group_id', 'sponsor','active_from','active_to','motivation',
        'targeted_industries','targeted_countries','ttp_summary','malware_families',
        'tools','notable_attacks','ioc_domains','ioc_ips','ioc_hashes','ioc_emails',
        'ioc_registry_paths','ioc_yara','detection_opportunities','references_section',
        'risk_score','confidence_level', 'knowledge_score'
    ];
    $data['risk_score'] = (int)$data['risk_score'];
    // Compute IntelCTX Knowledge Score
    $data['knowledge_score'] = calculate_knowledge_score($data);

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
          confidence_level=:confidence_level, knowledge_score=:knowledge_score
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
        ioc_yara, detection_opportunities, references_section, risk_score, confidence_level, knowledge_score)
        VALUES
        (:name,:aliases,:country, :mitre_group_id, :sponsor,:active_from,:active_to,:motivation,
        :targeted_industries,:targeted_countries,:ttp_summary,:malware_families,:tools,
        :notable_attacks,:ioc_domains,:ioc_ips,:ioc_hashes,:ioc_emails,:ioc_registry_paths,
        :ioc_yara,:detection_opportunities,:references_section,:risk_score,:confidence_level,:knowledge_score)";
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
<section class="space-y-6 text-sm">

  <h1 class="text-xl font-bold text-ht_blue">
    <?= $editing ? 'Edit APT Group' : 'New APT Group'; ?>
  </h1>

  <form method="post" class="grid gap-6">

    <!-- =======================
         SECTION: IDENTITY
    ======================== -->
    <div class="bg-ht_bg2 border border-ht_border rounded-xl p-6 space-y-5">
      <h2 class="text-xs font-bold uppercase tracking-wider text-ht_muted">Identity</h2>

      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="block text-ht_muted mb-1 text-xs font-semibold">Name</label>
          <input name="name" required
            class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-ht_text"
            value="<?= htmlspecialchars($apt['name']); ?>">
        </div>

        <div>
          <label class="block text-ht_muted mb-1 text-xs font-semibold">Aliases</label>
          <input name="aliases"
            class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-ht_text"
            value="<?= htmlspecialchars($apt['aliases']); ?>">
        </div>
      </div>

      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="block text-ht_muted mb-1 text-xs font-semibold">Country</label>
          <input name="country"
            class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-ht_text"
            value="<?= htmlspecialchars($apt['country']); ?>">
        </div>

        <div>
          <label class="block text-ht_muted mb-1 text-xs font-semibold">MITRE ATT&CK Group ID</label>
          <input name="mitre_group_id"
            class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 font-mono text-ht_text"
            placeholder="G0018"
            value="<?= htmlspecialchars($apt['mitre_group_id']); ?>">
        </div>
      </div>

      <div>
        <label class="block text-ht_muted mb-1 text-xs font-semibold">Sponsor</label>
        <input name="sponsor"
          class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-ht_text"
          value="<?= htmlspecialchars($apt['sponsor']); ?>">
      </div>
    </div>

    <!-- =======================
         SECTION: TIMELINE
    ======================== -->
    <div class="bg-ht_bg2 border border-ht_border rounded-xl p-6 space-y-4">
      <h2 class="text-xs font-bold uppercase tracking-wider text-ht_muted">Activity Timeline</h2>

      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="block text-ht_muted mb-1 text-xs font-semibold">Active From</label>
          <input name="active_from"
            class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-ht_text"
            value="<?= htmlspecialchars($apt['active_from']); ?>">
        </div>

        <div>
          <label class="block text-ht_muted mb-1 text-xs font-semibold">Active To</label>
          <input name="active_to"
            class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-ht_text"
            value="<?= htmlspecialchars($apt['active_to']); ?>">
        </div>
      </div>

      <div>
        <label class="block text-ht_muted mb-1 text-xs font-semibold">Motivation</label>
        <input name="motivation"
          class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-ht_text"
          value="<?= htmlspecialchars($apt['motivation']); ?>">
      </div>

      <div>
        <label class="block text-ht_muted mb-1 text-xs font-semibold">Targeted Industries</label>
        <textarea name="targeted_industries" rows="3"
          class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-ht_text"><?= htmlspecialchars($apt['targeted_industries']); ?></textarea>
      </div>

      <div>
        <label class="block text-ht_muted mb-1 text-xs font-semibold">Targeted Countries</label>
        <textarea name="targeted_countries" rows="3"
          class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-ht_text"><?= htmlspecialchars($apt['targeted_countries']); ?></textarea>
      </div>
    </div>

    <!-- =======================
         SECTION: CAPABILITIES
    ======================== -->
    <div class="bg-ht_bg2 border border-ht_border rounded-xl p-6 space-y-4">
      <h2 class="text-xs font-bold uppercase tracking-wider text-ht_muted">Capabilities</h2>

      <label class="block text-ht_muted mb-1 text-xs font-semibold">Malware Families</label>
      <textarea name="malware_families" rows="3"
        class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-ht_text"><?= htmlspecialchars($apt['malware_families']); ?></textarea>

      <label class="block text-ht_muted mt-3 mb-1 text-xs font-semibold">Tools</label>
      <textarea name="tools" rows="3"
        class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-ht_text"><?= htmlspecialchars($apt['tools']); ?></textarea>

      <label class="block text-ht_muted mt-3 mb-1 text-xs font-semibold">Notable Attacks</label>
      <textarea name="notable_attacks" rows="4"
        class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-ht_text"><?= htmlspecialchars($apt['notable_attacks']); ?></textarea>

      <label class="block text-ht_muted mt-3 mb-1 text-xs font-semibold">Detection Opportunities</label>
      <textarea name="detection_opportunities" rows="4"
        class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-ht_text"><?= htmlspecialchars($apt['detection_opportunities']); ?></textarea>

  <label class="block text-ht_muted mt-3 mb-1 text-xs font-semibold">TTP summary (MITRE ATT&CK, phases)</label>
                <textarea name="ttp_summary" rows="4"
                          class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-ht_text"><?php
                    echo htmlspecialchars($apt['ttp_summary']); ?></textarea>
            </div>
    </div>

    <!-- =======================
         SECTION: ANALYST INPUTS
    ======================== -->
    <div class="bg-ht_bg2 border border-ht_border rounded-xl p-6 space-y-4">
      <h2 class="text-xs font-bold uppercase tracking-wider text-ht_muted">Analyst Metadata</h2>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-ht_muted mb-1 text-xs font-semibold">Risk Score</label>
          <input name="risk_score" type="number" min="1" max="10"
            class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-ht_text"
            value="<?= (int)$apt['risk_score']; ?>">
        </div>

        <div>
          <label class="block text-ht_muted mb-1 text-xs font-semibold">Confidence Level</label>
          <select name="confidence_level"
            class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-ht_text">
            <?php foreach (['Low','Medium','High'] as $c): ?>
              <option value="<?= $c ?>" <?= $apt['confidence_level']===$c ? 'selected':'' ?>>
                <?= $c ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <label class="block text-ht_muted mt-3 mb-1 text-xs font-semibold">References</label>
      <textarea name="references_section" rows="4"
        class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-ht_text"><?= htmlspecialchars($apt['references_section']); ?></textarea>
    </div>

    <!-- =======================
         SECTION: IOCS
    ======================== -->
    <div class="bg-ht_bg2 border border-ht_border rounded-xl p-6 space-y-4">
      <h2 class="text-xs font-bold uppercase tracking-wider text-ht_blue">Indicators of Compromise</h2>

      <label class="block mb-1 text-ht_muted text-xs">Domains</label>
      <textarea name="ioc_domains" rows="2"
        class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-ht_text"><?= htmlspecialchars($apt['ioc_domains']); ?></textarea>

      <label class="block mb-1 mt-3 text-ht_muted text-xs">IPs</label>
      <textarea name="ioc_ips" rows="2"
        class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-ht_text"><?= htmlspecialchars($apt['ioc_ips']); ?></textarea>

      <label class="block mb-1 mt-3 text-ht_muted text-xs">File Hashes</label>
      <textarea name="ioc_hashes" rows="2"
        class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-ht_text"><?= htmlspecialchars($apt['ioc_hashes']); ?></textarea>

      <label class="block mb-1 mt-3 text-ht_muted text-xs">Emails</label>
      <textarea name="ioc_emails" rows="2"
        class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-ht_text"><?= htmlspecialchars($apt['ioc_emails']); ?></textarea>

      <label class="block mb-1 mt-3 text-ht_muted text-xs">Registry Paths</label>
      <textarea name="ioc_registry_paths" rows="2"
        class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 text-ht_text"><?= htmlspecialchars($apt['ioc_registry_paths']); ?></textarea>

      <label class="block mb-1 mt-3 text-ht_muted text-xs">YARA</label>
      <textarea name="ioc_yara" rows="4"
        class="w-full bg-ht_bg border border-ht_border rounded-lg px-3 py-2 font-mono text-ht_text"><?= htmlspecialchars($apt['ioc_yara']); ?></textarea>
    </div>

    <div class="flex justify-end gap-2">
      <a href="dashboard.php"
        class="px-4 py-2 text-xs bg-ht_bg border border-ht_border rounded-lg text-ht_muted hover:bg-ht_border">Cancel</a>
      <button type="submit"
        class="px-4 py-2 text-xs bg-ht_blue text-white rounded-lg hover:bg-blue-700">Save</button>
    </div>

  </form>

</section>

<?php include __DIR__ . '/../partials/footer.php'; ?>
