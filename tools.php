<?php
require_once 'db.php';
include 'partials/header.php';
$pdo = get_db();

$q = $_GET['q'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM threat_tools WHERE name LIKE :q OR description LIKE :q ORDER BY name ASC");
$stmt->execute([':q' => "%$q%"]);
$tools = $stmt->fetchAll();
?>

<section class="max-w-6xl mx-auto px-6 py-12 space-y-10 text-sm">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-ht_blue">Threat Tools & Frameworks</h1>
            <p class="text-xs text-slate-500 dark:text-gray-400 font-mono">
               Catalog of offensive & defensive tooling across APT ecosystems.
            </p>
        </div>
        <span class="text-xs bg-ht_blue/20 text-ht_blue px-3 py-1 rounded-md font-semibold">MVP</span>
    </div>

    <!-- Floating Glass Search Bar -->
    <!-- <form method="GET"
        class="backdrop-blur-xl bg-white/5 dark:bg-white/5 border border-white/10 
               rounded-xl shadow-lg px-4 py-3 flex gap-3 sticky top-4 z-20">
        
        <input type="text" name="q" value="<?= htmlspecialchars($q) ?>"
            placeholder="Search threat tools…"
            class="flex-1 bg-transparent text-sm text-slate-700 dark:text-gray-200 
                   placeholder-gray-400 dark:placeholder-gray-500 font-mono focus:outline-none px-2">

        <button type="submit"
            class="px-4 py-2 rounded-lg text-xs font-bold bg-ht_blue text-white hover:bg-ht_blue/80 transition">
            Search
        </button>
    </form> -->

    <!-- Tools Grid -->
    <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <?php foreach($tools as $t): ?>
        <div class="backdrop-blur-lg bg-white/5 dark:bg-white/5 border border-white/10 
                    rounded-xl p-5 shadow-lg hover:shadow-2xl transition transform hover:-translate-y-1 
                    hover:border-ht_blue/40 group">

            <!-- Tool Name -->
            <h2 class="text-lg font-bold text-ht_blue tracking-tight">
                <?= htmlspecialchars($t['name']) ?>
            </h2>

            <!-- Description -->
            <p class="text-xs text-slate-400 dark:text-gray-400 mt-3 line-clamp-4 leading-relaxed">
                <?= htmlspecialchars($t['description']) ?>
            </p>

            <!-- Tags -->
            <div class="mt-4 flex flex-wrap gap-2">
                <?php 
                    // Static sample tags for now
                    $tags = ["Execution", "Credential Access", "Defense Evasion"];
                    foreach($tags as $tag): ?>
                    
                    <span class="text-[10px] px-2 py-1 rounded-full bg-white/10 
                                border border-white/10 text-gray-300 font-mono 
                                group-hover:bg-ht_blue/20 group-hover:border-ht_blue/40 transition">
                        <?= strtoupper($tag) ?>
                    </span>

                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

    </div>

    <!-- Empty State -->
    <?php if(!$tools): ?>
        <div class="text-center py-10">
            <p class="text-sm text-slate-500 dark:text-gray-500 font-mono">
                No tools found. Try a different keyword.
            </p>
        </div>
    <?php endif; ?>

</section>

<?php include 'partials/footer.php'; ?>
