@extends('layouts.error')

@section('title', '429 - Terlalu Banyak Permintaan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-blue-50 flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-lg w-full text-center">
        <!-- 429 Illustration -->
        <div class="mb-8">
            <div class="relative">
                <!-- Main 429 Text -->
                <div class="text-8xl sm:text-9xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-blue-600 mb-4">
                    429
                </div>
                
                <!-- Floating Elements -->
                <div class="absolute -top-4 -left-4 w-8 h-8 bg-indigo-200 rounded-full animate-bounce" style="animation-delay: 0.1s;"></div>
                <div class="absolute -top-2 -right-6 w-6 h-6 bg-blue-200 rounded-full animate-bounce" style="animation-delay: 0.3s;"></div>
                <div class="absolute -bottom-2 left-8 w-4 h-4 bg-cyan-200 rounded-full animate-bounce" style="animation-delay: 0.5s;"></div>
            </div>
        </div>

        <!-- Error Message -->
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-4">
                Terlalu Banyak Permintaan
            </h1>
            <p class="text-gray-600 text-lg mb-2">
                Anda telah mengirim terlalu banyak permintaan dalam waktu singkat.
            </p>
            <p class="text-gray-500 text-sm">
                Silakan tunggu beberapa saat sebelum mencoba lagi.
            </p>
        </div>

        <!-- Countdown Timer -->
        <div class="mb-8">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Tunggu sebentar...</h3>
                <div class="flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-2xl font-bold text-indigo-600" id="countdown">60</span>
                    <span class="text-gray-600">detik</span>
                </div>
                <p class="text-sm text-gray-500 mt-2">
                    Halaman akan otomatis dapat diakses kembali setelah waktu tunggu berakhir.
                </p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
            <button onclick="location.reload()" 
                    id="retryButton"
                    disabled
                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span id="retryText">Tunggu...</span>
            </button>
            
            <a href="{{ url('/') }}" 
               class="inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Kembali ke Beranda
            </a>
        </div>

        <!-- Additional Help -->
        <div class="mt-12 p-6 bg-white rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Mengapa ini terjadi?</h3>
            <p class="text-gray-600 text-sm mb-4">
                Error 429 biasanya terjadi karena:
            </p>
            <ul class="text-left text-sm text-gray-600 space-y-2">
                <li class="flex items-start">
                    <svg class="w-4 h-4 mt-0.5 mr-2 text-indigo-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Terlalu banyak refresh halaman
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 mt-0.5 mr-2 text-indigo-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Mengirim formulir berulang kali
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 mt-0.5 mr-2 text-indigo-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Aktivitas yang mencurigakan terdeteksi
                </li>
            </ul>
            
            <div class="mt-4 p-3 bg-indigo-50 rounded-lg">
                <p class="text-sm text-indigo-800">
                    <strong>Tips:</strong> Gunakan aplikasi dengan normal dan hindari aktivitas berulang yang berlebihan.
                </p>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="mt-8 text-xs text-gray-400">
            Error Code: 429 | {{ now()->format('d M Y, H:i:s') }}
        </div>
    </div>
</div>

<script>
// Countdown timer
let timeLeft = 60;
const countdownElement = document.getElementById('countdown');
const retryButton = document.getElementById('retryButton');
const retryText = document.getElementById('retryText');

const timer = setInterval(() => {
    timeLeft--;
    countdownElement.textContent = timeLeft;
    
    if (timeLeft <= 0) {
        clearInterval(timer);
        retryButton.disabled = false;
        retryButton.classList.remove('opacity-50', 'cursor-not-allowed');
        retryText.textContent = 'Coba Lagi';
        countdownElement.textContent = '0';
        
        // Auto reload after countdown
        setTimeout(() => {
            location.reload();
        }, 1000);
    }
}, 1000);

// Progress bar animation
const progressBar = document.createElement('div');
progressBar.className = 'w-full bg-gray-200 rounded-full h-2 mt-4';
progressBar.innerHTML = '<div class="bg-indigo-600 h-2 rounded-full transition-all duration-1000 ease-linear" style="width: 100%"></div>';
document.querySelector('#countdown').parentElement.parentElement.appendChild(progressBar);

const progressFill = progressBar.querySelector('div');
let progress = 100;

const progressTimer = setInterval(() => {
    progress -= (100/60);
    progressFill.style.width = Math.max(0, progress) + '%';
    
    if (progress <= 0) {
        clearInterval(progressTimer);
    }
}, 1000);
</script>
@endsection