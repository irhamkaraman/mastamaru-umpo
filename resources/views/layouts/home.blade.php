<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ config('app.name') }}</title>
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

<body class="bg-white min-h-screen">
    @yield('content')
    <!-- Footer -->
    <div class="container mx-auto px-4 py-4 sm:py-8 max-w-md lg:max-w-4xl">
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 text-center">
            <p class="text-gray-600 text-sm sm:text-base">&copy; {{ config('app.name') }}. All rights
                reserved.</p>
            <p class="text-xs sm:text-sm text-gray-500 mt-2">Developed with ❤️ Mastamaru 2026</p>
        </div>
    </div>
    <!-- Additional Scripts -->
    @stack('scripts')
</body>

</html>
