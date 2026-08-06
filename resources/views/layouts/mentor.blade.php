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
    <script src="https://cdn.tailwindcss.com"></script>
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

<body class="mentor-shell-body min-h-screen">
    @yield('content')
    <!-- Footer -->
    <div class="mentor-footer-shell">
        <div class="mentor-footer-inner">
            <div class="mentor-footer-brand"><span class="mentor-footer-mark">M</span><div><strong>MASTAMARU</strong><span>Area pemandu</span></div></div>
            <div class="mentor-footer-meta">&copy; {{ config('app.name') }} <i></i> 2026</div>
        </div>
    </div>
    <!-- Additional Scripts -->
    @stack('scripts')
    <style>
        :root { --mentor-ink:#25204b; --mentor-muted:#68708a; }
        .mentor-shell-body { background:linear-gradient(135deg,#fffdf9 0%,#f7f6ff 55%,#eefaff 100%); font-family:'DM Sans',sans-serif; overflow-x:hidden; }
        .mentor-shell-body h1,.mentor-shell-body h2,.mentor-shell-body h3,.mentor-shell-body h4,.mentor-shell-body strong { font-family:'Plus Jakarta Sans',sans-serif; }
        .mentor-footer-shell { width:calc(100% - 32px); max-width:1180px; margin:0 auto; padding:0 0 28px; }
        .mentor-footer-inner { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:16px 20px; border:1px solid rgba(255,255,255,.9); border-radius:20px; background:rgba(255,255,255,.72); box-shadow:0 14px 36px rgba(75,58,146,.08); backdrop-filter:blur(14px); }
        .mentor-footer-brand { display:flex; align-items:center; gap:10px; color:var(--mentor-ink); }
        .mentor-footer-mark { display:grid; place-items:center; width:32px; height:32px; border-radius:10px; background:linear-gradient(135deg,#dff3ff,#eee8ff); color:#536a9b; font-weight:800; font-size:12px; }
        .mentor-footer-brand div { display:grid; gap:2px; }
        .mentor-footer-brand strong { font-size:11px; letter-spacing:.14em; }
        .mentor-footer-brand span,.mentor-footer-meta { color:var(--mentor-muted); font-size:10px; }
        .mentor-footer-meta { display:flex; align-items:center; gap:9px; }
        .mentor-footer-meta i { width:4px; height:4px; border-radius:50%; background:#9bd6c4; }
        @media(max-width:640px) { .mentor-footer-shell{width:calc(100% - 24px);padding-bottom:18px}.mentor-footer-inner{align-items:flex-start;flex-direction:column;gap:8px;padding:15px 17px} }
    </style>
</body>

</html>
