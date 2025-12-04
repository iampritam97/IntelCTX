<?php

function calculate_knowledge_score($apt) {
    $score = 0;

    if (!empty($apt['ttp_summary'])) $score += 20;
    if (!empty($apt['ioc_domains']) || !empty($apt['ioc_ips']) || !empty($apt['ioc_hashes'])) $score += 20;

    if (!empty($apt['malware_families'])) {
        $count = count(explode(",", $apt['malware_families']));
        $score += min($count * 5, 15);
    }

    if (!empty($apt['tools'])) {
        $count = count(explode(",", $apt['tools']));
        $score += min($count * 5, 15);
    }

    if (!empty($apt['references_section'])) $score += 10;
    if (!empty($apt['notable_attacks'])) $score += 10;
    if (!empty($apt['detection_opportunities'])) $score += 10;

    return min(100, $score);
}
