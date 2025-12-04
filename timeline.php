<?php
require_once 'db.php';
include 'partials/header.php';
$pdo = get_db();
$rows = $pdo->query("
  SELECT id,name,aliases,country,motivation,active_from,active_to,notable_attacks,updated_at 
  FROM apt_groups 
  ORDER BY active_from ASC
")->fetchAll();
?>

<section class="max-w-6xl mx-auto px-6 py-12 space-y-10">

  <!-- Header -->
  <div class="flex items-center gap-3">
    <h1 class="text-3xl font-extrabold tracking-tight text-ht_blue">
      Global APT Timeline
    </h1>

    <span class="px-3 py-1 text-[10px] bg-white/5 border border-white/10 rounded-full 
                 text-ht_muted backdrop-blur-md">
      Activity Overview
    </span>
  </div>

  <?php if(!$rows): ?>
    <p class="text-sm text-ht_muted">No APT entries available in timeline.</p>
  <?php endif; ?>

  <!-- Timeline -->
  <div class="relative pl-12">

    <!-- Vertical Glowing Line -->
    <div class="absolute left-5 top-0 h-full w-[3px] 
                bg-gradient-to-b from-ht_blue/60 via-ht_blue/20 to-transparent 
                rounded-full"></div>

    <?php 
      $currentYear = null; 
      foreach($rows as $r): 
        $year = date('Y', strtotime($r['active_from']));
    ?>

      <!-- Year Divider -->
      <?php if ($year !== $currentYear): ?>
        <div class="flex items-center gap-4 my-12">
          <div class="text-2xl font-bold text-ht_blue"><?= $year ?></div>
          <div class="flex-1 h-[1px] bg-gradient-to-r from-ht_blue/40 to-transparent"></div>
        </div>
        <?php $currentYear = $year; ?>
      <?php endif; ?>

      <!-- Timeline Item -->
      <div class="relative mb-12">

        <!-- Pulsing Dot -->
        <div class="absolute -left-[25px] top-4">
          <div class="w-4 h-4 rounded-full bg-ht_blue shadow-[0_0_12px] shadow-ht_blue/70 
                      border-2 border-white/30 animate-pulse"></div>
        </div>

        <!-- APT Card -->
        <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-xl p-5 
                    shadow-lg hover:shadow-2xl hover:border-ht_blue/40 transition 
                    transform hover:-translate-y-1">

          <!-- Top Row -->
          <div class="flex justify-between items-start">
            <div>
              <a href="apt.php?id=<?= $r['id']; ?>"
                class="text-lg font-bold text-white hover:text-ht_blue transition">
                <?= htmlspecialchars($r['name']); ?>
              </a>

              <?php if($r['aliases']): ?>
                <p class="text-[11px] text-ht_muted mt-1">
                  Aliases: <?= htmlspecialchars($r['aliases']); ?>
                </p>
              <?php endif; ?>
            </div>

            <div class="text-right">
              <p class="text-[10px] uppercase text-ht_muted">Updated</p>
              <p class="text-xs font-mono text-white">
                <?= date('d M Y', strtotime($r['updated_at'])); ?>
              </p>
            </div>
          </div>

          <!-- Metadata Grid -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-5 text-xs">

            <div>
              <span class="block text-[10px] uppercase text-ht_muted">Origin</span>
              <span class="font-medium text-white">
                <?= htmlspecialchars($r['country']); ?>
              </span>
            </div>

            <div>
              <span class="block text-[10px] uppercase text-ht_muted">Motivation</span>
              <span class="font-medium text-white">
                <?= htmlspecialchars($r['motivation']); ?>
              </span>
            </div>

            <div>
              <span class="block text-[10px] uppercase text-ht_muted">Active</span>
              <span class="font-medium text-white">
                <?= $r['active_from']; ?> – <?= $r['active_to'] ?: 'Present'; ?>
              </span>
            </div>

            <div>
              <span class="block text-[10px] uppercase text-ht_muted">Last Updated</span>
              <span class="font-medium text-white">
                <?= date('Y-m-d', strtotime($r['updated_at'])); ?>
              </span>
            </div>

          </div>

          <!-- Notable Attacks -->
          <?php if($r['notable_attacks']): ?>
            <div class="mt-5">
              <span class="text-[10px] uppercase font-bold text-ht_blue tracking-wider">
                Notable Attacks
              </span>
              <p class="text-xs text-ht_muted mt-1 whitespace-pre-line leading-relaxed">
                <?= htmlspecialchars($r['notable_attacks']); ?>
              </p>
            </div>
          <?php endif; ?>

        </div>
      </div>

    <?php endforeach; ?>

  </div>

</section>
<main>
<?php include 'partials/footer.php'; ?>
