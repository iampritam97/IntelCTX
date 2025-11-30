<?php
require_once __DIR__ . '/db.php';
$pdo = get_db();

$count = $pdo->query("SELECT COUNT(*) FROM apt_groups")->fetchColumn();
if ($count > 0) {
    echo "APT groups already present. Seed skipped.";
    exit;
}

$aptSamples = [
    [
        'name' => 'APT28',
        'aliases' => 'Fancy Bear, Sofacy',
        'country' => 'Russia',
        'sponsor' => 'Suspected state-sponsored',
        'active_from' => 2007,
        'active_to' => null,
        'motivation' => 'Espionage',
        'targeted_industries' => "Government\nDefense\nMedia",
        'targeted_countries' => "US\nEurope\nNATO members",
        'ttp_summary' => "Spearphishing with malicious documents; credential harvesting; living-off-the-land with native Windows tools; extensive use of custom malware families mapped to MITRE ATT&CK tactics (TA0001–TA0005, TA0007–TA0011).",
        'malware_families' => "X-Agent\nSednit\nSourface",
        'tools' => "Mimikatz\nPowerShell\nCustom loaders",
        'notable_attacks' => "- 2016 political election targeting\n- Multiple intrusions against NATO-related entities",
        'ioc_domains' => "example-update[.]com\ncdn-sync[.]net",
        'ioc_ips' => "192.0.2.10\n198.51.100.23",
        'ioc_hashes' => "sha256:111111...\nsha256:222222...",
        'ioc_emails' => "From: noreply-security@example[.]com\nSubject pattern: Security Alert *",
        'ioc_registry_paths' => "HKCU\\Software\\Example\nHKLM\\System\\Example",
        'ioc_yara' => "rule APT28_Sample {\n  strings:\n    $a = \"APT28\"\n  condition:\n    $a\n}",
        'detection_opportunities' => "Look for anomalous PowerShell executions from Office processes; unusual authentication attempts against OWA/VPN portals; rare destinations over HTTPS from critical systems.",
        'references_section' => "- Public threat reports about APT28\n- MITRE ATT&CK group page",
        'risk_score' => 9,
        'confidence_level' => 'High'
    ],
    [
        'name' => 'APT29',
        'aliases' => 'Cozy Bear',
        'country' => 'Russia',
        'sponsor' => 'Suspected state-sponsored',
        'active_from' => 2008,
        'active_to' => null,
        'motivation' => 'Espionage',
        'targeted_industries' => "Government\nThink tanks\nNGOs",
        'targeted_countries' => "US\nEurope",
        'ttp_summary' => "Stealthy, long-term intrusions; cloud-focused tradecraft; abuse of legitimate services and OAuth tokens.",
        'malware_families' => "Custom backdoors\nSecond-stage loaders",
        'tools' => "Cobalt Strike\nPowerShell\nCustom implants",
        'notable_attacks' => "- Compromises of multiple government entities (public reporting)",
        'ioc_domains' => "login-update[.]net",
        'ioc_ips' => "203.0.113.10",
        'ioc_hashes' => "sha256:333333...",
        'ioc_emails' => "",
        'ioc_registry_paths' => "",
        'ioc_yara' => "rule APT29_Sample { condition: false }",
        'detection_opportunities' => "Monitor unusual cloud admin activity; anomalous OAuth consent grants; sign-ins from rare IP ranges.",
        'references_section' => "- Public reports on APT29",
        'risk_score' => 9,
        'confidence_level' => 'High'
    ],
    // Add up to 10 entries as needed…
];

$insert = $pdo->prepare(
"INSERT INTO apt_groups
(name, aliases, country, sponsor, active_from, active_to, motivation,
targeted_industries, targeted_countries, ttp_summary, malware_families, tools,
notable_attacks, ioc_domains, ioc_ips, ioc_hashes, ioc_emails, ioc_registry_paths,
ioc_yara, detection_opportunities, references_section, risk_score, confidence_level)
VALUES
(:name,:aliases,:country,:sponsor,:active_from,:active_to,:motivation,
:targeted_industries,:targeted_countries,:ttp_summary,:malware_families,:tools,
:notable_attacks,:ioc_domains,:ioc_ips,:ioc_hashes,:ioc_emails,:ioc_registry_paths,
:ioc_yara,:detection_opportunities,:references_section,:risk_score,:confidence_level)"
);

foreach ($aptSamples as $a) {
    $insert->execute($a);
}

$malwares = [
    ['Emotet','Modular banking Trojan used in many operations'],
    ['TrickBot','Modular info-stealer and loader'],
    ['Cobalt Strike Beacon','Commercial red-team tool abused by multiple APTs'],
    ['PlugX','Remote access tool'],
    ['QuasarRAT','Open-source RAT'],
    ['ShadowPad','Modular backdoor'],
    ['PlugX Variant','Variant family'],
    ['NetWire','RAT'],
    ['AgentTesla','Keylogger/stealer'],
    ['Dridex','Banking Trojan']
];

$insM = $pdo->prepare("INSERT INTO malware_families (name, description) VALUES (?, ?)
    ON DUPLICATE KEY UPDATE description=VALUES(description)");
foreach ($malwares as $m) {
    $insM->execute($m);
}

$tools = [
    ['Mimikatz','Credential dumping tool'],
    ['Cobalt Strike','Red-team framework'],
    ['PowerShell Empire','Post-exploitation framework'],
    ['PsExec','Windows remote execution'],
    ['BloodHound','AD graph mapper'],
    ['Responder','LLMNR/NBT-NS poisoning tool'],
    ['Impacket','Python collection for networking/AD abuse'],
    ['Rubeus','Kerberos abuse tool'],
    ['SharpHound','BloodHound data collector'],
    ['Custom Loader','Operator-specific loader family']
];

$insT = $pdo->prepare("INSERT INTO threat_tools (name, description) VALUES (?, ?)
    ON DUPLICATE KEY UPDATE description=VALUES(description)");
foreach ($tools as $t) {
    $insT->execute($t);
}

echo "Seed complete.";
