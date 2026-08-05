@extends('layouts.error')

@section('title', '500 - Kesalahan Server')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-pink-50 flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-lg w-full text-center">
        <!-- 500 Illustration -->
        <div class="mb-8">
            <div class="relative">
                <!-- Main 500 Text -->
                <div class="text-8xl sm:text-9xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600 mb-4">
                    500
                </div>
                
                <!-- Floating Elements -->
                <div class="absolute -top-4 -left-4 w-8 h-8 bg-purple-200 rounded-full animate-bounce" style="animation-delay: 0.1s;"></div>
                <div class="absolute -top-2 -right-6 w-6 h-6 bg-pink-200 rounded-full animate-bounce" style="animation-delay: 0.3s;"></div>
                <div class="absolute -bottom-2 left-8 w-4 h-4 bg-indigo-200 rounded-full animate-bounce" style="animation-delay: 0.5s;"></div>
            </div>
        </div>

        <!-- Error Message -->
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-4">
                Oops! Terjadi Kesalahan Server
            </h1>
            <p class="text-gray-600 text-lg mb-2">
                Server mengalami masalah internal dan tidak dapat menyelesaikan permintaan Anda.
            </p>
            <p class="text-gray-500 text-sm">
                Tim teknis kami telah diberitahu dan sedang menangani masalah ini.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
            <a href="{{ url('/') }}" 
               class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 transform hover:scale-105 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Kembali ke Beranda
            </a>
            
            <button onclick="location.reload()" 
                    class="inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Coba Lagi
            </button>
        </div>

        <!-- Additional Help -->
        <div class="mt-12 p-6 bg-white rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Apa yang bisa Anda lakukan?</h3>
            <p class="text-gray-600 text-sm mb-4">
                Sementara kami memperbaiki masalah ini, Anda dapat mencoba:
            </p>
            <ul class="text-left text-sm text-gray-600 space-y-2">
                <li class="flex items-start">
                    <svg class="w-4 h-4 mt-0.5 mr-2 text-purple-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Tunggu beberapa menit dan coba lagi
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 mt-0.5 mr-2 text-purple-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Refresh halaman (Ctrl + F5)
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 mt-0.5 mr-2 text-purple-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Hubungi administrator jika masalah berlanjut
                </li>
            </ul>
        </div>

        <!-- Footer Info -->
        <div class="mt-8 text-xs text-gray-400">
            Error Code: 500 | {{ now()->format('d M Y, H:i:s') }}
        </div>
    </div>
</div>
@endsection