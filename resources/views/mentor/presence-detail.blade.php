@extends('layouts.mentor')

@section('title', 'Detail Presensi - ' . $session->session_name)

@section('content')
    <div class="container mx-auto px-4 py-4 sm:py-8 max-w-md lg:max-w-6xl">
        <!-- Header Card -->
        <div
            class="relative bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 rounded-3xl shadow-2xl p-8 mb-8 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-black bg-opacity-10"></div>
            <div class="absolute top-0 right-0 w-64 h-64 bg-white bg-opacity-10 rounded-full -translate-y-32 translate-x-32">
            </div>
            <div
                class="absolute bottom-0 left-0 w-48 h-48 bg-white bg-opacity-5 rounded-full translate-y-24 -translate-x-24">
            </div>

            <div class="relative z-10">
                <div class="lg:flex lg:items-center lg:justify-between">
                    <div class="text-center lg:text-left mb-6 lg:mb-0">
                        <h1 class="text-3xl lg:text-4xl font-bold text-white mb-3 leading-tight">{{ $session->session_name }}
                        </h1>
                        <p class="text-indigo-100 text-base lg:text-lg mb-6 leading-relaxed">{{ $session->description }}</p>

                        <div class="flex flex-col lg:flex-row lg:items-center lg:space-x-8 space-y-4 lg:space-y-0">
                            <div
                                class="bg-white bg-opacity-20 backdrop-blur-sm rounded-xl p-4 border border-white border-opacity-30">
                                <div class="text-sm text-indigo-100 mb-2 font-medium">Kode Sesi:</div>
                                <div class="text-xl font-bold text-white tracking-wider">{{ $session->session_code }}</div>
                            </div>

                            <div class="text-sm lg:text-base text-indigo-100">
                                <div class="font-semibold text-white mb-1">Waktu Sesi:</div>
                                <div class="bg-white bg-opacity-10 rounded-lg px-3 py-2 backdrop-blur-sm">
                                    {{ \Carbon\Carbon::parse($session->start_time)->format('d/m/Y H:i') }} -
                                    {{ \Carbon\Carbon::parse($session->end_time)->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center lg:text-right">
                        <div class="text-sm lg:text-base font-medium mb-6 bg-white bg-opacity-15 backdrop-blur-sm rounded-xl p-4"
                            id="current-time">
                            <span class="text-yellow-200 font-semibold block mb-1">Waktu Saat Ini:</span>
                            <span class="text-white text-lg font-bold" id="time-display">Loading...</span>
                        </div>

                        <a href="{{ route('mentor.dashboard') }}"
                            class="inline-flex items-center bg-white bg-opacity-20 hover:bg-opacity-30 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 backdrop-blur-sm border border-white border-opacity-30 hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-3 gap-4 lg:gap-6 mb-8">
            <!-- Present Card -->
            <div
                class="group relative bg-gradient-to-br from-emerald-500 to-green-600 rounded-2xl p-6 text-center shadow-xl hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300 overflow-hidden">
                <div
                    class="absolute inset-0 bg-white bg-opacity-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                </div>
                <div class="relative z-10">
                    <div
                        class="w-16 h-16 lg:w-20 lg:h-20 bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 lg:w-10 lg:h-10 text-white" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <div class="text-sm lg:text-base text-emerald-100 mb-2 font-medium">Telah Hadir</div>
                    <div class="text-3xl lg:text-4xl font-bold text-white mb-1" id="present-count">{{ $totalPresent }}</div>
                    <div class="text-xs text-emerald-200">Peserta</div>
                </div>
                <div
                    class="absolute top-0 right-0 w-24 h-24 bg-white bg-opacity-5 rounded-full -translate-y-12 translate-x-12">
                </div>
            </div>

            <!-- Absent Card -->
            <div
                class="group relative bg-gradient-to-br from-rose-500 to-red-600 rounded-2xl p-6 text-center shadow-xl hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300 overflow-hidden">
                <div
                    class="absolute inset-0 bg-white bg-opacity-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                </div>
                <div class="relative z-10">
                    <div
                        class="w-16 h-16 lg:w-20 lg:h-20 bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 lg:w-10 lg:h-10 text-white" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="text-sm lg:text-base text-rose-100 mb-2 font-medium">Belum Hadir</div>
                    <div class="text-3xl lg:text-4xl font-bold text-white mb-1" id="absent-count">{{ $totalAbsent }}</div>
                    <div class="text-xs text-rose-200">Peserta</div>
                </div>
                <div
                    class="absolute top-0 right-0 w-24 h-24 bg-white bg-opacity-5 rounded-full -translate-y-12 translate-x-12">
                </div>
            </div>

            <!-- Total Card -->
            <div
                class="group relative bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-6 text-center shadow-xl hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300 overflow-hidden md:col-span-1">
                <div
                    class="absolute inset-0 bg-white bg-opacity-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                </div>
                <div class="relative z-10">
                    <div
                        class="w-16 h-16 lg:w-20 lg:h-20 bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 lg:w-10 lg:h-10 text-white" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="text-sm lg:text-base text-blue-100 mb-2 font-medium">Total Peserta</div>
                    <div class="text-3xl lg:text-4xl font-bold text-white mb-1" id="total-count">{{ $totalStudents }}</div>
                    <div class="text-xs text-blue-200">Terdaftar</div>
                </div>
                <div
                    class="absolute top-0 right-0 w-24 h-24 bg-white bg-opacity-5 rounded-full -translate-y-12 translate-x-12">
                </div>
            </div>
        </div>

        <!-- QR Scanner and Manual Input Section -->
        <div class="grid grid-cols-1 gap-6 lg:gap-8 mb-8">
            <!-- QR Scanner Section -->
            <div
                class="relative bg-white bg-opacity-80 backdrop-blur-xl rounded-3xl shadow-2xl p-8 border border-white border-opacity-20 overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-indigo-50 opacity-50"></div>
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-purple-200 bg-opacity-30 rounded-full -translate-y-16 translate-x-16">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-24 h-24 bg-indigo-200 bg-opacity-20 rounded-full translate-y-12 -translate-x-12">
                </div>

                <div class="relative z-10">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                                </path>
                            </svg>
                        </div>
                        <h3
                            class="text-xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent">
                            Scan QR Code</h3>
                    </div>

                    <div class="relative">
                        <video id="qr-video"
                            class="w-full h-72 lg:h-80 bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl object-cover shadow-inner"
                            style="display: none;"></video>
                        <canvas id="qr-canvas"
                            class="w-full h-72 lg:h-80 bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl shadow-inner"
                            style="display: none;"></canvas>

                        <!-- Enhanced Scan Area Overlay -->
                        <div id="scan-overlay" class="absolute inset-0 pointer-events-none rounded-2xl"
                            style="display: none;">
                            <!-- Animated scanning area -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-3/4 h-3/4 relative">
                                    <!-- Modern corner brackets -->
                                    <div
                                        class="absolute top-0 left-0 w-12 h-12 border-l-4 border-t-4 border-emerald-400 rounded-tl-2xl shadow-lg animate-pulse">
                                    </div>
                                    <div
                                        class="absolute top-0 right-0 w-12 h-12 border-r-4 border-t-4 border-emerald-400 rounded-tr-2xl shadow-lg animate-pulse">
                                    </div>
                                    <div
                                        class="absolute bottom-0 left-0 w-12 h-12 border-l-4 border-b-4 border-emerald-400 rounded-bl-2xl shadow-lg animate-pulse">
                                    </div>
                                    <div
                                        class="absolute bottom-0 right-0 w-12 h-12 border-r-4 border-b-4 border-emerald-400 rounded-br-2xl shadow-lg animate-pulse">
                                    </div>

                                    <!-- Glowing border -->
                                    <div
                                        class="absolute inset-0 border-2 border-emerald-300 rounded-2xl opacity-80 shadow-lg animate-pulse">
                                    </div>
                                    <div class="absolute inset-0 border border-emerald-400 rounded-2xl animate-ping"></div>

                                    <!-- Enhanced scanning animation -->
                                    <div
                                        class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-emerald-400 to-transparent animate-pulse shadow-lg">
                                    </div>
                                    <div class="absolute inset-x-0 top-8 h-0.5 bg-emerald-400 opacity-60 animate-bounce shadow-sm"
                                        style="animation-delay: 0.5s;"></div>
                                    <div class="absolute inset-x-0 top-16 h-0.5 bg-emerald-300 opacity-40 animate-bounce shadow-sm"
                                        style="animation-delay: 1s;"></div>
                                </div>
                            </div>

                            <!-- Enhanced shadow effect -->
                            <div class="absolute inset-0 bg-black bg-opacity-30 rounded-2xl backdrop-blur-sm"></div>

                            <!-- Modern active indicator -->
                            <div class="absolute top-6 right-6 flex items-center space-x-3">
                                <div class="w-4 h-4 bg-red-500 rounded-full animate-pulse shadow-lg"></div>
                                <span
                                    class="text-white text-sm font-bold bg-gradient-to-r from-red-500 to-pink-500 px-3 py-1 rounded-full shadow-lg backdrop-blur-sm">LIVE</span>
                            </div>
                        </div>

                        <div id="qr-placeholder"
                            class="w-full h-72 lg:h-80 bg-gradient-to-br from-purple-50 via-indigo-50 to-blue-50 rounded-2xl flex flex-col items-center justify-center border-2 border-dashed border-purple-200 hover:border-purple-300 transition-colors duration-300 shadow-inner">
                            <div
                                class="w-20 h-20 lg:w-24 lg:h-24 bg-gradient-to-br from-purple-400 to-indigo-500 rounded-2xl flex items-center justify-center mb-6 shadow-lg animate-pulse">
                                <svg class="w-10 h-10 lg:w-12 lg:h-12 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                                    </path>
                                </svg>
                            </div>
                            <p class="text-purple-600 text-center text-base lg:text-lg font-medium">Klik tombol untuk
                                memulai scan</p>
                            <p class="text-purple-400 text-center text-sm mt-2">Pastikan QR code terlihat jelas</p>
                        </div>
                    </div>

                    <form id="qr-scan-form" action="{{ route('mentor.presence.scan', $slug) }}" method="POST"
                        style="display: none;">
                        @csrf
                        <input type="hidden" id="scanned-code" name="code">
                        <input type="hidden" id="device-timestamp" name="device_timestamp">
                    </form>

                    <div class="mt-8 space-y-4">
                        <button id="start-scan"
                            class="w-full py-4 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-medium px-4 rounded-xl transition-all duration-300 transform hover:scale-102 hover:shadow-lg flex items-center justify-center space-x-2">
                            <span>Mulai Scan QR Code</span>
                        </button>

                        <button id="stop-scan"
                            class="w-full py-4 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white font-medium px-4 rounded-xl transition-all duration-300 transform hover:scale-102 hover:shadow-lg flex items-center justify-center space-x-2"
                            style="display: none;">
                            <span>Stop Scanning</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Manual Input Section -->
            <div
                class="relative bg-white bg-opacity-80 backdrop-blur-xl rounded-3xl shadow-2xl p-8 border border-white border-opacity-20 overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-cyan-50 opacity-50"></div>
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-blue-200 bg-opacity-30 rounded-full -translate-y-16 translate-x-16">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-24 h-24 bg-cyan-200 bg-opacity-20 rounded-full translate-y-12 -translate-x-12">
                </div>

                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </div>
                            <h3
                                class="text-xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">
                                Input Manual</h3>
                        </div>

                        <!-- Info Icon untuk Mobile -->
                        <button type="button" id="info-btn-mobile"
                            class="lg:hidden p-3 bg-blue-100 bg-opacity-50 backdrop-blur-sm text-blue-600 hover:bg-blue-200 hover:bg-opacity-70 rounded-xl transition-all duration-300 transform hover:scale-110 z-50 relative">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>

                    <form id="manual-form" action="{{ route('mentor.presence.manual', $slug) }}" method="POST"
                        class="space-y-6">
                        @csrf
                        <input type="hidden" id="manual-device-timestamp" name="device_timestamp">
                        <div class="relative">
                            <label for="manual-code" class="block text-sm lg:text-base font-semibold text-gray-700 mb-3">
                                Kode Unik (8 karakter)
                            </label>
                            <div class="relative">
                                <input type="text" id="manual-code" name="manual_code" maxlength="8"
                                    class="w-full px-5 py-4 lg:py-5 bg-white bg-opacity-70 backdrop-blur-sm border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-400 transition-all duration-300 text-center text-lg lg:text-xl font-mono tracking-wider text-gray-800 placeholder-gray-500 shadow-lg"
                                    placeholder="Masukkan kode" required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-500 to-cyan-600 hover:from-blue-600 hover:to-cyan-700 text-white font-semibold py-4 lg:py-5 px-6 rounded-xl transition-all duration-300 shadow-xl transform hover:scale-105 hover:shadow-2xl flex items-center justify-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Submit Presensi
                        </button>
                    </form>

                    <!-- Tips untuk Desktop -->
                    <div class="hidden lg:block mt-6 p-4 bg-blue-50 rounded-lg">
                        <h4 class="text-sm font-medium text-blue-800 mb-2">💡 Cara Mendapatkan Kode Unik:</h4>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li>• Akses halaman utama aplikasi presensi</li>
                            <li>• Masukkan NIM peserta pada form yang tersedia</li>
                            <li>• Kode unik akan ditampilkan dan selalu berubah setiap saat</li>
                            <li>• Gunakan QR Scanner untuk proses yang lebih cepat</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Modal Info untuk Mobile -->
            <div id="info-modal-mobile" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-[99999] hidden"
                style="position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 99999 !important;">
                <div class="flex items-center justify-center min-h-screen p-4" style="min-height: 100vh !important;">
                    <div class="bg-white rounded-lg max-w-sm w-full p-6 relative shadow-2xl"
                        style="max-height: 90vh; overflow-y: auto; z-index: 100000 !important;">
                        <button type="button" id="close-modal-btn"
                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>

                        <div class="mb-4">
                            <h4 class="text-lg font-semibold text-blue-800 mb-3 flex items-center">
                                <span class="mr-2">💡</span>
                                Cara Mendapatkan Kode Unik
                            </h4>
                            <ul class="text-sm text-gray-700 space-y-2">
                                <li class="flex items-start">
                                    <span class="text-blue-600 mr-2 mt-1">•</span>
                                    <span>Akses halaman utama Sistem Presensi</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-blue-600 mr-2 mt-1">•</span>
                                    <span>Masukkan NIM peserta pada form yang tersedia</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-blue-600 mr-2 mt-1">•</span>
                                    <span>Kode unik akan ditampilkan dan selalu berubah setiap saat</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-blue-600 mr-2 mt-1">•</span>
                                    <span>Gunakan QR Scanner untuk proses yang lebih cepat</span>
                                </li>
                            </ul>
                        </div>

                        <button type="button" id="close-modal-btn-2"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                            Mengerti
                        </button>
                    </div>
                </div>
            </div>

            <!-- Message Display menggunakan SweetAlert2 -->
            @if (session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: '{{ session('success') }}',
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                    });
                </script>
            @endif

            @if (session('warning'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan!',
                            text: '{{ session('warning') }}',
                            timer: 4000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                    });
                </script>
            @endif

            @if (session('error'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: '{{ session('error') }}',
                            timer: 5000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                    });
                </script>
            @endif

            <!-- Attendance Lists -->
            <div
                class="relative bg-white bg-opacity-80 backdrop-blur-xl rounded-3xl shadow-2xl p-8 lg:p-10 border border-white border-opacity-20 overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-pink-50 opacity-50"></div>
                <div
                    class="absolute top-0 right-0 w-40 h-40 bg-purple-200 bg-opacity-30 rounded-full -translate-y-20 translate-x-20">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-32 h-32 bg-pink-200 bg-opacity-20 rounded-full translate-y-16 -translate-x-16">
                </div>

                <div class="relative z-10">
                    <div class="flex items-center mb-8">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <h3
                            class="text-xl lg:text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                            Daftar Peserta</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <!-- Present Students -->
                        <div
                            class="bg-white bg-opacity-60 backdrop-blur-sm rounded-2xl p-6 border border-green-200 border-opacity-50">
                            <h4 class="text-md lg:text-lg font-semibold text-green-700 mb-6 flex items-center">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-green-400 to-emerald-500 rounded-lg flex items-center justify-center mr-3 shadow-md">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                Peserta Hadir (<span id="present-list-count"
                                    class="font-bold">{{ $totalPresent }}</span>)
                            </h4>

                            <div id="present-students" class="space-y-3 max-h-96 lg:max-h-[500px] overflow-y-auto">
                                @forelse($presentStudents as $submission)
                                    <div
                                        class="flex justify-between items-center p-4 lg:p-5 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200 border-opacity-50 hover:from-green-100 hover:to-emerald-100 transition-all duration-300 transform hover:scale-[1.02] shadow-sm hover:shadow-md">
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-800 text-sm lg:text-base mb-1">
                                                {{ $submission->student->name ?? 'Nama tidak tersedia' }}</div>
                                            <div class="text-xs lg:text-sm text-gray-600 mb-1">
                                                {{ $submission->student->faculty ?? 'Fakultas tidak tersedia' }}</div>
                                            <div class="text-xs lg:text-sm text-gray-600 mb-1">
                                                {{ $submission->student->study_program ?? 'Program studi tidak tersedia' }}
                                            </div>
                                            <div class="text-xs lg:text-sm text-gray-500 flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $submission->submitted_at->format('H:i:s') }}
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            @php
                                                $statusClasses = match ($submission->status) {
                                                    'hadir'
                                                        => 'bg-gradient-to-r from-green-400 to-emerald-500 text-white',
                                                    'terlambat'
                                                        => 'bg-gradient-to-r from-yellow-400 to-orange-500 text-white',
                                                    'izin' => 'bg-gradient-to-r from-blue-400 to-cyan-500 text-white',
                                                    'sakit' => 'bg-gradient-to-r from-orange-400 to-red-500 text-white',
                                                    default => 'bg-gradient-to-r from-gray-400 to-gray-500 text-white',
                                                };
                                            @endphp
                                            <div
                                                class="text-xs px-3 py-2 {{ $statusClasses }} rounded-full font-medium shadow-sm">
                                                {{ ucfirst($submission->status) }}
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-gray-500 py-8 lg:py-12">
                                        <svg class="w-16 h-16 lg:w-20 lg:h-20 text-gray-300 mx-auto mb-4"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <p class="text-sm lg:text-base">Belum ada peserta yang hadir</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Absent Students -->
                        <div
                            class="bg-white bg-opacity-60 backdrop-blur-sm rounded-2xl p-6 border border-red-200 border-opacity-50">
                            <h4 class="text-md lg:text-lg font-semibold text-red-700 mb-6 flex items-center">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-red-400 to-rose-500 rounded-lg flex items-center justify-center mr-3 shadow-md">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                                Peserta Belum Hadir (<span id="absent-list-count"
                                    class="font-bold">{{ $totalAbsent }}</span>)
                            </h4>

                            <div id="absent-students" class="space-y-3 max-h-96 lg:max-h-[500px] overflow-y-auto">
                                @forelse($absentStudents as $student)
                                    <div
                                        class="flex justify-between items-center p-4 lg:p-5 bg-gradient-to-r from-red-50 to-rose-50 rounded-xl border border-red-200 border-opacity-50 hover:from-red-100 hover:to-rose-100 transition-all duration-300 transform hover:scale-[1.02] shadow-sm hover:shadow-md">
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-800 text-sm lg:text-base mb-1">
                                                {{ $student->name }}</div>
                                            <div class="text-xs lg:text-sm text-gray-600 mb-1">
                                                {{ $student->faculty ?? 'Fakultas tidak tersedia' }}</div>
                                            <div class="text-xs lg:text-sm text-gray-600 mb-1">
                                                {{ $student->study_program ?? 'Program studi tidak tersedia' }}</div>
                                            <div class="text-xs lg:text-sm text-gray-500 flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 00-2-2v0a2 2 0 00-2 2v2m4 0a2 2 0 104 0m-4 0a2 2 0 014 0z">
                                                    </path>
                                                </svg>
                                                {{ $student->student_id }}
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <div
                                                class="text-xs px-3 py-2 bg-gradient-to-r from-red-400 to-rose-500 text-white rounded-full font-medium shadow-sm">
                                                Belum Hadir
                                            </div>
                                            <button
                                                onclick="changeAbsentStudentStatus('{{ $student->id }}', '{{ addslashes($student->name) }}')"
                                                class="text-xs px-3 py-2 bg-gradient-to-r from-blue-400 to-cyan-500 text-white rounded-full hover:from-blue-500 hover:to-cyan-600 transition-all duration-300 transform hover:scale-105 font-medium shadow-sm">
                                                Opsi
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-gray-500 py-8 lg:py-12">
                                        <svg class="w-16 h-16 lg:w-20 lg:h-20 text-gray-300 mx-auto mb-4"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <p class="text-sm lg:text-base">Semua peserta sudah hadir!</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Include jsQR library -->
            <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>

            <script>
                class PresenceScanner {
                    constructor() {
                        this.video = document.getElementById('qr-video');
                        this.canvas = document.getElementById('qr-canvas');
                        this.context = this.canvas.getContext('2d');
                        this.placeholder = document.getElementById('qr-placeholder');
                        this.startButton = document.getElementById('start-scan');
                        this.stopButton = document.getElementById('stop-scan');
                        this.qrScanForm = document.getElementById('qr-scan-form');
                        this.scannedCodeInput = document.getElementById('scanned-code');
                        this.scanning = false;
                        this.stream = null;
                        this.scanStartTime = null;
                        this.scanTimeout = 15000; // 15 detik timeout
                        this.sessionExpiredShown = false; // Flag untuk mencegah spam modal
                        this.isSubmitting = false; // Flag untuk mencegah double submission
                        this.sessionCache = {
                            lastResponse: null,
                            timestamp: 0,
                            isValid: false
                        }; // Cache untuk response session check

                        this.initializeEventListeners();
                        this.updateCurrentTime();
                        setInterval(() => this.updateCurrentTime(), 1000);

                        // Cek status sesi setiap 30 detik dengan caching
                        this.checkSessionStatus();
                        setInterval(() => this.checkSessionStatus(), 30000);
                    }

                    initializeEventListeners() {
                        this.startButton.addEventListener('click', () => this.startScanning());
                        this.stopButton.addEventListener('click', () => this.stopScanning());

                        // Event listener untuk form manual
                        const manualForm = document.getElementById('manual-form');
                        manualForm.addEventListener('submit', (e) => {
                            e.preventDefault();

                            // Cek apakah sedang dalam proses submit
                            if (this.isSubmitting) {
                                return;
                            }

                            // Cek apakah presensi sedang berlangsung
                            const now = new Date();
                            const sessionStartTime = new Date('{{ $session->start_time }}');
                            const sessionEndTime = new Date('{{ $session->end_time }}');

                            if (now < sessionStartTime || now > sessionEndTime) {
                                this.showSessionStatusAlert();
                                return;
                            }

                            const manualCode = document.getElementById('manual-code').value;
                            if (!manualCode || manualCode.length !== 8) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Kode unik harus 8 karakter.',
                                    timer: 3000,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                });
                                return;
                            }

                            // Tampilkan loading
                            Swal.fire({
                                title: 'Memproses Presensi...',
                                html: `<div class="text-center">
                         <div class="mb-3">
                           <div class="text-lg font-semibold text-gray-800">Kode: ${manualCode}</div>
                         </div>
                         <div class="text-sm text-gray-500">Sedang memverifikasi kode...</div>
                       </div>`,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // Submit menggunakan AJAX
                            this.submitPresenceData({
                                manual_code: manualCode,
                                device_timestamp: this.formatDateTimeForMySQL(new Date()),
                                _token: document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            }, '{{ route('mentor.presence.manual', $slug) }}');
                        });
                    }

                    formatDateTimeForMySQL(date) {
                        // Konversi ke zona waktu Asia/Jakarta
                        const jakartaTime = new Date(date.toLocaleString("en-US", {
                            timeZone: "Asia/Jakarta"
                        }));

                        const year = jakartaTime.getFullYear();
                        const month = String(jakartaTime.getMonth() + 1).padStart(2, '0');
                        const day = String(jakartaTime.getDate()).padStart(2, '0');
                        const hours = String(jakartaTime.getHours()).padStart(2, '0');
                        const minutes = String(jakartaTime.getMinutes()).padStart(2, '0');
                        const seconds = String(jakartaTime.getSeconds()).padStart(2, '0');

                        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                    }

                    updateCurrentTime() {
                        const now = new Date();
                        const timeString = now.toLocaleString('id-ID', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit'
                        });
                        document.getElementById('time-display').textContent = timeString;

                        // Cek status sesi dan update tombol
                        this.checkSessionStatus(now);
                    }

                    checkSessionStatus(currentTime) {
                        const sessionStartTime = new Date('{{ $session->start_time }}');
                        const sessionEndTime = new Date('{{ $session->end_time }}');

                        if (currentTime < sessionStartTime) {
                            // Presensi belum dimulai
                            this.startButton.disabled = true;
                            this.startButton.textContent = 'Presensi Belum Dimulai';
                            this.startButton.classList.remove('bg-purple-600', 'hover:bg-purple-700');
                            this.startButton.classList.add('bg-gray-400', 'cursor-not-allowed');

                            const manualSubmitBtn = document.querySelector('#manual-form button[type="submit"]');
                            const manualCodeInput = document.getElementById('manual-code');

                            manualSubmitBtn.disabled = true;
                            manualSubmitBtn.textContent = 'Presensi Belum Dimulai';
                            manualSubmitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                            manualSubmitBtn.classList.add('bg-gray-400', 'cursor-not-allowed');

                            manualCodeInput.disabled = true;
                            manualCodeInput.classList.add('bg-gray-100', 'cursor-not-allowed');

                        } else if (currentTime > sessionEndTime) {
                            // Presensi sudah berakhir
                            this.startButton.disabled = true;
                            this.startButton.textContent = 'Presensi Telah Berakhir';
                            this.startButton.classList.remove('bg-purple-600', 'hover:bg-purple-700');
                            this.startButton.classList.add('bg-gray-400', 'cursor-not-allowed');

                            const manualSubmitBtn = document.querySelector('#manual-form button[type="submit"]');
                            const manualCodeInput = document.getElementById('manual-code');

                            manualSubmitBtn.disabled = true;
                            manualSubmitBtn.textContent = 'Presensi Telah Berakhir';
                            manualSubmitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                            manualSubmitBtn.classList.add('bg-gray-400', 'cursor-not-allowed');

                            manualCodeInput.disabled = true;
                            manualCodeInput.classList.add('bg-gray-100', 'cursor-not-allowed');

                        } else {
                            // Presensi sedang berlangsung
                            this.startButton.disabled = false;
                            this.startButton.textContent = 'Mulai Scan';
                            this.startButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
                            this.startButton.classList.add('bg-purple-600', 'hover:bg-purple-700');

                            const manualSubmitBtn = document.querySelector('#manual-form button[type="submit"]');
                            const manualCodeInput = document.getElementById('manual-code');

                            manualSubmitBtn.disabled = false;
                            manualSubmitBtn.textContent = 'Submit Presensi';
                            manualSubmitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                            manualSubmitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');

                            manualCodeInput.disabled = false;
                            manualCodeInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
                        }
                    }

                    showSessionStatusAlert() {
                        const now = new Date();
                        const sessionStartTime = new Date('{{ $session->start_time }}');
                        const sessionEndTime = new Date('{{ $session->end_time }}');

                        if (now < sessionStartTime) {
                            const startTimeFormatted = sessionStartTime.toLocaleString('id-ID', {
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false
                            });

                            Swal.fire({
                                icon: 'info',
                                title: 'Presensi Belum Dimulai',
                                text: `Presensi akan dimulai pada ${startTimeFormatted}`,
                                confirmButtonColor: '#3b82f6',
                                confirmButtonText: 'Mengerti'
                            });
                        } else if (now > sessionEndTime) {
                            // Set flag untuk mencegah popup dari checkSessionStatus muncul bersamaan
                            this.sessionExpiredShown = true;

                            const endTimeFormatted = sessionEndTime.toLocaleString('id-ID', {
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false
                            });

                            Swal.fire({
                                icon: 'warning',
                                title: 'Presensi Telah Berakhir',
                                text: `Presensi telah berakhir pada ${endTimeFormatted}`,
                                confirmButtonColor: '#f59e0b',
                                confirmButtonText: 'Mengerti',
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Popup kedua - dengan delay waktu untuk redirect
                                    Swal.fire({
                                        icon: 'info',
                                        title: 'Mengarahkan ke Dashboard',
                                        html: `<div class="text-center">
                                 <p class="mb-3">Anda akan diarahkan ke dashboard dalam:</p>
                                 <p class="font-semibold text-2xl text-blue-600" id="countdown-timer">5</p>
                                 <p class="mt-3 text-sm text-gray-600">detik...</p>
                               </div>`,
                                        timer: 5000,
                                        timerProgressBar: true,
                                        allowOutsideClick: false,
                                        allowEscapeKey: false,
                                        showConfirmButton: true,
                                        confirmButtonText: 'Ke Dashboard Sekarang',
                                        confirmButtonColor: '#3B82F6',
                                        didOpen: () => {
                                            // Countdown timer
                                            let timeLeft = 5;
                                            const countdownElement = document.getElementById(
                                                'countdown-timer');
                                            const countdownInterval = setInterval(() => {
                                                timeLeft--;
                                                if (countdownElement) {
                                                    countdownElement.textContent = timeLeft;
                                                }
                                                if (timeLeft <= 0) {
                                                    clearInterval(countdownInterval);
                                                }
                                            }, 1000);
                                        }
                                    }).then((result) => {
                                        // Redirect ke dashboard
                                        window.location.href = '{{ route('mentor.dashboard') }}';
                                    });
                                }
                            });
                        }
                    }

                    async checkSessionStatus() {
                        try {
                            const now = Date.now();
                            const CACHE_DURATION = 25000; // 25 detik cache

                            // Gunakan cache jika masih valid
                            if (this.sessionCache.isValid && (now - this.sessionCache.timestamp) < CACHE_DURATION) {
                                if (this.sessionCache.lastResponse && this.sessionCache.lastResponse.session_ended) {
                                    this.showSessionExpiredModal();
                                }
                                return;
                            }

                            const currentTime = this.formatDateTimeForMySQL(new Date());
                            const slug = '{{ $slug }}';

                            const response = await fetch(`/mentor/presence/${slug}/check-session`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    current_time: currentTime
                                })
                            });

                            const data = await response.json();

                            // Update cache
                            this.sessionCache = {
                                lastResponse: data,
                                timestamp: now,
                                isValid: true
                            };

                            if (data.success && !data.session_active && !this.sessionExpiredShown) {
                                // Set flag agar modal hanya muncul sekali
                                this.sessionExpiredShown = true;

                                // Stop scanning jika sedang aktif
                                if (this.scanning) {
                                    this.stopScanning();
                                }

                                // Popup pertama - konfirmasi bahwa sesi berakhir
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Sesi Presensi Berakhir',
                                    html: `<div class="text-center">
                             <p class="mb-3">Sesi presensi telah berakhir pada:</p>
                             <p class="font-semibold text-lg text-red-600">${new Date(data.session_end_time).toLocaleString('id-ID')}</p>
                             <p class="mt-3 text-sm text-gray-600">Klik "Mengerti" untuk melanjutkan.</p>
                           </div>`,
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    showConfirmButton: true,
                                    confirmButtonText: 'Mengerti',
                                    confirmButtonColor: '#f59e0b'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Popup kedua - dengan delay waktu untuk redirect
                                        Swal.fire({
                                            icon: 'info',
                                            title: 'Mengarahkan ke Dashboard',
                                            html: `<div class="text-center">
                                     <p class="mb-3">Anda akan diarahkan ke dashboard dalam:</p>
                                     <p class="font-semibold text-2xl text-blue-600" id="countdown-timer">5</p>
                                     <p class="mt-3 text-sm text-gray-600">detik...</p>
                                   </div>`,
                                            timer: 5000,
                                            timerProgressBar: true,
                                            allowOutsideClick: false,
                                            allowEscapeKey: false,
                                            showConfirmButton: true,
                                            confirmButtonText: 'Ke Dashboard Sekarang',
                                            confirmButtonColor: '#3B82F6',
                                            didOpen: () => {
                                                // Countdown timer
                                                let timeLeft = 5;
                                                const countdownElement = document.getElementById(
                                                    'countdown-timer');
                                                const countdownInterval = setInterval(() => {
                                                    timeLeft--;
                                                    if (countdownElement) {
                                                        countdownElement.textContent = timeLeft;
                                                    }
                                                    if (timeLeft <= 0) {
                                                        clearInterval(countdownInterval);
                                                    }
                                                }, 1000);
                                            }
                                        }).then((result) => {
                                            // Redirect ke dashboard
                                            window.location.href = data.redirect_url;
                                        });
                                    }
                                });
                            }
                        } catch (error) {
                            console.error('Error checking session status:', error);
                            // Invalidasi cache saat terjadi error
                            this.sessionCache.isValid = false;
                            // Jangan tampilkan error ke user untuk pengecekan background ini
                        }
                    }

                    async startScanning() {
                        // Cek apakah presensi sedang berlangsung
                        const now = new Date();
                        const sessionStartTime = new Date('{{ $session->start_time }}');
                        const sessionEndTime = new Date('{{ $session->end_time }}');

                        if (now < sessionStartTime || now > sessionEndTime) {
                            this.showSessionStatusAlert();
                            return;
                        }

                        try {
                            this.stream = await navigator.mediaDevices.getUserMedia({
                                video: {
                                    facingMode: 'environment'
                                }
                            });

                            this.video.srcObject = this.stream;
                            this.video.play();

                            this.placeholder.style.display = 'none';
                            this.video.style.display = 'block';
                            document.getElementById('scan-overlay').style.display = 'block';
                            this.startButton.style.display = 'none';
                            this.stopButton.style.display = 'block';

                            this.scanning = true;
                            this.scanStartTime = Date.now();
                            this.scanQRCode();

                        } catch (error) {
                            console.error('Error accessing camera:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Tidak dapat mengakses kamera. Pastikan izin kamera telah diberikan.',
                                timer: 5000,
                                timerProgressBar: true
                            });
                        }
                    }

                    stopScanning() {
                        this.scanning = false;
                        this.scanStartTime = null;

                        if (this.stream) {
                            this.stream.getTracks().forEach(track => track.stop());
                            this.stream = null;
                        }

                        this.video.style.display = 'none';
                        document.getElementById('scan-overlay').style.display = 'none';
                        this.placeholder.style.display = 'flex';
                        this.startButton.style.display = 'block';
                        this.stopButton.style.display = 'none';
                    }

                    scanQRCode() {
                        if (!this.scanning) return;

                        // Cek timeout - jika sudah lebih dari waktu yang ditentukan
                        if (this.scanStartTime && (Date.now() - this.scanStartTime) > this.scanTimeout) {
                            this.handleScanTimeout();
                            return;
                        }

                        if (this.video.readyState === this.video.HAVE_ENOUGH_DATA) {
                            this.canvas.width = this.video.videoWidth;
                            this.canvas.height = this.video.videoHeight;
                            this.context.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);

                            const imageData = this.context.getImageData(0, 0, this.canvas.width, this.canvas.height);
                            const code = jsQR(imageData.data, imageData.width, imageData.height);

                            if (code) {
                                this.processScanResult(code.data);
                                return;
                            }
                        }

                        requestAnimationFrame(() => this.scanQRCode());
                    }

                    handleScanTimeout() {
                        this.stopScanning();

                        Swal.fire({
                            icon: 'error',
                            title: 'QR Code Tidak Terbaca',
                            html: `<div class="text-center">
                        <p class="mb-3">QR code tidak benar atau tidak dapat terbaca.</p>
                        <p class="text-sm text-gray-600">Pastikan QR code jelas terlihat dan tidak rusak.</p>
                        <p class="text-sm text-gray-600 mt-2">Anda dapat mencoba scan ulang atau menggunakan input manual.</p>
                    </div>`,
                            confirmButtonText: 'Coba Lagi',
                            confirmButtonColor: '#3B82F6',
                            showCancelButton: true,
                            cancelButtonText: 'Tutup',
                            cancelButtonColor: '#6B7280'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Mulai scan ulang
                                this.startScanning();
                            }
                        });
                    }

                    showInvalidQRCodeAlert(qrData) {
                        let displayData = '';

                        if (typeof qrData === 'object' && qrData !== null) {
                            displayData = JSON.stringify(qrData, null, 2);
                        } else {
                            displayData = qrData;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'QR Code Tidak Benar',
                            html: `<div class="text-center">
                        <p class="mb-3">QR code yang dipindai tidak memiliki format yang benar.</p>
                        <div class="bg-gray-100 p-3 rounded-lg mb-3 text-left">
                            <p class="text-sm font-semibold text-gray-700 mb-2">Data yang ditemukan:</p>
                            <pre class="text-xs text-gray-600 whitespace-pre-wrap">${displayData}</pre>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-lg text-left">
                            <p class="text-sm font-semibold text-blue-700 mb-2">Format yang benar:</p>
                            <pre class="text-xs text-blue-600">{"nama":"Nama Peserta","student_id":"NIM","mentor":"Nama Mentor"}</pre>
                        </div>
                        <p class="text-sm text-gray-600 mt-3">Pastikan menggunakan QR code yang valid untuk presensi.</p>
                    </div>`,
                            confirmButtonText: 'Scan Ulang',
                            confirmButtonColor: '#3B82F6',
                            showCancelButton: true,
                            cancelButtonText: 'Tutup',
                            cancelButtonColor: '#6B7280'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Mulai scan ulang
                                this.startScanning();
                            }
                        });
                    }

                    processScanResult(code) {
                        this.stopScanning();

                        // Cek apakah sedang dalam proses submit
                        if (this.isSubmitting) {
                            return;
                        }

                        // Parse dan validasi data dari QR code
                        let studentInfo = null;
                        let isValidQRCode = false;

                        try {
                            const qrData = JSON.parse(code);

                            // Validasi struktur QR code yang benar
                            if (qrData && qrData.nama && qrData.student_id && qrData.mentor) {
                                studentInfo = {
                                    nama: qrData.nama,
                                    nim: qrData.student_id,
                                    mentor: qrData.mentor
                                };
                                isValidQRCode = true;
                            } else {
                                // QR code tidak memiliki struktur yang benar
                                this.showInvalidQRCodeAlert(qrData);
                                return;
                            }
                        } catch (e) {
                            // QR code bukan JSON yang valid
                            this.showInvalidQRCodeAlert(code);
                            return;
                        }

                        // Jika QR code valid, lanjutkan proses
                        if (!isValidQRCode) {
                            return;
                        }

                        // Tampilkan loading dengan informasi peserta
                        Swal.fire({
                            title: 'Memproses Presensi...',
                            html: `<div class="text-center">
                     <div class="mb-3">
                       <div class="text-lg font-semibold text-gray-800">${studentInfo.nama}</div>
                       <div class="text-sm text-gray-600">NIM: ${studentInfo.nim}</div>
                     </div>
                     <div class="text-sm text-gray-500">Sedang mengirim data presensi...</div>
                   </div>`,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit menggunakan AJAX
                        this.submitPresenceData({
                            code: code,
                            device_timestamp: this.formatDateTimeForMySQL(new Date()),
                            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }, '{{ route('mentor.presence.scan', $slug) }}');
                    }

                    submitPresenceData(data, url) {
                        // Cek apakah sedang dalam proses submit
                        if (this.isSubmitting) {
                            return;
                        }

                        // Set flag untuk mencegah double submission
                        this.isSubmitting = true;

                        fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': data._token,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify(data)
                            })
                            .then(response => {
                                if (!response.ok) {
                                    return response.json().then(errorData => {
                                        throw new Error(errorData.message ||
                                            `HTTP error! status: ${response.status}`);
                                    });
                                }
                                return response.json();
                            })
                            .then(result => {
                                Swal.close();
                                // Reset flag setelah mendapat response
                                this.isSubmitting = false;

                                if (result.success) {
                                    // Determine icon based on response type
                                    const icon = result.type === 'warning' ? 'warning' : 'success';

                                    Swal.fire({
                                        icon: icon,
                                        title: icon === 'warning' ? 'Perhatian!' : 'Berhasil!',
                                        text: result.message,
                                        timer: 3000,
                                        timerProgressBar: true,
                                        showConfirmButton: false
                                    });

                                    // Update data attendance
                                    this.refreshAttendanceData();

                                    // Clear manual input if exists
                                    const manualCodeInput = document.getElementById('manual-code');
                                    if (manualCodeInput) {
                                        manualCodeInput.value = '';
                                    }
                                } else {
                                    // This shouldn't happen with the new controller logic, but keep as fallback
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: result.message || 'Terjadi kesalahan saat memproses presensi.',
                                        timer: 5000,
                                        timerProgressBar: true,
                                        showConfirmButton: false
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.close();
                                // Reset flag jika terjadi error
                                this.isSubmitting = false;

                                // Handle different types of errors
                                let errorMessage = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
                                let errorIcon = 'error';

                                if (error.message && error.message !== 'Failed to fetch') {
                                    errorMessage = error.message;

                                    // Check if it's a warning type error
                                    if (error.message.includes('sudah melakukan presensi')) {
                                        errorIcon = 'warning';
                                    }
                                }

                                Swal.fire({
                                    icon: errorIcon,
                                    title: errorIcon === 'warning' ? 'Perhatian!' : 'Error!',
                                    text: errorMessage,
                                    timer: 5000,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                });
                            });
                    }

                    // Function to get status classes based on attendance status
                    getStatusClasses(status) {
                        switch (status) {
                            case 'hadir':
                                return 'bg-gradient-to-r from-green-400 to-emerald-500 text-white';
                            case 'terlambat':
                                return 'bg-gradient-to-r from-yellow-400 to-orange-500 text-white';
                            case 'izin':
                                return 'bg-gradient-to-r from-blue-400 to-cyan-500 text-white';
                            case 'sakit':
                                return 'bg-gradient-to-r from-orange-400 to-red-500 text-white';
                            default:
                                return 'bg-gradient-to-r from-gray-400 to-gray-500 text-white';
                        }
                    }

                    refreshAttendanceData() {
                        fetch('{{ route('mentor.presence.data', $slug) }}')
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Update counters
                                    document.getElementById('present-count').textContent = data.data.totalPresent;
                                    document.getElementById('absent-count').textContent = data.data.totalAbsent;
                                    document.getElementById('total-count').textContent = data.data.totalStudents;
                                    document.getElementById('present-list-count').textContent = data.data.totalPresent;
                                    document.getElementById('absent-list-count').textContent = data.data.totalAbsent;

                                    // Update present students list
                                    const presentContainer = document.getElementById('present-students');
                                    if (data.data.presentStudents.length > 0) {
                                        presentContainer.innerHTML = data.data.presentStudents.map(submission => {
                                            const statusClasses = this.getStatusClasses(submission.status);
                                            return `
                                <div class="flex justify-between items-center p-4 lg:p-5 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200 border-opacity-50 hover:from-green-100 hover:to-emerald-100 transition-all duration-300 transform hover:scale-[1.02] shadow-sm hover:shadow-md">
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-800 text-sm lg:text-base mb-1">${submission.student_name}</div>
                                        <div class="text-xs lg:text-sm text-gray-600 mb-1">${submission.faculty || 'Fakultas tidak tersedia'}</div>
                                        <div class="text-xs lg:text-sm text-gray-600 mb-1">${submission.study_program || 'Program studi tidak tersedia'}</div>
                                        <div class="text-xs lg:text-sm text-gray-500 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                            </svg>
                                            ${submission.student_id || 'ID tidak tersedia'}
                                        </div>
                                    </div>
                                        <div class="text-xs lg:text-sm text-gray-500 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            ${submission.submitted_at || new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit', second: '2-digit'})}
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="text-xs px-3 py-2 ${statusClasses} rounded-full font-medium shadow-sm">
                                            ${submission.status ? submission.status.charAt(0).toUpperCase() + submission.status.slice(1) : 'Hadir'}
                                        </div>
                                    </div>
                                </div>
                                `;
                                        }).join('');
                                    } else {
                                        presentContainer.innerHTML = `
                                <div class="text-center text-gray-500 py-8 lg:py-12">
                                    <svg class="w-16 h-16 lg:w-20 lg:h-20 text-gray-300 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                    <p class="text-sm lg:text-base">Belum ada peserta yang hadir</p>
                                </div>
                            `;
                                    }

                                    // Update absent students list
                                    const absentContainer = document.getElementById('absent-students');
                                    if (data.data.absentStudents.length > 0) {
                                        absentContainer.innerHTML = data.data.absentStudents.map(student => `
                                <div class="flex justify-between items-center p-4 lg:p-5 bg-gradient-to-r from-red-50 to-rose-50 rounded-xl border border-red-200 border-opacity-50 hover:from-red-100 hover:to-rose-100 transition-all duration-300 transform hover:scale-[1.02] shadow-sm hover:shadow-md">
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-800 text-sm lg:text-base mb-1">${student.name}</div>
                                        <div class="text-xs lg:text-sm text-gray-600 mb-1">${student.faculty || 'Fakultas tidak tersedia'}</div>
                                        <div class="text-xs lg:text-sm text-gray-600 mb-1">${student.study_program || 'Program studi tidak tersedia'}</div>
                                        <div class="text-xs lg:text-sm text-gray-500 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 00-2-2v0a2 2 0 00-2 2v2m4 0a2 2 0 104 0m-4 0a2 2 0 014 0z"></path>
                                            </svg>
                                            ${student.student_id}
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="text-xs px-3 py-2 bg-gradient-to-r from-red-400 to-rose-500 text-white rounded-full font-medium shadow-sm">
                                            Belum Hadir
                                        </div>
                                        <button onclick="changeAbsentStudentStatus('${student.id}', '${student.name.replace(/'/g, '\\\'')}')"
                                            class="text-xs px-3 py-2 bg-gradient-to-r from-blue-400 to-cyan-500 text-white rounded-full hover:from-blue-500 hover:to-cyan-600 transition-all duration-300 transform hover:scale-105 font-medium shadow-sm">
                                            Opsi
                                        </button>
                                    </div>
                                </div>
                            `).join('');
                                    } else {
                                        absentContainer.innerHTML = `
                                <div class="text-center text-gray-500 py-8 lg:py-12">
                                    <svg class="w-16 h-16 lg:w-20 lg:h-20 text-gray-300 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <p class="text-sm lg:text-base">Semua peserta sudah hadir!</p>
                                </div>
                            `;
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error refreshing data:', error);
                            });
                    }
                }
                // Function to change status for absent students (create new attendance record)
                function changeAbsentStudentStatus(studentId, studentName) {
                    // Cek validasi waktu terlebih dahulu
                    const now = new Date();
                    const sessionStartTime = new Date('{{ $session->start_time }}');
                    const sessionEndTime = new Date('{{ $session->end_time }}');

                    if (now < sessionStartTime) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Presensi Belum Dimulai',
                            text: `Sesi presensi belum dimulai. Waktu mulai: ${sessionStartTime.toLocaleString('id-ID')}`,
                            timer: 5000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                        return;
                    }

                    if (now > sessionEndTime) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Presensi Telah Berakhir',
                            text: `Sesi presensi sudah berakhir. Waktu berakhir: ${sessionEndTime.toLocaleString('id-ID')}`,
                            timer: 5000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                        return;
                    }

                    Swal.fire({
                        title: `Buat Record Presensi`,
                        html: `
                    <div class="text-center mb-6">
                        <div class="font-semibold text-gray-800 text-lg">${studentName}</div>
                        <div class="text-sm text-gray-600 mt-1">Pilih status kehadiran untuk membuat record presensi</div>
                    </div>
                    <div class="grid grid-cols-1 gap-3">
                        <button onclick="createAttendanceRecord(${studentId}, 'terlambat', '${studentName.replace(/'/g, '\\\'')}')"
                                class="w-full py-3 px-4 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-medium transition-colors">
                            ⏰ Terlambat
                        </button>
                        <button onclick="createAttendanceRecord(${studentId}, 'izin', '${studentName.replace(/'/g, '\\\'')}')"
                                class="w-full py-3 px-4 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors">
                            📋 Izin
                        </button>
                        <button onclick="createAttendanceRecord(${studentId}, 'sakit', '${studentName.replace(/'/g, '\\\'')}')"
                                class="w-full py-3 px-4 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium transition-colors">
                            🏥 Sakit
                        </button>
                    </div>
                `,
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonText: 'Batal',
                        cancelButtonColor: '#6B7280',
                        width: '400px',
                        customClass: {
                            popup: 'rounded-2xl',
                            htmlContainer: 'text-left'
                        }
                    });
                }

                // Helper function untuk format datetime MySQL
                function formatDateTimeForMySQL(date) {
                    // Konversi ke zona waktu Asia/Jakarta
                    const jakartaTime = new Date(date.toLocaleString("en-US", {
                        timeZone: "Asia/Jakarta"
                    }));

                    const year = jakartaTime.getFullYear();
                    const month = String(jakartaTime.getMonth() + 1).padStart(2, '0');
                    const day = String(jakartaTime.getDate()).padStart(2, '0');
                    const hours = String(jakartaTime.getHours()).padStart(2, '0');
                    const minutes = String(jakartaTime.getMinutes()).padStart(2, '0');
                    const seconds = String(jakartaTime.getSeconds()).padStart(2, '0');

                    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                }

                // Function to create attendance record for absent students
                function createAttendanceRecord(studentId, status, studentName) {
                    Swal.close();

                    // Validasi waktu sekali lagi sebelum mengirim request
                    const now = new Date();
                    const sessionStartTime = new Date('{{ $session->start_time }}');
                    const sessionEndTime = new Date('{{ $session->end_time }}');

                    if (now < sessionStartTime || now > sessionEndTime) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Waktu Tidak Valid',
                            text: 'Sesi presensi tidak sedang berlangsung.',
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                        return;
                    }

                    // Show loading
                    Swal.fire({
                        title: 'Membuat Record...',
                        html: `<div class="text-center">
                    <div class="text-lg font-semibold text-gray-800">${studentName}</div>
                    <div class="text-sm text-gray-600 mt-1">Status: ${status.charAt(0).toUpperCase() + status.slice(1)}</div>
                </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Send AJAX request
                    fetch('{{ route('mentor.presence.create-record', $slug) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                student_id: studentId,
                                status: status,
                                device_timestamp: formatDateTimeForMySQL(now)
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(errorData => {
                                    throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                                });
                            }
                            return response.json();
                        })
                        .then(result => {
                            Swal.close();

                            if (result.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: result.message,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                });

                                // Refresh attendance data
                                const scanner = new PresenceScanner();
                                scanner.refreshAttendanceData();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: result.message || 'Terjadi kesalahan saat membuat record presensi.',
                                    timer: 5000,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.close();

                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: error.message || 'Terjadi kesalahan jaringan. Silakan coba lagi.',
                                timer: 5000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            });
                        });
                }

                // Initialize scanner when page loads
                document.addEventListener('DOMContentLoaded', function() {
                    const scanner = new PresenceScanner();

                    // Tampilkan alert saat halaman dimuat jika presensi belum dimulai atau sudah berakhir
                    setTimeout(() => {
                        scanner.showSessionStatusAlert();
                    }, 500); // Delay sedikit untuk memastikan SweetAlert2 sudah siap

                    // Handle info modal untuk mobile
                    const infoBtn = document.getElementById('info-btn-mobile');
                    const infoModal = document.getElementById('info-modal-mobile');
                    const closeModalBtn = document.getElementById('close-modal-btn');
                    const closeModalBtn2 = document.getElementById('close-modal-btn-2');

                    if (infoBtn && infoModal) {
                        // Show modal
                        infoBtn.addEventListener('click', function() {
                            infoModal.classList.remove('hidden');
                            document.body.style.overflow = 'hidden'; // Prevent background scroll
                        });

                        // Hide modal functions
                        function hideModal() {
                            infoModal.classList.add('hidden');
                            document.body.style.overflow = ''; // Restore scroll
                        }

                        // Close modal events
                        if (closeModalBtn) {
                            closeModalBtn.addEventListener('click', hideModal);
                        }

                        if (closeModalBtn2) {
                            closeModalBtn2.addEventListener('click', hideModal);
                        }

                        // Close modal when clicking outside
                        infoModal.addEventListener('click', function(e) {
                            if (e.target === infoModal) {
                                hideModal();
                            }
                        });

                        // Close modal with escape key
                        document.addEventListener('keydown', function(e) {
                            if (e.key === 'Escape' && !infoModal.classList.contains('hidden')) {
                                hideModal();
                            }
                        });
                    }
                });
            </script>
            <style>
                /* Custom Responsive Styles */
                @media (max-width: 640px) {
                    .container {
                        padding-left: 1rem;
                        padding-right: 1rem;
                    }

                    .grid-cols-1 {
                        gap: 1rem;
                    }

                    .text-3xl {
                        font-size: 1.875rem;
                        line-height: 2.25rem;
                    }

                    .p-8 {
                        padding: 1.5rem;
                    }
                }

                @media (min-width: 641px) and (max-width: 1024px) {
                    .lg\:grid-cols-2 {
                        grid-template-columns: repeat(1, minmax(0, 1fr));
                    }

                    .lg\:grid-cols-3 {
                        grid-template-columns: repeat(2, minmax(0, 1fr));
                    }
                }

                @media (min-width: 1025px) {
                    .container {
                        max-width: 1200px;
                    }
                }

                /* Smooth Animations */
                .transform {
                    transition: transform 0.3s ease-in-out;
                }

                .hover\:scale-105:hover {
                    transform: scale(1.05);
                }

                .hover\:scale-\[1\.02\]:hover {
                    transform: scale(1.02);
                }

                /* Glassmorphism Effects */
                .backdrop-blur-xl {
                    backdrop-filter: blur(16px);
                }

                .backdrop-blur-sm {
                    backdrop-filter: blur(4px);
                }

                /* Custom Scrollbar */
                .overflow-y-auto::-webkit-scrollbar {
                    width: 6px;
                }

                .overflow-y-auto::-webkit-scrollbar-track {
                    background: #f1f5f9;
                    border-radius: 3px;
                }

                .overflow-y-auto::-webkit-scrollbar-thumb {
                    background: #cbd5e1;
                    border-radius: 3px;
                }

                .overflow-y-auto::-webkit-scrollbar-thumb:hover {
                    background: #94a3b8;
                }
            </style>
        @endsection
