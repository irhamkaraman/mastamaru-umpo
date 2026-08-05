@extends('layouts.error')

@section('title', '419 - Halaman Kedaluwarsa')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-lg w-full text-center">
        <!-- 419 Illustration -->
        <div class="mb-8">
            <div class="relative">
                <!-- Main 419 Text -->
                <div class="text-8xl sm:text-9xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-orange-600 to-red-600 mb-4">
                    419
                </div>
                
                <!-- Floating Elements -->
                <div class="absolute -top-4 -left-4 w-8 h-8 bg-orange-200 rounded-full animate-bounce" style="animation-delay: 0.1s;"></div>
                <div class="absolute -top-2 -right-6 w-6 h-6 bg-red-200 rounded-full animate-bounce" style="animation-delay: 0.3s;"></div>
                <div class="absolute -bottom-2 left-8 w-4 h-4 bg-yellow-200 rounded-full animate-bounce" style="animation-delay: 0.5s;"></div>
            </div>
        </div>

        <!-- Error Message -->
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-4">
                Halaman Kedaluwarsa
            </h1>
            <p class="text-gray-600 text-lg mb-2">
                Sesi Anda telah berakhir karena tidak ada aktivitas dalam waktu yang lama.
            </p>
            <p class="text-gray-500 text-sm">
                Silakan refresh halaman atau kembali ke halaman sebelumnya untuk melanjutkan.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
            <button onclick="location.reload()" 
                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200 transform hover:scale-105 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Refresh Halaman
            </button>
            
            <button onclick="history.back()" 
                    class="inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </button>
        </div>

        <!-- Additional Help -->
        <div class="mt-12 p-6 bg-white rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Mengapa ini terjadi?</h3>
            <p class="text-gray-600 text-sm mb-4">
                Error 419 biasanya terjadi karena:
            </p>
            <ul class="text-left text-sm text-gray-600 space-y-2">
                <li class="flex items-start">
                    <svg class="w-4 h-4 mt-0.5 mr-2 text-orange-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Sesi keamanan telah berakhir
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 mt-0.5 mr-2 text-orange-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Halaman dibuka terlalu lama tanpa aktivitas
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 mt-0.5 mr-2 text-orange-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Token keamanan tidak valid
                </li>
            </ul>
            
            <div class="mt-4 p-3 bg-orange-50 rounded-lg">
                <p class="text-sm text-orange-800">
                    <strong>Solusi:</strong> Refresh halaman untuk mendapatkan sesi baru yang valid.
                </p>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="mt-8 text-xs text-gray-400">
            Error Code: 419 | {{ now()->format('d M Y, H:i:s') }}
        </div>
    </div>
</div>
@endsection