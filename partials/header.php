<?php require_once __DIR__ . '/../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<script>
// Initialize theme from localStorage
if (localStorage.getItem('theme') === 'dark') {
  document.documentElement.classList.add('dark');
} else {
  document.documentElement.classList.remove('dark');
}

// Attach toggleTheme to window so it is globally callable
window.toggleTheme = function() {
  const root = document.documentElement;
  const isDark = root.classList.contains("dark");

  if (isDark) {
    root.classList.remove("dark");
    localStorage.setItem("theme", "light");
  } else {
    root.classList.add("dark");
    localStorage.setItem("theme", "dark");
  }

  console.log("Theme toggled → dark mode:", !isDark);
};
</script>


<head>

    <meta charset="UTF-8">
    <title>IntelCTX — Threat Intelligence for Modern Defenders</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        primary: "#0F172A",
        accent: "#2563EB",
        border: "#E2E8F0",
        light: "#F8FAFC",
        darkbg: "#030712",
        darktext: "#F3F4F6"
      },
      fontFamily: {
        sans: ["Inter", "sans-serif"],
        mono: ["Fira Code", "monospace"]
      }
    }
  }
}
</script>

</head>
<body class="bg-slate-100 text-slate-900">
<header class="bg-white border-b border-gray-200 px-6 py-4 transition dark:bg-gray-900 dark:border-gray-800">
  <nav class="max-w-7xl mx-auto flex justify-between items-center">

    <!-- Logo -->
    <div class="flex items-center gap-3">
      <!-- <img src="assets/logo.png" class="h-7 w-7 opacity-80 dark:opacity-90"> -->
      <span class="text-xl font-bold text-primary dark:text-darktext tracking-tight">IntelCTX</span>
    </div>

    <!-- Desktop Links -->
    <div class="hidden md:flex gap-6 text-sm font-medium items-center">
      <a href="index.php" class="nav-link">Encyclopedia</a>
      <a href="malware.php" class="nav-link">Malware</a>
      <a href="tools.php" class="nav-link">Threat Tools</a>
      <a href="timeline.php" class="nav-link">Timeline</a>
      <a href="hunter.php" class="nav-link">Hunt Builder</a>
      <a href="admin/login.php" class="nav-link">Admin</a>

      <!-- ✅ THEME TOGGLE FIXED -->
<!-- <button onclick="toggleTheme()" 
  class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
  <span class="text-sm dark:text-gray-300">🌗</span>
</button> -->

    </div>

    <!-- Mobile Button -->
    <button onclick="document.getElementById('mobile_nav').classList.toggle('hidden')" 
      class="md:hidden border border-border dark:border-gray-700 rounded-lg px-3 py-1 dark:text-darktext">
      ☰
    </button>

  </nav>

  <!-- Mobile Links -->
  <div id="mobile_nav" class="hidden max-w-7xl mx-auto mt-4 grid gap-3 text-xs md:hidden dark:text-darktext">
      <a href="index.php" class="mobile-link">APT Encyclopedia</a>
      <a href="malware.php" class="mobile-link">Malware</a>
      <a href="tools.php" class="mobile-link">Threat Tools</a>
      <a href="timeline.php" class="mobile-link">Timeline</a>
      <a href="hunter.php" class="mobile-link">Hunt Builder</a>
      <a href="admin/login.php" class="mobile-link">Admin</a>

      <!-- ✅ Mobile Theme Toggle -->
      <button onclick="toggleTheme()" class="mobile-link text-left">🌗 Toggle Theme</button>
  </div>
</header>

<style>
.nav-link {
  color:#475569;
  transition:0.2s;
}
.nav-link:hover {
  color:#2563EB;
}

.mobile-link {
  @apply p-2 border border-border rounded-lg dark:border-gray-700 hover:bg-slate-50 dark:hover:bg-gray-800 transition;
}
</style>


<main class="max-w-6xl mx-auto px-4 py-6">
