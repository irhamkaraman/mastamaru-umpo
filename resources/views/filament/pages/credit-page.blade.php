<x-filament-panels::page>
    <div class="fi-page-content space-y-6">
        <!-- Header Card -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-950 dark:text-white">Sistem Presensi MASTAMARU 2026</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Sistem manajemen presensi digital untuk kegiatan MASTAMARU<br>
                            (Masa Ta'aruf Mahasiswa Baru Universitas Muhammadiyah Ponorogo) tahun 2026
                        </p>
                    </div>
                </div>
            </x-slot>
        </x-filament::section>

        <!-- Developer Info Card -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 bg-success-100 dark:bg-success-900/50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <span class="text-lg font-semibold text-gray-950 dark:text-white">Dikembangkan oleh</span>
                </div>
            </x-slot>

            <div class="space-y-4">
                <div class="bg-gray-50 dark:bg-white/5 rounded-xl p-6 border border-gray-200 dark:border-white/10">
                    <div class="text-center space-y-3">
                        <div class="w-16 h-16 bg-primary-600 dark:bg-primary-500 rounded-full flex items-center justify-center mx-auto">
                            <span class="text-white font-bold text-xl">LIK</span>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-primary-600 dark:text-primary-400">Hamba Allah</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-200 mt-1">Mahasiswa Teknik Informatika</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Universitas Muhammadiyah Ponorogo</p>
                        </div>
                        <div class="flex flex-wrap justify-center gap-2 mt-4">
                            <x-filament::badge color="info" size="sm">
                                <x-slot name="icon">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </x-slot>
                                Full Stack Developer
                            </x-filament::badge>
                            <x-filament::badge color="success" size="sm">
                                <x-slot name="icon">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </x-slot>
                                Laravel & Filament Specialist
                            </x-filament::badge>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <!-- Technology Stack Card -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <span class="text-lg font-semibold text-gray-950 dark:text-white">Teknologi yang Digunakan</span>
                </div>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border border-red-200 dark:border-red-700/50 rounded-xl p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 dark:from-red-600 dark:to-red-700 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold text-lg">L</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-red-700 dark:text-red-300">Laravel 11</h4>
                            <p class="text-sm text-red-600 dark:text-red-400">Backend Framework</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 border border-amber-200 dark:border-amber-700/50 rounded-xl p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-amber-600 dark:from-amber-600 dark:to-amber-700 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold text-lg">F</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-amber-700 dark:text-amber-300">Filament 3</h4>
                            <p class="text-sm text-amber-600 dark:text-amber-400">Admin Panel</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border border-blue-200 dark:border-blue-700/50 rounded-xl p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold text-lg">M</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-blue-700 dark:text-blue-300">MySQL</h4>
                            <p class="text-sm text-blue-600 dark:text-blue-400">Database</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border border-green-200 dark:border-green-700/50 rounded-xl p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold text-lg">Q</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-green-700 dark:text-green-300">QR Code</h4>
                            <p class="text-sm text-green-600 dark:text-green-400">Presensi Digital</p>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <!-- Features Card -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 bg-warning-100 dark:bg-warning-900/50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-warning-600 dark:text-warning-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                    <span class="text-lg font-semibold text-gray-950 dark:text-white">Fitur Utama</span>
                </div>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-white/5 rounded-xl border border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                    <div class="w-3 h-3 bg-success-500 rounded-full flex-shrink-0"></div>
                    <span class="text-sm font-medium dark:text-gray-300">Manajemen Sesi Presensi</span>
                </div>
                <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-white/5 rounded-xl border border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                    <div class="w-3 h-3 bg-success-500 rounded-full flex-shrink-0"></div>
                    <span class="text-sm font-medium dark:text-gray-300">QR Code Scanner</span>
                </div>
                <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-white/5 rounded-xl border border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                    <div class="w-3 h-3 bg-success-500 rounded-full flex-shrink-0"></div>
                    <span class="text-sm font-medium dark:text-gray-300">Export Data Excel</span>
                </div>
                <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-white/5 rounded-xl border border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                    <div class="w-3 h-3 bg-success-500 rounded-full flex-shrink-0"></div>
                    <span class="text-sm font-medium dark:text-gray-300">Real-time Monitoring</span>
                </div>
                <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-white/5 rounded-xl border border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                    <div class="w-3 h-3 bg-success-500 rounded-full flex-shrink-0"></div>
                    <span class="text-sm font-medium dark:text-gray-300">Multi-status Presensi</span>
                </div>
                <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-white/5 rounded-xl border border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                    <div class="w-3 h-3 bg-success-500 rounded-full flex-shrink-0"></div>
                    <span class="text-sm font-medium dark:text-gray-300">Dashboard Analytics</span>
                </div>
                <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-white/5 rounded-xl border border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                    <div class="w-3 h-3 bg-success-500 rounded-full flex-shrink-0"></div>
                    <span class="text-sm font-medium dark:text-gray-300">Sinkronisasi API UMPO</span>
                </div>
                <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-white/5 rounded-xl border border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                    <div class="w-3 h-3 bg-success-500 rounded-full flex-shrink-0"></div>
                    <span class="text-sm font-medium dark:text-gray-300">Sertifikat Digital & Generator</span>
                </div>
                <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-white/5 rounded-xl border border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                    <div class="w-3 h-3 bg-success-500 rounded-full flex-shrink-0"></div>
                    <span class="text-sm font-medium dark:text-gray-300">Manajemen Role & Hak Akses</span>
                </div>
            </div>
        </x-filament::section>

        <!-- Footer Card -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center justify-center gap-3">
                    <div class="w-8 h-8 bg-info-100 dark:bg-info-900/50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-info-600 dark:text-info-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="text-lg font-semibold text-gray-950 dark:text-white">Tahun Pengembangan: 2026</span>
                </div>
            </x-slot>

            <div class="text-center gap-4">
                <div class="bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 rounded-xl p-6 border border-primary-200 dark:border-primary-700/50">
                    <div class="gap-3">
                        <div class="flex items-center justify-center gap-2 mb-4">
                            <div class="w-6 h-6 bg-primary-500 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-xs">©</span>
                            </div>
                            <p class="text-lg font-bold text-primary-700 dark:text-primary-300">
                                2026 - Sistem Presensi MASTAMARU
                            </p>
                        </div>

                        <div class="border-t border-primary-200 dark:border-primary-700/50 pt-3">
                            <p class="text-sm text-primary-600 dark:text-primary-400 font-medium">
                                Universitas Muhammadiyah Ponorogo
                            </p>
                        </div>

                        <div class="flex items-center justify-center gap-2 text-sm text-primary-500 dark:text-primary-400">
                            <span>Dibuat dengan</span>
                            <svg class="w-5 h-5 text-red-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                            </svg>
                            <span>untuk kemajuan pendidikan</span>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
