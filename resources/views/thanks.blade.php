<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mastamaru 2026 - Terima Kasih</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                        'poppins': ['Poppins', 'sans-serif']
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 1.2s ease-out',
                        'float': 'float 8s ease-in-out infinite',
                        'glow': 'glow 3s ease-in-out infinite alternate',
                        'slide-in': 'slideIn 1s ease-out',
                        'scale-in': 'scaleIn 0.8s ease-out'
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(40px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px) rotate(0deg)' },
                            '50%': { transform: 'translateY(-15px) rotate(2deg)' }
                        },
                        glow: {
                            '0%': { boxShadow: '0 0 20px rgba(34, 197, 94, 0.3)' },
                            '100%': { boxShadow: '0 0 40px rgba(34, 197, 94, 0.6)' }
                        },
                        slideIn: {
                            '0%': { opacity: '0', transform: 'translateX(-50px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' }
                        },
                        scaleIn: {
                            '0%': { opacity: '0', transform: 'scale(0.8)' },
                            '100%': { opacity: '1', transform: 'scale(1)' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .animate-fade-in-up {
            animation: fadeInUp 1s ease-out;
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        
        .font-inter {
            font-family: 'Inter', sans-serif;
        }
        
        .font-poppins {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="font-inter bg-gradient-to-br from-emerald-50 via-green-50 to-teal-50 min-h-screen p-4 relative" x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 200)">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute top-10 left-10 w-32 h-32 bg-green-500 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 right-20 w-48 h-48 bg-emerald-400 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 left-1/4 w-40 h-40 bg-teal-500 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 right-10 w-36 h-36 bg-green-600 rounded-full blur-3xl"></div>
    </div>
    <div class="max-w-6xl w-full mx-auto bg-white/90 backdrop-blur-xl rounded-3xl shadow-2xl border border-green-100/50 overflow-hidden animate-fade-in-up relative z-10 my-8" x-show="loaded" x-transition.duration.1200ms>
        <!-- Header -->
        <div class="bg-gradient-to-r from-emerald-600 via-green-600 to-teal-600 text-white relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-transparent via-white/5 to-black/10"></div>
            <div class="absolute inset-0">
                <div class="absolute top-0 left-0 w-full h-full bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-30"></div>
            </div>
            <div class="relative z-10 px-8 py-16 text-center">
                <!-- Logo Section -->
                <div class="flex justify-center items-center gap-10 mb-8">
                    <div class="relative">
                        <img src="{{ asset('img/logo_Universitas-Muhammadiyah-Ponorogo-1.png') }}" alt="Logo Universitas Muhammadiyah Ponorogo" class="h-20 w-20 md:h-24 md:w-24 object-contain animate-float drop-shadow-2xl">
                        <div class="absolute inset-0 bg-white/20 rounded-full blur-xl animate-glow"></div>
                    </div>
                    <div class="relative">
                        <img src="{{ asset('img/logo_mastamaru_2025.png') }}" alt="Logo MASTAMARU 2025" class="h-20 w-20 md:h-24 md:w-24 object-contain animate-float drop-shadow-2xl" style="animation-delay: 1s">
                        <div class="absolute inset-0 bg-white/20 rounded-full blur-xl animate-glow" style="animation-delay: 1s"></div>
                    </div>
                </div>
                <h1 class="font-poppins text-5xl md:text-7xl font-black mb-6 tracking-tight">
                    <span class="bg-gradient-to-r from-white via-green-100 to-emerald-200 bg-clip-text text-transparent drop-shadow-lg">
                        MASTAMARU
                    </span>
                </h1>
                <p class="font-inter text-xl md:text-2xl font-light opacity-95 tracking-wide">
                    Masa Ta'aruf Mahasiswa Baru 2026
                </p>
            </div>
        </div>
        
        <!-- Content -->
        <div class="px-8 py-16 text-center">
            <h2 class="font-poppins text-5xl md:text-6xl font-black mb-12 bg-gradient-to-r from-emerald-700 via-green-600 to-teal-700 bg-clip-text text-transparent animate-slide-in">
                Terima Kasih!
                <span class="inline-block animate-float text-green-500 text-4xl md:text-5xl ml-4">🌿</span>
            </h2>
            
            <p class="font-inter text-xl md:text-2xl text-gray-700 leading-relaxed mb-16 max-w-5xl mx-auto font-light animate-fade-in-up" style="animation-delay: 0.3s">
                Dengan penuh rasa syukur, kami mengucapkan terima kasih yang sebesar-besarnya kepada seluruh Peserta, Pemandu, Panitia Mahasiswa, dan Civitas Akademika yang telah berpartisipasi dalam acara MASTAMARU 2026. Keberhasilan acara ini tidak lepas dari dukungan dan antusiasme dari semua pihak.
            </p>
            
            <!-- Appreciation Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16" x-data="{ cards: [1,2,3,4] }">
                <div class="group bg-gradient-to-br from-emerald-500 via-green-500 to-teal-500 text-white p-8 rounded-3xl shadow-xl transform hover:-translate-y-3 hover:scale-105 transition-all duration-500 hover:shadow-2xl border border-green-400/30 animate-scale-in" x-show="loaded" x-transition.delay.200ms>
                    <div class="text-center relative">
                        <div class="bg-white/20 rounded-full p-4 w-20 h-20 mx-auto mb-6 group-hover:bg-white/30 transition-all duration-300">
                            <svg class="w-12 h-12 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                            </svg>
                        </div>
                        <h3 class="font-poppins text-xl font-bold mb-3">Peserta Mastamaru</h3>
                        <p class="font-inter text-sm opacity-95 leading-relaxed">Terima kasih atas antusiasme dan partisipasi aktif kalian dalam setiap kegiatan</p>
                    </div>
                </div>
                
                <div class="group bg-gradient-to-br from-green-500 via-emerald-500 to-green-600 text-white p-8 rounded-3xl shadow-xl transform hover:-translate-y-3 hover:scale-105 transition-all duration-500 hover:shadow-2xl border border-green-400/30 animate-scale-in" x-show="loaded" x-transition.delay.300ms>
                    <div class="text-center relative">
                        <div class="bg-white/20 rounded-full p-4 w-20 h-20 mx-auto mb-6 group-hover:bg-white/30 transition-all duration-300">
                            <svg class="w-12 h-12 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <h3 class="font-poppins text-xl font-bold mb-3">Pemandu</h3>
                        <p class="font-inter text-sm opacity-95 leading-relaxed">Terima kasih atas bimbingan dan dukungan yang luar biasa kepada para peserta</p>
                    </div>
                </div>
                
                <div class="group bg-gradient-to-br from-teal-500 via-green-500 to-emerald-600 text-white p-8 rounded-3xl shadow-xl transform hover:-translate-y-3 hover:scale-105 transition-all duration-500 hover:shadow-2xl border border-green-400/30 animate-scale-in" x-show="loaded" x-transition.delay.400ms>
                    <div class="text-center relative">
                        <div class="bg-white/20 rounded-full p-4 w-20 h-20 mx-auto mb-6 group-hover:bg-white/30 transition-all duration-300">
                            <svg class="w-12 h-12 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <h3 class="font-poppins text-xl font-bold mb-3">Panitia Mahasiswa</h3>
                        <p class="font-inter text-sm opacity-95 leading-relaxed">Terima kasih atas dedikasi dan kerja keras dalam menyukseskan acara ini</p>
                    </div>
                </div>
                
                <div class="group bg-gradient-to-br from-green-600 via-emerald-500 to-teal-500 text-white p-8 rounded-3xl shadow-xl transform hover:-translate-y-3 hover:scale-105 transition-all duration-500 hover:shadow-2xl border border-green-400/30 animate-scale-in" x-show="loaded" x-transition.delay.500ms>
                    <div class="text-center relative">
                        <div class="bg-white/20 rounded-full p-4 w-20 h-20 mx-auto mb-6 group-hover:bg-white/30 transition-all duration-300">
                            <svg class="w-12 h-12 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm3 5a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1zm0 3a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <h3 class="font-poppins text-xl font-bold mb-3">Civitas Akademika</h3>
                        <p class="font-inter text-sm opacity-95 leading-relaxed">Terima kasih atas dukungan penuh dan fasilitas yang telah disediakan</p>
                    </div>
                </div>
            </div>
            
            <!-- Statistics -->
            <div class="bg-gradient-to-r from-green-50 via-emerald-50 to-teal-50 rounded-3xl p-10 mb-16 border border-green-100 shadow-xl animate-fade-in-up" x-show="loaded" x-transition.delay.600ms>
                <div class="flex items-center justify-center mb-10">
                    <div class="bg-gradient-to-r from-emerald-500 to-green-600 p-3 rounded-2xl mr-4">
                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                        </svg>
                    </div>
                    <h3 class="font-poppins text-3xl md:text-4xl font-bold bg-gradient-to-r from-emerald-700 to-green-700 bg-clip-text text-transparent">Pencapaian Mastamaru 2026</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div class="text-center group" x-data="{ count: 0 }" x-init="setTimeout(() => { let interval = setInterval(() => { count += 50; if(count >= 1090) { count = 1090; clearInterval(interval); } }, 50); }, 1000)">
                        <div class="bg-white rounded-2xl p-8 shadow-lg group-hover:shadow-xl transition-all duration-300 border border-green-100">
                            <div class="font-poppins text-5xl md:text-6xl font-black bg-gradient-to-r from-emerald-600 to-green-600 bg-clip-text text-transparent mb-4" x-text="count + '+'">
                                1090+
                            </div>
                            <div class="font-inter text-gray-700 font-semibold text-lg">Mahasiswa</div>
                        </div>
                    </div>
                    <div class="text-center group" x-data="{ count: 0 }" x-init="setTimeout(() => { let interval = setInterval(() => { count += 5; if(count >= 120) { count = 120; clearInterval(interval); } }, 50); }, 1200)">
                        <div class="bg-white rounded-2xl p-8 shadow-lg group-hover:shadow-xl transition-all duration-300 border border-green-100">
                            <div class="font-poppins text-5xl md:text-6xl font-black bg-gradient-to-r from-green-600 to-teal-600 bg-clip-text text-transparent mb-4" x-text="count + '+'">
                                120+
                            </div>
                            <div class="font-inter text-gray-700 font-semibold text-lg">Panitia</div>
                        </div>
                    </div>
                    <div class="text-center group" x-data="{ count: 0 }" x-init="setTimeout(() => { let interval = setInterval(() => { count += 1; if(count >= 5) { count = 5; clearInterval(interval); } }, 100); }, 1400)">
                        <div class="bg-white rounded-2xl p-8 shadow-lg group-hover:shadow-xl transition-all duration-300 border border-green-100">
                            <div class="font-poppins text-5xl md:text-6xl font-black bg-gradient-to-r from-teal-600 to-emerald-600 bg-clip-text text-transparent mb-4" x-text="count">
                                5
                            </div>
                            <div class="font-inter text-gray-700 font-semibold text-lg">Hari Kegiatan</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <p class="font-inter text-xl md:text-2xl text-gray-700 leading-relaxed max-w-5xl mx-auto font-light animate-fade-in-up" x-show="loaded" x-transition.delay.700ms>
                Semoga pengalaman dan ilmu yang didapat selama Mastamaru 2026 dapat bermanfaat untuk perjalanan akademik kalian selanjutnya. Mari kita lanjutkan semangat kebersamaan ini dalam membangun masa depan yang lebih baik!
                <span class="inline-block animate-float text-green-500 text-2xl ml-3">🌱</span>
            </p>
        </div>
        
        <!-- Footer -->
        <div class="bg-gradient-to-r from-emerald-800 via-green-800 to-teal-800 text-white px-8 py-12 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-transparent via-white/5 to-black/10"></div>
            <div class="relative z-10 text-center">
                <div class="mb-6">
                    <span class="text-3xl animate-bounce">🌿</span>
                    <span class="font-poppins text-xl font-bold mx-4">Mastamaru 2026 - Sukses Terselenggara</span>
                    <span class="text-3xl animate-bounce" style="animation-delay: 0.5s">🌿</span>
                </div>
                <div class="font-inter text-base text-green-100 mb-4 font-medium">
                    Sistem ini dibuat oleh IT Kesekretariatan Mastamaru 2026
                </div>
                <div class="flex items-center justify-center text-sm text-green-200">
                    <div class="bg-white/10 rounded-full p-2 mr-3">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.736 6.979C9.208 6.193 9.696 6 10 6s.792.193 1.264.979c.446.74.736 1.747.736 2.771 0 .579-.185 1.113-.507 1.533-.322.42-.753.717-1.493.717-.74 0-1.171-.297-1.493-.717-.322-.42-.507-.954-.507-1.533 0-1.024.29-2.031.736-2.771z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <span class="font-inter">© Mastamaru 2026 - Universitas Muhammadiyah Ponorogo. All rights reserved.</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>