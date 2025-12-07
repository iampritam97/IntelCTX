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
<!-- Local Custom Logo Font -->
<style>
@font-face {
  font-family: 'LogoFont';
  src: url('../assets/fonts/r-Light.ttf') format('truetype');
  font-weight: 400;
  font-style: normal;
}
.logo-text {
  font-family: 'LogoFont', sans-serif;
  font-size: 20px;
  font-weight: 700;
  letter-spacing: 0.5px;
}
</style>
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
  selector: '.richtext',
  height: 180,
  menubar: false,
  plugins: 'lists link code',
  toolbar: 'undo redo | bold italic underline | bullist numlist | link | code',
  content_style: "body { font-size: 14px; }"
});
</script>
    <!-- Primary SEO -->
<meta name="title" content="IntelCTX — Cyber Threat Intelligence Platform">
<meta name="description" content="Modern cyber threat intelligence for SOC, DFIR, Threat Hunters and Detection Engineers. Explore APT groups, malware, TTPs, and actionable IOCs.">

<meta name="keywords" content="Threat Intelligence, APT Groups, Malware Analysis, IOC Database, MITRE ATT&CK, Cybersecurity, DFIR, Detection Engineering, IntelCTX">
<meta name="author" content="IntelCTX Research Team">
<meta name="robots" content="index, follow">

<!-- Canonical URL -->
<link rel="canonical" href="https://intelctx.com/">

<!-- Open Graph (Facebook / LinkedIn) -->
<meta property="og:title" content="IntelCTX — Threat Intelligence for Modern Defenders">
<meta property="og:description" content="Enterprise-grade intelligence: APT encyclopedia, malwarepedia, threat tools, IOCs, detection opportunities, and hunt builder.">
<meta property="og:type" content="website">
<meta property="og:url" content="https://intelctx.com/">
<meta property="og:image" content="https://intelctx.com/assets/og-intelctx.png">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="IntelCTX — Cyber Threat Intelligence Platform">
<meta name="twitter:description" content="Modern cyber threat intelligence designed for SOC, DFIR, and Incident Response teams.">
<meta name="twitter:image" content="https://intelctx.com/assets/og-intelctx.png">

    <meta charset="UTF-8">
    <title>IntelCTX — Threat Intelligence for Modern Defenders</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebApplication",
  "name": "IntelCTX",
  "url": "https://intelctx.com",
  "description": "AI-assisted threat intelligence platform featuring APT encyclopedia, malwarepedia, threat tool registry and detection builder.",
  "publisher": {
    "@type": "Organization",
    "name": "IntelCTX",
    "logo": "https://intelctx.com/assets/logo-dark.png"
  },
  "applicationCategory": "Cybersecurity",
  "operatingSystem": "All",
  "offers": {
    "@type": "Offer",
    "price": "0",
    "priceCurrency": "USD"
  }
}
</script>

    <script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        ht_bg: "#0B0E11",
        ht_bg2: "#13161A",
        ht_text: "#E5E7EB",
        ht_muted: "#9CA3AF",
        ht_blue: "#3B82F6",
        ht_blue2: "#2563FF",
        ht_border: "#1F242C",
      },
      fontFamily: {
        mono: ["Fira Code", "Menlo", "monospace"],
        hacker: ["Inter", "sans-serif"],
      },
      backgroundImage: {
        grid: "url('/assets/grid.svg')",
      },
    }
  }
}

</script>
<script>
function bindSuggest(inputId) {
  const input = document.getElementById(inputId);
  if (!input) return;

  let box = document.createElement('div');
  box.className = "absolute z-50 mt-1 bg-white border border-border rounded shadow-sm w-full hidden";
  input.parentNode.style.position = 'relative';
  input.parentNode.appendChild(box);

  input.addEventListener('input', async () => {
    const q = input.value.trim();
    if (!q) { box.innerHTML = ''; box.classList.add('hidden'); return; }

    const res = await fetch('search_suggest.php?q=' + encodeURIComponent(q));
    const suggestions = await res.json();
    if (!suggestions.length) { box.innerHTML = ''; box.classList.add('hidden'); return; }

    box.innerHTML = suggestions.map(s => {
      return `<a class="block px-3 py-2 text-sm hover:bg-slate-50" href="${s.type=='apt'?'/apt.php?id='+s.id:(s.type=='mal'?'/malware_view.php?id='+s.id:'/tools_view.php?id='+s.id)}">
        ${s.name} <span class="text-xs text-slate-400">(${s.type})</span>
      </a>`;
    }).join('');
    box.classList.remove('hidden');
  });

  document.addEventListener('click', (e) => { if (!input.parentNode.contains(e.target)) box.classList.add('hidden'); });
}

// call on DOM ready for the main search input id "aptSearch"
document.addEventListener('load', () => bindSuggest('aptSearch'));
</script>

</head>
<body class="bg-ht_bg text-ht_text bg-grid bg-[length:60px_60px]">

<header class="sticky top-0 z-40">

  <div class="backdrop-blur-xl bg-black/30 border-b border-white/10 shadow-lg">
    <nav class="max-w-7xl mx-auto px-8 py-4 flex items-center justify-between">

      <!-- LOGO -->
       <a href="https://intelctx.com" class="flex items-center gap-2 group">
      <div class="flex items-center gap-2">
        <span class="text-ht_blue font-mono text-xl">&gt;</span>
        <span class="logo-text text-xl tracking-wide text-white">IntelCTX</span>
      </div>
</a>

      <!-- SEARCH BAR (Desktop) -->
      <div class="hidden md:flex w-1/2">
        <form action="search.php" class="w-full relative">
          <input id="aptSearch"
            type="text"
            name="q"
            placeholder="Search APTs, Malware, Tools…"
            class="w-full px-4 py-2 rounded-lg bg-white/5 text-ht_text 
                   border border-white/10 placeholder-gray-400
                   focus:border-ht_blue focus:ring-0 backdrop-blur-sm transition" />
        </form>
      </div>

      <!-- RIGHT SIDE -->
      <div class="flex items-center gap-4">

        <!-- THEME TOGGLE -->
        <!-- <button onclick="toggleTheme()" 
          class="p-2 rounded-lg bg-white/5 border border-white/10 
                 hover:bg-white/10 transition text-gray-300"
          title="Toggle Theme">
          🌓
        </button> -->
        <!-- HAMBURGER MENU -->
<div class="relative">
    <button id="hamburgerBtn" class="p-2 rounded-lg hover:bg-white/5 transition">
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="w-6 h-6 text-slate-300" fill="none" viewBox="0 0 24 24"
             stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    <!-- DROPDOWN MENU -->
    <div id="hamburgerMenu"
         class="hidden absolute right-0 mt-2 w-48 rounded-xl border border-ht_border bg-ht_bg2 shadow-xl z-50">

        <!-- <a href="/index.php" 
           class="block px-4 py-2 text-xs text-slate-300 hover:bg-white/5 rounded-t-xl">
           Home
        </a> -->

        <a href="hunter.php" 
           class="block px-4 py-2 text-xs text-slate-300 hover:bg-white/5">
           Hunter
        </a>

        <a href="malware.php" 
           class="block px-4 py-2 text-xs text-slate-300 hover:bg-white/5">
           Malware Families
        </a>

        <a href="tools.php" 
           class="block px-4 py-2 text-xs text-slate-300 hover:bg-white/5">
           Threat Tools
        </a>

        <a href="submit_intel.php" 
           class="block px-4 py-2 text-xs text-slate-300 hover:bg-white/5">
           Submit Intel
        </a>

        <!-- <a href="faq.php" 
           class="block px-4 py-2 text-xs text-slate-300 hover:bg-white/5 rounded-b-xl">
           FAQs
        </a> -->
    </div>
</div>

        <!-- SIGN IN -->
        <a href="admin/login.php"
          class="px-4 py-1 rounded-lg text-sm bg-white/5 border border-white/10 
                 hover:bg-ht_blue hover:border-ht_blue hover:text-white transition">
          ⌁ Sign In
        </a>
      </div>

    </nav>
  </div>
<script>
const btn = document.getElementById("hamburgerBtn");
const menu = document.getElementById("hamburgerMenu");

btn.addEventListener("click", () => {
    menu.classList.toggle("hidden");
});

// Close dropdown if clicking elsewhere
document.addEventListener("click", (e) => {
    if (!btn.contains(e.target) && !menu.contains(e.target)) {
        menu.classList.add("hidden");
    }
});
</script>

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

/* Logo refinement */
.logo-text {
  font-family: "LogoFont", sans-serif;
  font-weight: 700;
  letter-spacing: 0.5px;
}

/* Search dropdown suggestions (glass-style) */
#aptSearch + div {
  background: rgba(0,0,0,0.6);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 8px;
}

#aptSearch + div a:hover {
  background: rgba(255,255,255,0.06) !important;
}

</style>


<main class="max-w-6xl mx-auto px-4 py-6">
