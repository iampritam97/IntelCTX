<?php
require_once __DIR__ . '/../../auth.php';
require_login();

$pdo = get_db();

// Generate token
$token = bin2hex(random_bytes(32));

$stmt = $pdo->prepare("INSERT INTO api_tokens (token, owner) VALUES (?, ?)");
$stmt->execute([$token, $_SESSION['admin_username']]);

?>
<?php include __DIR__ . '/../../partials/header.php'; ?>

<section class="max-w-xl mx-auto px-6 py-16 text-sm">

    <!-- Success Card -->
    <div class="backdrop-blur-xl bg-white/5 border border-white/10 shadow-xl rounded-2xl p-8 text-center animate-fadeIn">

        <h1 class="text-2xl font-bold text-ht_blue mb-2">API Token Generated</h1>
        <p class="text-ht_muted mb-6">This token is shown <strong>only once</strong>. Store it securely.</p>

        <!-- Token Box -->
        <div class="relative bg-ht_bg border border-ht_border rounded-xl p-4 mb-6 shadow-inner">

            <code id="apiToken" class="block text-center text-[13px] font-mono tracking-wider text-white break-words">
                <?= htmlspecialchars($token); ?>
            </code>

            <!-- Copy Button -->
            <button onclick="copyToken()"
                class="absolute right-3 top-3 text-xs bg-ht_blue text-white px-3 py-1 rounded-lg hover:bg-ht_blue2 transition">
                Copy
            </button>
        </div>

        <a href="/admin/api_tokens.php"
           class="inline-block mt-3 px-4 py-2 bg-ht_bg border border-ht_border rounded-lg text-ht_muted hover:bg-white/10 transition text-xs">
            ← Back to Token Manager
        </a>
    </div>

</section>

<!-- Toast -->
<div id="toast"
     class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 bg-white/10 backdrop-blur-xl text-white px-4 py-2 rounded-md border border-white/20 text-xs shadow-md">
    Copied!
</div>

<style>
@keyframes fadeIn { from {opacity: 0;} to {opacity: 1;} }
.animate-fadeIn { animation: fadeIn .25s ease-out; }
</style>

<script>
function copyToken() {
    const token = document.getElementById("apiToken").innerText;
    navigator.clipboard.writeText(token).then(() => showToast());
}

function showToast() {
    const t = document.getElementById("toast");
    t.classList.remove("hidden");
    t.style.opacity = "1";

    setTimeout(() => { t.style.opacity = "0"; }, 1800);
    setTimeout(() => { t.classList.add("hidden"); }, 2300);
}
</script>

<?php include __DIR__ . '/../../partials/footer.php'; ?>
