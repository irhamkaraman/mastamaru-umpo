<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8'
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="min-h-screen text-slate-800">
    <div class="site-orbit site-orbit-one" aria-hidden="true"></div>
    <div class="site-orbit site-orbit-two" aria-hidden="true"></div>
    <header class="site-header">
        <div class="site-header-inner">
            <a href="{{ route('home.index') }}" class="brand-lockup" aria-label="Kembali ke halaman utama">
                <span class="brand-logo brand-logo-mastamaru"><img src="{{ asset('img/logo_mastamaru_2025.png') }}" alt="Logo MASTAMARU" loading="eager"></span>
                <span class="brand-copy"><strong>MASTAMARU</strong><span>Sistem presensi mahasiswa baru</span></span>
            </a>
            <div class="campus-lockup">
                <span class="campus-copy">Universitas Muhammadiyah<br class="hidden sm:block"> Ponorogo</span>
                <span class="brand-logo brand-logo-campus"><img src="{{ asset('img/logo_Universitas-Muhammadiyah-Ponorogo-1.png') }}" alt="Logo Universitas Muhammadiyah Ponorogo" loading="eager"></span>
            </div>
        </div>
    </header>
    @yield('content')
    <!-- Footer -->
    <div class="container mx-auto px-4 py-4 sm:py-8 max-w-md lg:max-w-4xl relative z-10">
        <div class="ui-panel rounded-xl p-4 sm:p-6 text-center">
            <p class="text-gray-600 text-sm sm:text-base">&copy; {{ config('app.name') }}. All rights
                reserved.</p>
            <p class="text-xs sm:text-sm text-gray-500 mt-2">Developed with ❤️ Mastamaru 2026</p>
        </div>
    </div>
    <!-- Additional Scripts -->
    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('form').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (event.defaultPrevented) return;
                    const button = form.querySelector('button[type="submit"]');
                    if (!button || button.disabled) return;
                    button.disabled = true;
                    button.classList.add('is-loading');
                    button.setAttribute('aria-busy', 'true');
                    button.innerHTML = '<span class="ui-spinner" aria-hidden="true"></span><span>Memproses...</span>';
                });
            });
            document.querySelectorAll('[data-loading-button]').forEach(function (button) {
                button.addEventListener('click', function () {
                    if (button.disabled || button.dataset.loadingButton === 'false') return;
                    button.disabled = true;
                    button.classList.add('is-loading');
                    button.setAttribute('aria-busy', 'true');
                    button.innerHTML = '<span class="ui-spinner" aria-hidden="true"></span><span>Memproses...</span>';
                });
            });
        });
    </script>
    <style>
        :root { --ink:#25204b; --muted:#68708a; --purple:#4b3a92; --orange:#f97316; }
        * { box-sizing:border-box; }
        body { background:linear-gradient(135deg,#fffdf9 0%,#f6f5ff 52%,#edf9ff 100%); font-family:'DM Sans',sans-serif; overflow-x:hidden; }
        h1,h2,h3,h4,h5,h6,.brand-copy strong { font-family:'Plus Jakarta Sans',sans-serif; }
        button,a,input,select { -webkit-tap-highlight-color:transparent; }
        button:focus-visible,a:focus-visible,input:focus-visible,select:focus-visible { outline:3px solid rgba(249,115,22,.42); outline-offset:3px; }
        .site-orbit { position:fixed; border-radius:999px; pointer-events:none; z-index:0; filter:blur(2px); }
        .site-orbit-one { width:380px; height:380px; top:90px; right:-180px; background:rgba(251,191,36,.12); }
        .site-orbit-two { width:300px; height:300px; bottom:40px; left:-160px; background:rgba(96,165,250,.12); }
        .site-header { position:relative; z-index:20; padding:18px 16px 4px; }
        .site-header-inner { max-width:1120px; margin:0 auto; display:flex; align-items:center; justify-content:space-between; gap:16px; }
        .brand-lockup,.campus-lockup { display:inline-flex; align-items:center; gap:12px; text-decoration:none; }
        .brand-logo { display:grid; place-items:center; flex:0 0 auto; border-radius:18px; background:rgba(255,255,255,.78); box-shadow:0 12px 30px rgba(75,58,146,.12); }
        .brand-logo img { display:block; object-fit:contain; }
        .brand-logo-mastamaru { width:58px; height:58px; padding:7px; }
        .brand-logo-mastamaru img { width:100%; height:100%; }
        .brand-logo-campus { width:48px; height:48px; padding:5px; }
        .brand-logo-campus img { width:100%; height:100%; }
        .brand-copy { display:grid; gap:2px; color:var(--ink); }
        .brand-copy strong { font-size:15px; letter-spacing:.12em; }
        .brand-copy span,.campus-copy { color:var(--muted); font-size:11px; line-height:1.35; }
        .campus-lockup { text-align:right; }
        .campus-copy { font-weight:700; color:var(--purple); text-transform:uppercase; letter-spacing:.04em; }
        .ui-panel { background:rgba(255,255,255,.82); border:1px solid rgba(255,255,255,.9); box-shadow:0 18px 50px rgba(75,58,146,.10); backdrop-filter:blur(16px); }
        .ui-spinner { width:1.05em; height:1.05em; border:2px solid currentColor; border-right-color:transparent; border-radius:999px; animation:ui-spin .7s linear infinite; display:inline-block; vertical-align:-.15em; }
        .is-loading { cursor:wait!important; opacity:.86; transform:none!important; }
        @keyframes ui-spin { to { transform:rotate(360deg); } }
        @media(max-width:640px) { .site-header{padding-top:12px}.site-header-inner{align-items:flex-start}.brand-logo-mastamaru{width:48px;height:48px;border-radius:14px}.brand-logo-campus{width:40px;height:40px;border-radius:12px}.brand-copy strong{font-size:12px}.brand-copy span,.campus-copy{font-size:9px}.campus-copy{display:none} }
        @media(prefers-reduced-motion:reduce) { *,*::before,*::after{animation-duration:.01ms!important;transition-duration:.01ms!important;scroll-behavior:auto!important} }
    </style>
</body>

</html>
