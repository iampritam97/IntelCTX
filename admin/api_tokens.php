<?php 
require_once __DIR__ . '/../auth.php';
require_login();
$pdo = get_db();

/* -----------------------------------------
   ENABLE / DISABLE TOKEN
------------------------------------------ */
if (isset($_GET['toggle'])) {
    $pdo->prepare("UPDATE api_tokens SET active = 1 - active WHERE id = ?")
        ->execute([(int)$_GET['toggle']]);
    header("Location: api_tokens.php?toggled=1");
    exit;
}

/* -----------------------------------------
   DELETE TOKEN
------------------------------------------ */
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM api_tokens WHERE id = ?")
        ->execute([(int)$_GET['delete']]);
    header("Location: api_tokens.php?deleted=1");
    exit;
}

/* -----------------------------------------
   UPDATE TOKEN SCOPES / EXPIRY
------------------------------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_token'])) {
    $stmt = $pdo->prepare("
        UPDATE api_tokens
        SET scopes = ?, expires_at = ?
        WHERE id = ?
    ");
    $stmt->execute([
        $_POST['scopes'],
        $_POST['expires_at'] ?: null,
        $_POST['id']
    ]);

    header("Location: api_tokens.php?updated=1");
    exit;
}

/* -----------------------------------------
   FETCH TOKENS
------------------------------------------ */
$tokens = $pdo->query("SELECT * FROM api_tokens ORDER BY created_at DESC")->fetchAll();

include __DIR__ . '/../partials/header.php';
?>

<section class="max-w-6xl mx-auto px-6 py-10 text-sm">

    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-ht_blue tracking-tight">API Token Management</h1>
            <p class="text-xs text-ht_muted mt-1">Manage access credentials, scopes, expiry, and logs.</p>
        </div>

        <a href="/api/token/create.php"
           class="px-4 py-2 bg-ht_blue text-white rounded-lg text-xs shadow hover:bg-ht_blue2 transition flex items-center gap-2">
            ➕ <span>Generate Token</span>
        </a>
    </div>

    <!-- Token Table -->
    <div class="backdrop-blur-xl bg-white/5 border border-ht_border rounded-xl p-6 shadow-xl">

        <table class="w-full text-xs">
            <thead>
                <tr class="text-ht_muted border-b border-ht_border uppercase tracking-wide text-[10px]">
                    <th class="pb-2 text-left">Token</th>
                    <th class="pb-2 text-left">Owner</th>
                    <th class="pb-2 text-left">Scopes</th>
                    <th class="pb-2 text-left">Expiry</th>
                    <th class="pb-2 text-left">Status</th>
                    <th class="pb-2 text-left">Last Used</th>
                    <th class="pb-2 text-right">Actions</th>
                </tr>
            </thead>

            <tbody>

            <?php foreach ($tokens as $t): ?>
                <tr class="border-b border-white/10 hover:bg-white/10 transition">

                    <!-- TOKEN -->
                    <td class="py-3">
                        <span class="px-2 py-1 bg-white/10 rounded-lg font-mono text-[10px]">
                            <?= substr($t['token'], 0, 10) . "••••"; ?>
                        </span>
                    </td>

                    <!-- OWNER -->
                    <td><?= htmlspecialchars($t['owner']); ?></td>

                    <!-- SCOPES -->
                    <td>
                        <span class="px-2 py-1 bg-ht_bg border border-ht_border rounded text-[10px]">
                            <?= htmlspecialchars($t['scopes']); ?>
                        </span>
                    </td>

                    <!-- EXPIRY -->
                    <td>
                        <?= $t['expires_at'] ? "<span class='text-red-300'>{$t['expires_at']}</span>" : "<span class='text-ht_muted'>No expiry</span>" ?>
                    </td>

                    <!-- STATUS -->
                    <td>
                        <?php if ($t['active']): ?>
                            <span class="px-2 py-1 bg-green-600/30 border border-green-600/40 rounded text-green-300 text-[10px]">Active</span>
                        <?php else: ?>
                            <span class="px-2 py-1 bg-red-600/20 border border-red-600/30 rounded text-red-300 text-[10px]">Disabled</span>
                        <?php endif; ?>
                    </td>

                    <!-- LAST USED -->
                    <td><?= $t['last_used'] ?: '—'; ?></td>

                    <!-- ACTIONS -->
                    <td class="text-right">
                        <div class="flex justify-end gap-3 text-xs">

                            <a href="?toggle=<?= $t['id']; ?>" class="text-blue-300 hover:text-blue-400 transition">
                                Toggle
                            </a>

                            <a href="api_logs.php?token=<?= urlencode($t['token']); ?>"
                               class="text-purple-300 hover:text-purple-400 transition">
                                Logs
                            </a>

                            <button onclick="openEditModal(<?= $t['id']; ?>, '<?= $t['scopes']; ?>', '<?= $t['expires_at']; ?>')"
                                    class="text-ht_blue hover:text-blue-400 transition">
                                Edit
                            </button>

                            <button onclick="confirmDelete(<?= $t['id']; ?>)"
                                    class="text-red-400 hover:text-red-500 transition">
                                Delete
                            </button>

                        </div>
                    </td>

                </tr>
            <?php endforeach; ?>

            </tbody>

        </table>
    </div>


    <!-- EDIT MODAL -->
    <div id="editModal"
         class="hidden fixed inset-0 bg-black/60 backdrop-blur-md flex items-center justify-center z-50 animate-fadeIn">

        <div class="bg-ht_bg2 p-6 rounded-xl border border-ht_border shadow-2xl w-96 animate-scaleIn">

            <h2 class="text-lg font-bold text-ht_blue mb-3">Edit Token</h2>

            <form method="post" class="space-y-3">
                <input type="hidden" name="id" id="modal_id">
                <input type="hidden" name="update_token" value="1">

                <label class="block text-xs text-ht_muted">Scopes</label>
                <select name="scopes" id="modal_scopes"
                        class="w-full bg-ht_bg border border-ht_border rounded p-2 text-white">
                    <option value="read">read</option>
                    <option value="read,write">read,write</option>
                    <option value="read,export">read,export</option>
                    <option value="admin">admin</option>
                </select>

                <label class="block text-xs text-ht_muted mt-2">Expiry (optional)</label>
                <input type="datetime-local" name="expires_at" id="modal_expires"
                       class="w-full bg-ht_bg border border-ht_border rounded p-2 text-white">

                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" onclick="closeModal()" class="text-xs text-ht_muted hover:text-white transition">
                        Cancel
                    </button>

                    <button class="px-4 py-2 bg-ht_blue text-white rounded text-xs hover:bg-ht_blue2 transition shadow">
                        Save
                    </button>
                </div>

                <button type="button"
                        onclick="confirmRegen(document.getElementById('modal_id').value)"
                        class="w-full mt-4 text-xs bg-yellow-600/40 border border-yellow-600/30 text-yellow-300 
                               rounded-lg px-3 py-2 hover:bg-yellow-600/60 transition">
                    🔄 Regenerate Token
                </button>

            </form>
        </div>
    </div>


    <!-- DELETE CONFIRM MODAL -->
    <div id="deleteConfirm"
         class="hidden fixed inset-0 bg-black/60 backdrop-blur-md flex items-center justify-center z-50">

        <div class="bg-ht_bg2 p-6 rounded-xl border border-red-600/40 w-80 shadow-xl animate-scaleIn">

            <h2 class="text-lg font-bold text-red-400 mb-3">Delete API Token?</h2>

            <p class="text-xs text-ht_muted mb-4 leading-relaxed">
                This action is irreversible.<br>
                The token will be permanently removed and can no longer be used.
            </p>

            <div class="flex justify-center gap-3">
                <button onclick="closeDelete()"
                    class="px-3 py-1 text-xs bg-ht_bg border border-ht_border rounded hover:bg-white/10">
                    Cancel
                </button>

                <a id="deleteLink"
                   class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 transition">
                   Delete
                </a>
            </div>
        </div>
    </div>


    <!-- REGENERATE CONFIRM MODAL -->
    <div id="regenConfirm"
         class="hidden fixed inset-0 bg-black/60 backdrop-blur-md flex items-center justify-center z-50">

        <div class="bg-ht_bg2 p-6 rounded-xl border border-yellow-600/40 w-80 shadow-xl animate-scaleIn">

            <h2 class="text-lg font-bold text-yellow-400 mb-3">Regenerate Token?</h2>

            <p class="text-xs text-ht_muted mb-4 leading-relaxed">
                The current token will stop working immediately.<br>
                A new token will be generated.
            </p>

            <div class="flex justify-center gap-3">
                <button onclick="closeRegen()"
                    class="px-3 py-1 text-xs bg-ht_bg border border-ht_border rounded hover:bg-white/10">
                    Cancel
                </button>

                <a id="regenLink"
                   class="px-3 py-1 text-xs bg-yellow-600 text-white rounded hover:bg-yellow-700 transition">
                   Regenerate
                </a>
            </div>
        </div>
    </div>

</section>

<!-- Animations -->
<style>
@keyframes fadeIn { from {opacity:0;} to {opacity:1;} }
@keyframes scaleIn { from {transform:scale(.85);} to {transform:scale(1);} }
.animate-fadeIn { animation: fadeIn .2s ease-out; }
.animate-scaleIn { animation: scaleIn .2s ease-out; }
</style>

<script>
function openEditModal(id, scopes, expires) {
    document.getElementById('modal_id').value = id;
    document.getElementById('modal_scopes').value = scopes;
    document.getElementById('modal_expires').value = expires || '';
    document.getElementById('editModal').classList.remove('hidden');
}
function closeModal() {
    document.getElementById('editModal').classList.add('hidden');
}

/* DELETE */
function confirmDelete(id) {
    document.getElementById('deleteLink').href = "?delete=" + id;
    document.getElementById('deleteConfirm').classList.remove('hidden');
}
function closeDelete() {
    document.getElementById('deleteConfirm').classList.add('hidden');
}

/* REGENERATE */
function confirmRegen(id) {
    document.getElementById('regenLink').href = "/api/token/regenerate.php?id=" + id;
    document.getElementById('regenConfirm').classList.remove('hidden');
}
function closeRegen() {
    document.getElementById('regenConfirm').classList.add('hidden');
}
</script>
</main>
<?php include __DIR__ . '/../partials/footer.php'; ?>
