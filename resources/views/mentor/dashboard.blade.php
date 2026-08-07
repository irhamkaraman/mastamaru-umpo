@extends('layouts.mentor')

@section('title', 'Dashboard Pemandu')

@section('content')
    <div class="mentor-dashboard-page min-h-screen">
        <div class="mentor-dashboard-container container mx-auto px-4 py-6 sm:py-8 max-w-md md:max-w-4xl lg:max-w-6xl">
            <!-- Header dengan Gradient -->
            <div class="mentor-dashboard-hero rounded-3xl shadow-2xl p-6 sm:p-8 mb-8 relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-0 left-0 w-40 h-40 bg-white rounded-full -translate-x-20 -translate-y-20"></div>
                    <div class="absolute bottom-0 right-0 w-32 h-32 bg-white rounded-full translate-x-16 translate-y-16"></div>
                    <div class="absolute top-1/2 right-1/4 w-24 h-24 bg-white rounded-full"></div>
                </div>

                <div class="mentor-hero-grid relative z-10">
                    <div class="mentor-hero-main">
                        <div class="flex items-center mb-5">
                            <div class="bg-white bg-opacity-20 p-3 rounded-2xl mr-4">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-3xl md:text-4xl font-bold text-white mb-1">Dashboard</h1>
                                <p class="text-blue-100 text-lg">Pemandu Mastamaru 2026</p>
                            </div>
                        </div>
                        <div class="mentor-welcome-card bg-white bg-opacity-20 rounded-2xl p-4 backdrop-blur-sm">
                            <p class="text-white text-lg font-medium">Selamat datang kembali,</p>
                            <p class="text-2xl font-bold text-white">{{ $mentor->name }}! 👋</p>
                        </div>
                    </div>
                    <div class="mentor-hero-side">
                        <div class="mentor-id-chip bg-white bg-opacity-20 rounded-2xl p-4 text-center backdrop-blur-sm">
                            <p class="text-blue-100 text-sm font-medium">NIM Anda</p>
                            <p class="text-white text-xl font-bold">{{ $mentor->student_id }}</p>
                        </div>
                        <form action="{{ route('mentor.logout') }}" method="POST" class="inline mentor-logout-form" id="logout-form">
                            @csrf
                            <button type="button" onclick="confirmLogout()"
                                class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-6 py-3 rounded-2xl transition-all duration-300 font-medium backdrop-blur-sm border border-white border-opacity-30 hover:scale-105 transform">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        <!-- Notifikasi dengan SweetAlert2 -->
        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: '{{ session('success') }}',
                        confirmButtonColor: '#3b82f6',
                        timer: 3000,
                        timerProgressBar: true
                    });
                });
            </script>
        @endif

        @if (session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: '{{ session('error') }}',
                        confirmButtonColor: '#3b82f6'
                    });
                });
            </script>
        @endif

            <!-- Daftar Sesi Presensi -->
            <div class="mentor-sessions-panel bg-white rounded-3xl shadow-2xl p-6 md:p-8 mb-8 border border-gray-100">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 space-y-4 md:space-y-0">
                    <div class="flex items-center">
                        <div class="bg-gradient-to-r from-green-400 to-blue-500 p-3 rounded-2xl mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl md:text-2xl font-bold text-gray-800">Sesi Presensi Aktif</h3>
                            <p class="text-gray-600">Kelola kehadiran peserta kelompok Anda</p>
                        </div>
                    </div>
                     <div class="mentor-live-pill flex items-center bg-green-50 px-4 py-2 rounded-full border border-green-200">
                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse mr-2"></div>
                        <span class="text-sm font-medium text-green-700">Live Session</span>
                    </div>
                </div>

                @if ($activeSessions->count() > 0)
                    <div class="space-y-6">
                        @foreach ($activeSessions as $session)
                             <div class="mentor-session-card bg-gradient-to-r from-white to-gray-50 border border-gray-200 rounded-2xl p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
                                <!-- Decorative Elements -->
                                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full -translate-y-16 translate-x-16 opacity-50"></div>
                                <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-green-100 to-blue-100 rounded-full translate-y-12 -translate-x-12 opacity-50"></div>

                                <!-- Mobile Layout -->
                                <div class="block md:hidden relative z-10">
                                    <!-- Header -->
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center mb-2">
                                                <div class="bg-gradient-to-r from-blue-500 to-purple-500 p-2 rounded-lg mr-3">
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                                <h4 class="font-bold text-gray-800 text-lg truncate">
                                                    {{ $session->session_name }}</h4>
                                            </div>
                                            <p class="text-sm text-gray-600 line-clamp-2 ml-11">{{ $session->description }}</p>
                                        </div>
                                        <div class="ml-3 flex-shrink-0">
                                            <span class="session-status-badge" data-start="{{ $session->start_time }}"
                                                data-end="{{ $session->end_time }}">
                                                <span
                                                    class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-medium shadow-sm">Loading...</span>
                                            </span>
                                        </div>
                                    </div>

                                <!-- Session Info -->
                                <div class="grid grid-cols-2 gap-4 mb-6">
                                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-100">
                                        <div class="flex items-center mb-2">
                                            <svg class="w-4 h-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div class="text-xs text-blue-600 font-medium">Waktu Sesi</div>
                                        </div>
                                        <div class="text-sm font-bold text-gray-800">
                                            {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                        </div>
                                    </div>
                                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-4 border border-green-100">
                                        <div class="flex items-center mb-2">
                                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                            </svg>
                                            <div class="text-xs text-green-600 font-medium">Kehadiran</div>
                                        </div>
                                        @php
                                            $totalSubmissions = $session
                                                ->attendanceSubmissions()
                                                ->where('group_id', $mentor->group_id)
                                                ->count();
                                            $totalStudents = $mentor->attendances->count();
                                        @endphp
                                        <div class="text-sm font-bold text-gray-800">
                                            {{ $totalSubmissions }}/{{ $totalStudents }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Info -->
                                <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4 mb-6 border border-purple-100">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-xs text-purple-600 font-medium mb-1">Status Sesi</div>
                                            <span class="session-status-badge" data-start="{{ $session->start_time }}"
                                                data-end="{{ $session->end_time }}">
                                                <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-medium shadow-sm">Loading...</span>
                                            </span>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs text-purple-600 font-medium mb-1">Waktu Saat Ini</div>
                                            <div class="text-purple-700 font-bold text-sm current-time-display">Loading...</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Button -->
                                <a href="{{ route('mentor.presence.detail', $session->slug) }}"
                                    class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl text-sm font-bold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                        </path>
                                    </svg>
                                    <span>Kelola Presensi</span>
                                </a>
                            </div>

                            <!-- Tablet & Desktop Layout -->
                            <div class="hidden md:block relative z-10">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-4">
                                            <div class="bg-gradient-to-r from-blue-500 to-purple-500 p-3 rounded-xl">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-800 text-xl">{{ $session->session_name }}</h4>
                                                <p class="text-gray-600 text-sm mt-1">{{ $session->description }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-4 lg:space-x-6">
                                        <!-- Session Info -->
                                        <div class="flex items-center space-x-3 lg:space-x-6">
                                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-3 lg:p-4 border border-blue-100 text-center min-w-[80px] lg:min-w-[100px]">
                                                <div class="flex items-center justify-center mb-2">
                                                    <svg class="w-4 h-4 text-blue-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <div class="text-blue-600 text-xs font-medium">Waktu</div>
                                                </div>
                                                <div class="font-bold text-gray-800 text-sm">
                                                    {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                                </div>
                                            </div>
                                            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-3 lg:p-4 border border-green-100 text-center min-w-[80px] lg:min-w-[100px]">
                                                <div class="flex items-center justify-center mb-2">
                                                    <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                                    </svg>
                                                    <div class="text-green-600 text-xs font-medium">Kehadiran</div>
                                                </div>
                                                @php
                                                    $totalSubmissions = $session
                                                        ->attendanceSubmissions()
                                                        ->where('group_id', $mentor->group_id)
                                                        ->count();
                                                    $totalStudents = $mentor->attendances->count();
                                                @endphp
                                                <div class="font-bold text-gray-800 text-sm">
                                                    {{ $totalSubmissions }}/{{ $totalStudents }}</div>
                                            </div>
                                        </div>

                                        <!-- Status -->
                                        <div class="text-center">
                                            <span class="session-status-badge" data-start="{{ $session->start_time }}"
                                                data-end="{{ $session->end_time }}">
                                                <span
                                                    class="bg-gray-100 text-gray-800 px-4 py-2 rounded-full text-sm font-medium shadow-sm">Loading...</span>
                                            </span>
                                        </div>

                                        <!-- Action Button -->
                                        <div class="flex-shrink-0">
                                            <a href="{{ route('mentor.presence.detail', $session->slug) }}"
                                                class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 lg:px-6 py-2 lg:py-3 rounded-xl text-sm font-bold hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center space-x-2 whitespace-nowrap">
                                                <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                                </svg>
                                                <span class="hidden lg:inline">Kelola Presensi</span>
                                                <span class="lg:hidden">Kelola</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @else
                    <div class="text-center py-12 relative">
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 rounded-2xl opacity-50"></div>
                        <div class="absolute top-4 right-4 w-32 h-32 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full opacity-30"></div>
                        <div class="absolute bottom-4 left-4 w-24 h-24 bg-gradient-to-tr from-green-100 to-blue-100 rounded-full opacity-30"></div>

                        <div class="relative z-10">
                            <div class="mx-auto w-32 h-32 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6 shadow-lg">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-3">Tidak Ada Sesi Aktif</h3>
                            <p class="text-gray-600 max-w-md md:max-w-lg mx-auto text-lg leading-relaxed">Saat ini tidak ada sesi presensi yang sedang berlangsung untuk kelompok Anda. Silakan tunggu hingga sesi berikutnya dimulai.</p>

                            <!-- Decorative Elements -->
                            <div class="flex justify-center mt-8 space-x-2">
                                <div class="w-3 h-3 bg-blue-300 rounded-full animate-pulse"></div>
                                <div class="w-3 h-3 bg-purple-300 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                                <div class="w-3 h-3 bg-pink-300 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
                            </div>
                        </div>
                    </div>
                @endif
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
            <!-- Info Kelompok -->
             <div class="mentor-info-card bg-white rounded-xl shadow-lg p-4 md:p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 p-2 md:p-3 rounded-full flex-shrink-0">
                        <svg class="h-6 w-6 md:h-8 md:w-8 text-green-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3 md:ml-4 min-w-0 flex-1">
                        <h3 class="text-base md:text-lg font-semibold text-gray-800">Kelompok</h3>
                        @if ($mentor->group)
                            <p class="text-gray-600 truncate">{{ $mentor->group->name }}</p>
                            <p class="text-sm text-gray-500">Urutan: {{ $mentor->group->order }}</p>
                        @else
                            <p class="text-gray-500">Belum ada kelompok</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistik -->
             <div class="mentor-info-card bg-white rounded-xl shadow-lg p-4 md:p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-2 md:p-3 rounded-full flex-shrink-0">
                        <svg class="h-6 w-6 md:h-8 md:w-8 text-purple-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3 md:ml-4 min-w-0 flex-1">
                        <h3 class="text-base md:text-lg font-semibold text-gray-800">Total Peserta</h3>
                        <p class="text-xl md:text-2xl font-bold text-purple-600">{{ $mentor->attendances->count() }}</p>
                        <p class="text-sm text-gray-500">Peserta terdaftar</p>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Card (Mobile Only) -->
            <div
                 class="mentor-today-card bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl shadow-lg p-4 md:p-6 text-white md:col-span-2 lg:col-span-1">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base md:text-lg font-semibold">Status Hari Ini</h3>
                        <p class="text-sm opacity-90 mt-1">Ringkasan aktivitas</p>
                    </div>
                    <div class="bg-white bg-opacity-20 p-2 md:p-3 rounded-full">
                        <svg class="h-6 w-6 md:h-8 md:w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 md:mt-4">
                    <p class="text-xl md:text-2xl font-bold">{{ $todayStats }}</p>
                    <p class="text-sm opacity-90">Sesi aktif hari ini</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gradient-to-r from-white to-gray-50 rounded-3xl shadow-2xl p-6 md:p-8 text-center border border-gray-100 relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full -translate-y-20 translate-x-20 opacity-30"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-green-100 to-blue-100 rounded-full translate-y-16 -translate-x-16 opacity-30"></div>

            <div class="relative z-10">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                    <div class="flex items-center justify-center md:justify-start">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-500 p-3 rounded-2xl mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div class="text-left">
                            <div class="text-lg font-bold text-gray-800">Sistem Presensi Mastamaru 2026</div>
                            <div class="text-sm text-gray-600">Dashboard Pemandu - Kelola Kehadiran Peserta</div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-4 py-3 rounded-xl border border-purple-100">
                        <div class="text-xs text-purple-600 font-medium mb-1">Waktu Saat Ini</div>
                        <div class="text-sm font-bold text-purple-700 current-time-display">Loading...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .mentor-dashboard-page { color:#29344d; }
        .mentor-dashboard-container { position:relative; }
        .mentor-dashboard-hero { min-height:250px; display:flex; align-items:center; }
        .mentor-hero-grid { display:grid; grid-template-columns:minmax(0,1fr) 230px; gap:32px; align-items:center; width:100%; }
        .mentor-hero-main { min-width:0; }
        .mentor-hero-side { display:grid; gap:12px; align-content:center; }
        .mentor-logout-form,.mentor-logout-form button { width:100%; }
        .mentor-logout-form button { display:flex; align-items:center; justify-content:center; }
        .mentor-welcome-card { max-width:520px; }
        .mentor-dashboard-hero::after { content:''; position:absolute; right:8%; bottom:-85px; width:220px; height:220px; border-radius:999px; background:rgba(255,255,255,.34); border:22px solid rgba(255,255,255,.24); pointer-events:none; }
        .mentor-dashboard-hero h1 { letter-spacing:-.06em; }
        .mentor-dashboard-hero .bg-white.bg-opacity-20 { border:1px solid rgba(255,255,255,.72); box-shadow:0 12px 28px rgba(75,58,146,.08); }
        .mentor-id-chip { min-width:128px; }
        .mentor-live-pill { box-shadow:0 7px 16px rgba(34,197,94,.08); }
        .mentor-sessions-panel { box-shadow:0 20px 60px rgba(75,58,146,.09); }
        .mentor-sessions-panel > .flex { padding-bottom:20px; border-bottom:1px solid #eef1f6; }
        .mentor-sessions-panel > .flex h3 { letter-spacing:-.04em; }
        .mentor-session-card { background:rgba(255,255,255,.84); box-shadow:0 11px 28px rgba(75,58,146,.06); }
        .mentor-session-card:hover { border-color:#cbd9ef; box-shadow:0 18px 38px rgba(75,58,146,.12); }
        .mentor-session-card .bg-gradient-to-br.from-blue-50.to-indigo-50 { background:#eff7ff; border-color:#dbeeff; }
        .mentor-session-card .bg-gradient-to-br.from-green-50.to-emerald-50 { background:#effbf6; border-color:#d8f2e7; }
        .mentor-session-card .bg-gradient-to-r.from-purple-50.to-pink-50 { background:#f5f2ff; border-color:#e8e2ff; }
        .mentor-session-card .session-status-badge span { font-weight:700; }
        .mentor-session-card a { letter-spacing:.01em; }
        .mentor-info-card { border:1px solid rgba(255,255,255,.92); box-shadow:0 14px 32px rgba(75,58,146,.07); transition:transform .2s,box-shadow .2s; }
        .mentor-info-card:hover { transform:translateY(-3px); box-shadow:0 19px 38px rgba(75,58,146,.11); }
        .mentor-today-card { box-shadow:0 14px 32px rgba(105,98,170,.16); }
        .mentor-dashboard-container .text-green-600 { color:#159a67; }
        .mentor-dashboard-container .text-purple-600 { color:#7565b1; }
        .mentor-dashboard-container > .bg-gradient-to-r { border:1px solid rgba(255,255,255,.9); }
        .mentor-dashboard-page { position:relative; isolation:isolate; }
        .mentor-dashboard-page::before,.mentor-dashboard-page::after { content:''; position:fixed; border-radius:999px; pointer-events:none; z-index:-1; }
        .mentor-dashboard-page::before { width:360px; height:360px; top:120px; right:-210px; background:rgba(186,230,253,.28); }
        .mentor-dashboard-page::after { width:280px; height:280px; bottom:120px; left:-170px; background:rgba(221,214,254,.28); }
        .mentor-dashboard-container { width:calc(100% - 32px); max-width:1180px; }
        .mentor-dashboard-hero { background:linear-gradient(135deg,#eaf6ff 0%,#f1edff 58%,#effbf5 100%); border:1px solid rgba(155,173,204,.2); color:#25204b; }
        .mentor-dashboard-hero .text-white { color:#25204b!important; }
        .mentor-dashboard-hero .text-blue-100,.mentor-dashboard-hero .text-blue-200 { color:#68708a!important; }
        .mentor-dashboard-hero .bg-white.bg-opacity-20 { background:rgba(255,255,255,.62); }
        .mentor-dashboard-hero .border-white.border-opacity-30 { border-color:rgba(255,255,255,.85); }
        .mentor-dashboard-hero .hover\:bg-opacity-30:hover { background:rgba(255,255,255,.84); }
        .mentor-dashboard-hero .bg-white.bg-opacity-20 svg { color:#5a8aa6; }
        .mentor-sessions-panel,.mentor-info-card { background:rgba(255,255,255,.82); border-color:rgba(255,255,255,.9); backdrop-filter:blur(12px); }
        .mentor-session-card { border-color:rgba(148,163,184,.18); }
        .mentor-session-card a { background:linear-gradient(135deg,#5a8aa6,#6873b5); }
        .mentor-session-card a:hover { background:linear-gradient(135deg,#4f7d98,#5b63a5); }
        .mentor-today-card { background:linear-gradient(135deg,#769fb5,#8681b9); }
        .mentor-dashboard-container .bg-gradient-to-r.from-green-400.to-blue-500 { background:linear-gradient(135deg,#9bd6c4,#81b7d0); }
        .mentor-dashboard-container .bg-gradient-to-r.from-blue-500.to-purple-500 { background:linear-gradient(135deg,#81b7d0,#938bc8); }
        @media(max-width:900px) { .mentor-hero-grid{grid-template-columns:minmax(0,1fr) 190px;gap:20px}.mentor-dashboard-hero h1{font-size:2.5rem}.mentor-dashboard-hero .text-2xl{font-size:1.45rem} }
        @media(max-width:640px) { .mentor-dashboard-container{width:calc(100% - 24px)}.mentor-dashboard-hero{border-radius:1.65rem;padding:20px!important;min-height:0}.mentor-hero-grid{display:block}.mentor-hero-main{margin-bottom:18px}.mentor-dashboard-hero h1{font-size:2rem}.mentor-dashboard-hero .text-2xl{font-size:1.35rem}.mentor-dashboard-hero::after{width:130px;height:130px;right:-65px;bottom:-55px;border-width:13px}.mentor-sessions-panel{padding:18px!important;border-radius:1.65rem}.mentor-session-card{padding:16px!important}.mentor-info-card,.mentor-today-card{border-radius:1.2rem}.mentor-dashboard-container .grid-cols-2{gap:10px}.mentor-dashboard-container .grid-cols-2>div{padding:12px}.mentor-dashboard-hero .mentor-id-chip{min-width:0;width:100%}.mentor-dashboard-hero form{width:100%}.mentor-dashboard-hero form button{width:100%}.mentor-sessions-panel > .flex{padding-bottom:16px} }
        @media(min-width:641px) and (max-width:1023px) { .mentor-dashboard-container{max-width:760px}.mentor-session-card{padding:18px}.mentor-dashboard-hero{padding:24px!important} }
    </style>

    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'Konfirmasi Logout',
                text: 'Apakah Anda yakin ingin keluar dari sistem?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }

        function updateCurrentTime() {
            const now = new Date();
            const timeString = now.toLocaleString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            }).replace(/\//g, '/').replace(',', '');

            // Update all current time displays
            document.querySelectorAll('.current-time-display').forEach(element => {
                element.textContent = timeString;
            });

            // Update session status badges
            document.querySelectorAll('.session-status-badge').forEach(badge => {
                const startTime = new Date(badge.dataset.start);
                const endTime = new Date(badge.dataset.end);
                const statusSpan = badge.querySelector('span');

                if (now < startTime) {
                    statusSpan.className =
                        'bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium';
                    statusSpan.textContent = 'Belum Dimulai';
                } else if (now > endTime) {
                    statusSpan.className = 'bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium';
                    statusSpan.textContent = 'Berakhir';
                } else {
                    statusSpan.className = 'bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium';
                    statusSpan.textContent = 'Aktif';
                }
            });
        }

        // Update time immediately and then every second
        updateCurrentTime();
        setInterval(updateCurrentTime, 1000);
    </script>
@endsection
