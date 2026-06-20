<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  @php
    $siteSettings = \App\Models\SiteSetting::first();
    $webThemeColors = config('web-theme-colors');
    $webThemeKey = array_key_exists((string) $siteSettings?->web_theme_color, $webThemeColors)
      ? $siteSettings->web_theme_color
      : 'green';
    $webTheme = $webThemeColors[$webThemeKey];
    $referenceToken = \App\Support\ReferenceUrl::currentReferenceToken();
  @endphp
  @if ($siteSettings?->noindex)
    <meta name="robots" content="noindex, nofollow">
  @endif
  <title>{{ $title ?? 'Cv Manager - Minimal CV' }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    /* ── LIGHT THEME (default) ── */
    :root {
      --color-ink: #172033;
      --color-muted: #586174;
      --color-line: #D8DEE9;
      --color-soft: #F5F7FA;
      --color-row: #F1F4F8;
      --color-accent: {{ $webTheme['light']['accent'] }};
      --color-accentDark: {{ $webTheme['light']['dark'] }};
      --color-accentSoft: {{ $webTheme['light']['soft'] }};
      --color-iconBox: {{ $webTheme['light']['icon'] }};
      --bg-body: #e2e8f0;
      --bg-card: #ffffff;
    }

    /* ── DARK THEME ── */
    html.dark {
      --color-ink: #E2E8F0;
      --color-muted: #94A3B8;
      --color-line: #2D3748;
      --color-soft: #1E2533;
      --color-row: #1A2030;
      --color-accent: {{ $webTheme['dark']['accent'] }};
      --color-accentDark: {{ $webTheme['dark']['dark'] }};
      --color-accentSoft: {{ $webTheme['dark']['soft'] }};
      --color-iconBox: {{ $webTheme['dark']['icon'] }};
      --bg-body: #0F1623;
      --bg-card: #161E2E;
    }

    body {
      background-color: var(--bg-body);
    }

    /* Smooth transitions on theme switch */
    *,
    *::before,
    *::after {
      transition: background-color 0.25s ease, border-color 0.25s ease, color 0.18s ease;
    }

    /* ── INLINE NAV ── */
    .web-nav {
      position: sticky;
      top: 1.5rem;
      align-self: start;
    }

    .web-nav .nav-item {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      padding: 0.55rem 0.9rem;
      font-size: 13.5px;
      font-weight: 600;
      color: var(--color-muted);
      text-decoration: none;
      border-radius: 0.6rem;
      position: relative;
      transition: all 0.15s ease;
    }

    .web-nav .nav-item:hover {
      color: var(--color-ink);
      background: var(--color-soft);
    }

    .web-nav .nav-item.active {
      color: var(--color-accentDark);
      background: var(--color-accentSoft);
      font-weight: 700;
    }

    .web-nav .nav-item .lucide {
      width: 16px;
      height: 16px;
      flex-shrink: 0;
      opacity: 0.7;
    }

    .web-nav .nav-item.active .lucide {
      opacity: 1;
    }

    /* ── THEME TOGGLE (compact) ── */
    #theme-toggle {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      padding: 0.3rem 0.65rem 0.3rem 0.45rem;
      border-radius: 9999px;
      border: 1.5px solid var(--color-line);
      background: var(--bg-card);
      color: var(--color-muted);
      cursor: pointer;
      font-size: 11.5px;
      font-weight: 700;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
      user-select: none;
    }

    html.dark #theme-toggle {
      box-shadow: 0 1px 6px rgba(0, 0, 0, 0.25);
    }

    #theme-toggle:hover {
      border-color: var(--color-accent);
      color: var(--color-accent);
    }

    #theme-toggle .lucide {
      width: 13px;
      height: 13px;
    }

    #toggle-track {
      width: 28px;
      height: 14px;
      border-radius: 9999px;
      background: var(--color-line);
      position: relative;
      flex-shrink: 0;
      transition: background 0.25s;
    }

    html.dark #toggle-track {
      background: var(--color-accent);
    }

    #toggle-thumb {
      position: absolute;
      top: 2px;
      left: 2px;
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: #fff;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
      transition: transform 0.22s cubic-bezier(.4, 0, .2, 1);
    }

    html.dark #toggle-thumb {
      transform: translateX(14px);
    }

    /* ── MOBILE NAV ── */
    .mobile-nav-bar {
      display: none;
    }

    @media (max-width: 1023px) {
      .mobile-nav-bar {
        display: flex;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 100;
        background: var(--bg-card);
        border-top: 1px solid var(--color-line);
        padding: 0.35rem 0.5rem;
        justify-content: space-around;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.06);
      }

      html.dark .mobile-nav-bar {
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.3);
      }

      .mobile-nav-bar a {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
        padding: 0.3rem 0.6rem;
        border-radius: 0.5rem;
        font-size: 10px;
        font-weight: 600;
        color: var(--color-muted);
        text-decoration: none;
      }

      .mobile-nav-bar a.active {
        color: var(--color-accentDark);
      }

      .mobile-nav-bar a .lucide {
        width: 18px;
        height: 18px;
      }

      /* Bottom padding for mobile nav */
      .web-content-area {
        padding-bottom: 4.5rem;
      }
    }

    .reference-greeting {
      position: fixed;
      right: 1.25rem;
      bottom: 1.25rem;
      z-index: 90;
      display: flex;
      max-width: min(360px, calc(100vw - 2rem));
      align-items: center;
      gap: 0.85rem;
      border: 1px solid var(--color-line);
      border-radius: 1rem;
      background: var(--bg-card);
      color: var(--color-ink);
      padding: 0.8rem 0.95rem;
      box-shadow: 0 18px 45px rgba(15, 23, 42, 0.18);
    }

    .reference-greeting img,
    .reference-greeting-placeholder {
      width: 46px;
      height: 46px;
      flex-shrink: 0;
      border-radius: 0.85rem;
      object-fit: cover;
      background: var(--color-accentSoft);
      color: var(--color-accentDark);
    }

    .reference-greeting-placeholder {
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 900;
      font-size: 0.9rem;
    }

    .reference-greeting-title {
      font-size: 0.78rem;
      line-height: 1.25rem;
      font-weight: 900;
      color: var(--color-ink);
    }

    .reference-greeting-text {
      margin-top: 0.1rem;
      font-size: 0.72rem;
      line-height: 1rem;
      font-weight: 650;
      color: var(--color-muted);
    }

    @media (max-width: 1023px) {
      .reference-greeting {
        right: 0.75rem;
        bottom: 4.8rem;
        max-width: calc(100vw - 1.5rem);
      }
    }

    /* ── PRINT ── */
    @page {
      size: A4;
      margin: 0;
    }

    html {
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    .lucide {
      width: 1em;
      height: 1em;
      stroke-width: 1.65;
      vector-effect: non-scaling-stroke;
      flex-shrink: 0;
    }

    @media print {

      html,
      body {
        width: 210mm;
        height: 297mm;
        background: #ffffff !important;
        overflow: hidden;
      }

      .web-nav,
      .mobile-nav-bar,
      .reference-greeting,
      #theme-toggle,
      .nav-header {
        display: none !important;
      }

      .web-grid {
        display: block !important;
      }

      .web-content-area {
        padding: 0 !important;
        margin: 0 !important;
        max-width: none !important;
        width: 100% !important;
      }

      .print-btn {
        display: none !important;
      }

      .a4-page {
        width: 210mm !important;
        height: 297mm !important;
        max-height: 297mm !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 8mm 10mm !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        font-size: 92%;
        overflow: hidden;
        background: #ffffff !important;
        --color-ink: #172033;
        --color-muted: #586174;
        --color-line: #D8DEE9;
        --color-soft: #F5F7FA;
        --color-row: #F1F4F8;
        --color-accent: {{ $webTheme['light']['accent'] }};
        --color-accentDark: {{ $webTheme['light']['dark'] }};
        --color-accentSoft: {{ $webTheme['light']['soft'] }};
        --bg-card: #ffffff;
      }

      a {
        color: inherit;
        text-decoration: none;
      }

      .a4-page section.grid {
        grid-template-columns: 1fr 1fr !important;
        gap: 12px !important;
        margin-top: 10px !important;
      }

      .a4-page header.grid {
        grid-template-columns: 170px 1fr !important;
        gap: 16px !important;
      }

      .a4-page .rounded-2xl {
        padding: 8px !important;
        border-radius: 10px !important;
      }

      .a4-page .rounded-2xl .rounded-lg {
        padding: 3px 6px !important;
        margin-bottom: 4px !important;
        font-size: 10px !important;
      }

      .a4-page .rounded-2xl .rounded-lg .h-7 {
        width: 18px !important;
        height: 18px !important;
        font-size: 10px !important;
        border-radius: 4px !important;
      }

      .a4-page .rounded-2xl .rounded-xl {
        border-radius: 8px !important;
      }

      .a4-page .rounded-xl>div {
        font-size: 10.5px;
      }

      .a4-page .rounded-xl>div.grid {
        grid-template-columns: 100px 1fr !important;
      }

      .a4-page .rounded-xl>div>div {
        padding: 3px 8px !important;
      }

      .a4-page footer {
        margin-top: 8px !important;
        padding-top: 6px !important;
        font-size: 10px !important;
      }

      .a4-page h1 {
        font-size: 32px !important;
      }

      .a4-page header p {
        font-size: 12px !important;
      }

      .a4-page header .rounded-xl {
        padding: 6px 8px !important;
        font-size: 11px !important;
      }

      .a4-page aside img {
        width: 120px !important;
        height: 120px !important;
      }

      .a4-page .cv-qr-link {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 4px !important;
        margin-top: 6px !important;
        padding: 4px 10px !important;
        border-radius: 9999px !important;
        background: var(--color-accent) !important;
        color: #ffffff !important;
        font-size: 10px !important;
        font-weight: 900 !important;
        line-height: 1 !important;
        text-decoration: none !important;
        white-space: nowrap !important;
      }

      .a4-page .cv-qr-link .lucide {
        width: 11px !important;
        height: 11px !important;
        color: #ffffff !important;
      }
    }
  </style>
  <script>
      (function () {
        if (localStorage.getItem('cv-theme') === 'dark') {
          document.documentElement.classList.add('dark');
        }
      })();
  </script>
</head>

<body class="min-h-screen overflow-x-hidden text-ink antialiased">

  <div class="container mx-auto py-6 web-content-area">
    <div class="web-grid grid grid-cols-1 lg:grid-cols-[200px_1fr] gap-6 xl:gap-8 items-start">

      {{-- ── INLINE NAV (desktop: sol kolon) ── --}}
      <nav class="web-nav hidden lg:block">

        {{-- Logo / Branding --}}
        <div class="nav-header mb-5 px-1">
          <div class="flex items-center gap-2.5">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-accentSoft text-accent">
              <i data-lucide="hexagon" style="width:18px;height:18px"></i>
            </span>
            <div>
              <div class="text-[15px] font-black tracking-tight text-ink leading-tight">ACT</div>
              <div class="text-[11px] text-muted font-medium">{{ __('Portfolyo') }}</div>
            </div>
          </div>
        </div>

        {{-- Menü Linkleri --}}
        <div class="space-y-0.5">
          <a href="{{ \App\Support\ReferenceUrl::route('about') }}" wire:navigate @class(['nav-item', 'active' => request()->routeIs('about')])>
            <i data-lucide="user"></i>
            <span>{{ __('Hakkımda') }}</span>
          </a>
          <a href="{{ \App\Support\ReferenceUrl::route('cv') }}" wire:navigate @class(['nav-item', 'active' => request()->routeIs('cv')])>
            <i data-lucide="file-text"></i>
            <span>{{ __('Özgeçmiş') }}</span>
          </a>
          <a href="{{ \App\Support\ReferenceUrl::route('portfolio.index') }}" wire:navigate @class(['nav-item', 'active' => request()->routeIs('portfolio.*')])>
            <i data-lucide="briefcase"></i>
            <span>{{ __('Portfolyo') }}</span>
          </a>
          <a href="{{ \App\Support\ReferenceUrl::route('contact') }}" wire:navigate @class(['nav-item', 'active' => request()->routeIs('contact')])>
            <i data-lucide="mail"></i>
            <span>{{ __('İletişim') }}</span>
          </a>
        </div>

        {{-- Ayraç --}}
        <div class="my-4 mx-3 border-t border-line/50"></div>

        {{-- Tema Toggle --}}
        <div class="px-1">
          <button style="width: 100%;" id="theme-toggle" aria-label="{{ __('Tema değiştir') }}" onclick="toggleTheme()">
            <span id="toggle-track"><span id="toggle-thumb"></span></span>
            <i id="theme-icon" data-lucide="sun"></i>
            <span id="theme-label">Light</span>
          </button>
        </div>

      </nav>

      {{-- ── İÇERİK ALANI (sağ kolon) ── --}}
      <div class="min-w-0">
        {{ $slot }}
      </div>

    </div>
  </div>

  {{-- ── MOBİL BOTTOM NAV ── --}}
  <div class="mobile-nav-bar">
    <a href="{{ \App\Support\ReferenceUrl::route('about') }}" wire:navigate @class(['active' => request()->routeIs('about')])>
      <i data-lucide="user"></i>
      <span>{{ __('Hakkımda') }}</span>
    </a>
    <a href="{{ \App\Support\ReferenceUrl::route('cv') }}" wire:navigate @class(['active' => request()->routeIs('cv')])>
      <i data-lucide="file-text"></i>
      <span>{{ __('Özgeçmiş') }}</span>
    </a>
    <a href="{{ \App\Support\ReferenceUrl::route('portfolio.index') }}" wire:navigate @class(['active' => request()->routeIs('portfolio.*')])>
      <i data-lucide="briefcase"></i>
      <span>{{ __('Portfolyo') }}</span>
    </a>
    <a href="{{ \App\Support\ReferenceUrl::route('contact') }}" wire:navigate @class(['active' => request()->routeIs('contact')])>
      <i data-lucide="mail"></i>
      <span>{{ __('İletişim') }}</span>
    </a>
    <button onclick="toggleTheme()" style="background:none;border:none;cursor:pointer;">
      <i id="theme-icon-mobile" data-lucide="sun" style="width:18px;height:18px;color:var(--color-muted)"></i>
      <span style="font-size:10px;font-weight:600;color:var(--color-muted)" id="theme-label-mobile">{{ __('Tema') }}</span>
    </button>
  </div>

  @if ($referenceToken)
    <aside class="reference-greeting" aria-label="Referans karşılama mesajı">
      @if ($referenceToken->image)
        <img src="{{ asset($referenceToken->image) }}" alt="" />
      @else
        <span class="reference-greeting-placeholder">{{ str($referenceToken->name)->substr(0, 2)->upper() }}</span>
      @endif
      <div class="min-w-0">
        <div class="reference-greeting-title">{{ __('Merhaba') }} {{ $referenceToken->name }}</div>
        <div class="reference-greeting-text">{{ __('enjoy') }}</div>
      </div>
    </aside>
  @endif

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (window.lucide) window.lucide.createIcons();
    });

    document.addEventListener('livewire:navigated', () => {
      if (window.lucide) window.lucide.createIcons();
      applyTheme(document.documentElement.classList.contains('dark'));
    });

    document.addEventListener('livewire:init', () => {
      Livewire.hook('morph.updated', ({ el, component }) => {
        if (window.lucide) window.lucide.createIcons();
      });
    });

    function applyTheme(dark) {
      const html = document.documentElement;
      const icon = document.getElementById('theme-icon');
      const iconMobile = document.getElementById('theme-icon-mobile');
      const label = document.getElementById('theme-label');
      if (dark) {
        html.classList.add('dark');
        if (icon) icon.setAttribute('data-lucide', 'moon');
        if (iconMobile) iconMobile.setAttribute('data-lucide', 'moon');
        if (label) label.textContent = 'Dark';
      } else {
        html.classList.remove('dark');
        if (icon) icon.setAttribute('data-lucide', 'sun');
        if (iconMobile) iconMobile.setAttribute('data-lucide', 'sun');
        if (label) label.textContent = 'Light';
      }
      if (window.lucide) window.lucide.createIcons();
    }

    function toggleTheme() {
      const isDark = document.documentElement.classList.contains('dark');
      applyTheme(!isDark);
      localStorage.setItem('cv-theme', !isDark ? 'dark' : 'light');
    }

    applyTheme(document.documentElement.classList.contains('dark'));
  </script>
</body>

</html>
