@extends('layouts.error')

@section('title', '403 - Akses Ditolak')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 via-white to-orange-50 flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-lg w-full text-center">
        <!-- 403 Illustration -->
        <div class="mb-8">
            <div class="relative">
                <!-- Main 403 Text -->
                <div class="text-8xl sm:text-9xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-orange-600 mb-4">
                    403
                </div>
                
                <!-- Floating Elements -->
                <div class="absolute -top-4 -left-4 w-8 h-8 bg-red-200 rounded-full animate-bounce" style="animation-delay: 0.1s;"></div>
                <div class="absolute -top-2 -right-6 w-6 h-6 bg-orange-200 rounded-full animate-bounce" style="animation-delay: 0.3s;"></div>
                <div class="absolute -bottom-2 left-8 w-4 h-4 bg-yellow-200 rounded-full animate-bounce" style="animation-delay: 0.5s;"></div>
            </div>
        </div>

        <!-- Error Message -->
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-4">
                Akses Ditolak!
            </h1>
            <p class="text-gray-600 text-lg mb-2">
                Anda tidak memiliki izin untuk mengakses halaman ini.
            </p>
            <p class="text-gray-500 text-sm">
                Silakan hubungi administrator jika Anda yakin ini adalah kesalahan.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
            <a href="{{ url('/') }}" 
               class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 transform hover:scale-105 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Kembali ke Beranda
            </a>
            
            <button onclick="history.back()" 
                    class="inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Halaman Sebelumnya
            </button>
        </div>

        <!-- Additional Help -->
        <div class="mt-12 p-6 bg-white rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Mengapa ini terjadi?</h3>
            <p class="text-gray-600 text-sm mb-4">
                Error 403 terjadi ketika server memahami permintaan Anda tetapi menolak untuk memberikan akses:
            </p>
            <ul class="text-left text-sm text-gray-600 space-y-2">
                <li class="flex items-start">
                    <svg class="w-4 h-4 mt-0.5 mr-2 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    Anda belum login atau sesi telah berakhir
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 mt-0.5 mr-2 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    Akun Anda tidak memiliki hak akses yang diperlukan
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 mt-0.5 mr-2 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    Halaman dibatasi untuk pengguna tertentu
                </li>
            </ul>
        </div>

        <!-- Footer Info -->
        <div class="mt-8 text-xs text-gray-400">
            Error Code: 403 | {{ now()->format('d M Y, H:i:s') }}
        </div>
    </div>
</div>
@endsection