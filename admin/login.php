<?php
require_once __DIR__ . '/../auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    if (login($u, $p)) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
include __DIR__ . '/../partials/header.php';
?>
<section class="max-w-sm mx-auto bg-white border border-slate-200 rounded-lg p-4 text-sm">
    <h1 class="text-lg font-semibold mb-3">Admin Login</h1>
    <?php if ($error): ?>
        <p class="text-xs text-red-600 mb-2"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post" class="space-y-3">
        <div>
            <label class="block text-xs mb-1">Username</label>
            <input name="username" class="w-full border border-slate-300 rounded px-2 py-1.5 text-sm">
        </div>
        <div>
            <label class="block text-xs mb-1">Password</label>
            <input type="password" name="password"
                   class="w-full border border-slate-300 rounded px-2 py-1.5 text-sm">
        </div>
        <button class="border border-slate-400 rounded px-3 py-1.5 text-sm" type="submit">
            Login
        </button>
    </form>
</section>
<?php include __DIR__ . '/../partials/footer.php'; ?>
