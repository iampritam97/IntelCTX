<?php
require_once __DIR__ . '/../auth.php';
require_login();
$pdo = get_db();

$groups = $pdo->query("SELECT id, name, country, motivation, risk_score, updated_at FROM apt_groups ORDER BY updated_at DESC")->fetchAll();
include __DIR__ . '/../partials/header.php';
?>
<section class="space-y-4 text-sm">
    <div class="flex items-center justify-between">
        <h1 class="text-lg font-semibold">Admin Panel</h1>
        <div class="flex gap-2 text-xs">
            <a href="apt_edit.php" class="border border-slate-400 rounded px-3 py-1">New APT Group</a>
            <a href="malware.php" class="border border-slate-300 rounded px-3 py-1">Malware master</a>
            <a href="tools.php" class="border border-slate-300 rounded px-3 py-1">Tools master</a>
            <a href="logout.php" class="border border-slate-300 rounded px-3 py-1">Logout</a>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-lg p-3">
        <h2 class="text-sm font-semibold mb-2">APT Groups</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead class="border-b border-slate-200 text-slate-500">
                    <tr>
                        <th class="text-left py-1 pr-4">Name</th>
                        <th class="text-left py-1 pr-4">Country</th>
                        <th class="text-left py-1 pr-4">Motivation</th>
                        <th class="text-left py-1 pr-4">Risk</th>
                        <th class="text-left py-1 pr-4">Updated</th>
                        <th class="text-right py-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($groups as $g): ?>
                    <tr class="border-b border-slate-100">
                        <td class="py-1 pr-4"><?php echo htmlspecialchars($g['name']); ?></td>
                        <td class="py-1 pr-4"><?php echo htmlspecialchars($g['country']); ?></td>
                        <td class="py-1 pr-4"><?php echo htmlspecialchars($g['motivation']); ?></td>
                        <td class="py-1 pr-4"><?php echo (int)$g['risk_score']; ?></td>
                        <td class="py-1 pr-4"><?php echo $g['updated_at']; ?></td>
                        <td class="py-1 text-right">
                            <a href="apt_edit.php?id=<?php echo $g['id']; ?>" class="underline mr-2">Edit</a>
                            <a href="apt_delete.php?id=<?php echo $g['id']; ?>"
                               class="underline text-red-600"
                               onclick="return confirm('Delete this APT group?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$groups): ?>
                    <tr><td colspan="6" class="py-2 text-slate-500">No APT groups yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../partials/footer.php'; ?>
