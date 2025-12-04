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

<section class="min-h-[70vh] flex items-center justify-center px-4">

  <div class="w-full max-w-sm bg-ht_bg2 border border-ht_border rounded-xl shadow-lg p-8">

    <h1 class="text-2xl font-extrabold text-ht_blue">Admin Portal Login</h1>
    <p class="text-xs text-ht_muted mb-6">Restricted access — authorized users only.</p>

    <?php if ($error): ?>
      <div class="bg-red-600/20 border border-red-600 text-red-300 text-xs rounded-md p-2 mb-4">
        <?= htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <form method="post" class="space-y-5">

      <div>
        <label class="block text-xs text-ht_muted mb-1">Username</label>
        <input name="username"
               class="w-full px-3 py-2 text-sm rounded-lg bg-ht_bg border border-ht_border text-ht_text focus:outline-none focus:ring-1 focus:ring-ht_blue"
               autocomplete="username"
               autofocus>
      </div>

      <div>
        <label class="block text-xs text-ht_muted mb-1">Password</label>
        <input type="password" name="password"
               class="w-full px-3 py-2 text-sm rounded-lg bg-ht_bg border border-ht_border text-ht_text focus:outline-none focus:ring-1 focus:ring-ht_blue"
               autocomplete="current-password">
      </div>

      <button type="submit"
        class="w-full bg-ht_blue text-white py-2.5 rounded-lg text-sm font-semibold hover:bg-blue-600 transition">
        Sign In
      </button>

    </form>

    <p class="text-[10px] text-ht_muted mt-6 text-center">IntelCTX Admin Interface v1.0</p>

  </div>

</section>
    </main>
<?php include __DIR__ . '/../partials/footer.php'; ?>
