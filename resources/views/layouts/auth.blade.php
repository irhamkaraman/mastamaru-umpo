<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title') | {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @livewireStyles
    <style>
        :root { --auth-ink:#25204b; --auth-muted:#68708a; --auth-sky:#eaf6ff; --auth-lilac:#f1edff; }
        @import "tailwindcss";

        @theme {
            --color-primary-50: #eff6ff;
            --color-primary-100: #dbeafe;
            --color-primary-200: #bfdbfe;
            --color-primary-300: #93c5fd;
            --color-primary-400: #60a5fa;
            --color-primary-500: #3b82f6;
            --color-primary-600: #2563eb;
            --color-primary-700: #1d4ed8;
            --color-primary-800: #1e40af;
            --color-primary-900: #1e3a8a;
            --color-primary-950: #172554;
        }

        .gradient-bg { background:linear-gradient(135deg,#fffdf9 0%,#f7f6ff 52%,#eefaff 100%); font-family:'DM Sans',sans-serif; overflow-x:hidden; }
        .gradient-bg h1,.gradient-bg h2,.gradient-bg h3,.gradient-bg strong { font-family:'Plus Jakarta Sans',sans-serif; }
        .auth-orbit { position:fixed; border-radius:999px; pointer-events:none; filter:blur(1px); }
        .auth-orbit-one { width:390px; height:390px; right:-180px; top:-150px; background:rgba(186,230,253,.42); }
        .auth-orbit-two { width:300px; height:300px; left:-160px; bottom:-150px; background:rgba(221,214,254,.35); }

        .glass-effect { backdrop-filter:blur(16px); background:rgba(255,255,255,.78); border:1px solid rgba(255,255,255,.9); box-shadow:0 24px 70px rgba(75,58,146,.12); }

        .input-focus {
            transition: all 0.3s ease;
        }

        .input-focus:focus { transform:translateY(-1px); box-shadow:0 10px 25px rgba(75,58,146,.10); }

        .btn-hover {
            transition: all 0.3s ease;
        }

        .btn-hover:hover { transform:translateY(-2px); box-shadow:0 12px 28px rgba(86,111,185,.24); }

        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body class="h-full gradient-bg">
    <div class="auth-orbit auth-orbit-one" aria-hidden="true"></div>
    <div class="auth-orbit auth-orbit-two" aria-hidden="true"></div>
    <div class="min-h-full flex items-center justify-center py-8 sm:py-12 px-4 sm:px-6 lg:px-8 relative z-10">
        @yield('content')
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
