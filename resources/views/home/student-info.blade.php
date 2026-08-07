@extends('layouts.home')

@section('title', 'Informasi Peserta - ' . $student->name)

@section('content')
    <div class="container mx-auto px-4 py-8 sm:py-12 max-w-md lg:max-w-4xl relative z-10">
        <!-- Header dengan informasi peserta -->
        <div class="bg-gradient-to-br from-emerald-400 via-cyan-400 to-purple-600 rounded-[2rem] shadow-xl p-6 sm:p-10 mb-6 text-white">
            <div class="text-center">
                {{-- <div class="w-20 h-20 sm:w-24 sm:h-24 bg-white bg-opacity-30 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <svg class="w-10 h-10 sm:w-12 sm:h-12 text-blue-700" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4 4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h16v12H4V6zm2 2v2h2V8H6zm4 0v2h8V8h-8zm-4 4v2h2v-2H6zm4 0v2h8v-2h-8z"/>
                    </svg>
                </div> --}}
                <h1 class="text-2xl sm:text-3xl font-bold mb-2 text-white drop-shadow-lg">{{ $student->name }}</h1>
                <p class="text-white text-opacity-90 text-sm sm:text-base mb-2 font-medium">NIM: {{ $student->student_id }}</p>
                <div class="inline-flex items-center bg-white bg-opacity-25 backdrop-blur-sm rounded-full px-4 py-2 shadow-md">
                    <svg class="w-4 h-4 mr-2 text-green-700" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm font-semibold text-green-700">Peserta Terdaftar</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- QR Code Section -->
            <div class="ui-panel rounded-[2rem] p-6 sm:p-8">
                <div class="text-center">
                    <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4 flex items-center justify-center">
                        <svg class="w-6 h-6 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm2 2V5h1v1H5zM3 13a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3zm2 2v-1h1v1H5zM13 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1V4zm2 2V5h1v1h-1zM13 13a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-3zm2 2v-1h1v1h-1z" clip-rule="evenodd"></path>
                        </svg>
                        QR Code Presensi
                    </h2>

                    <div class="bg-gray-50 rounded-xl p-4 mb-4 inline-block">
                        <div id="qr-code-container">
                            <!-- QR Code akan di-generate di sini -->
                        </div>
                    </div>

                    <p class="text-sm text-gray-600 mb-4">Tunjukkan QR Code ini kepada Pemandu untuk melakukan presensi</p>

                    <!-- Tombol Unduh QR Code -->
                    <button id="download-qr"
                            class="bg-gradient-to-r from-purple-600 to-indigo-500 hover:from-purple-700 hover:to-indigo-600 text-white font-medium py-2 px-4 rounded-xl transition duration-200 inline-flex items-center mb-2 shadow-lg shadow-purple-200"
                            onclick="downloadQRCode()">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        Unduh QR Code
                    </button>
                </div>
            </div>

            <!-- Informasi Detail -->
            <div class="space-y-6">
                <!-- Kode Unik -->
                <div class="ui-panel rounded-[2rem] p-6 sm:p-8">
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                        Kode Unik
                    </h3>
                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                        <div class="text-2xl sm:text-3xl font-bold text-blue-600 font-mono tracking-wider mb-2" id="unique-code">
                            {{ $uniqueCode }}
                        </div>
                        <p class="text-sm text-blue-700">Gunakan kode ini untuk input manual</p>
                    </div>
                </div>

                <!-- Informasi Peserta -->
                <div class="ui-panel rounded-[2rem] p-6 sm:p-8">
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                        </svg>
                        Informasi Peserta
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">Fakultas:</span>
                            <span class="text-sm font-semibold text-gray-800">{{ $student->faculty ?? 'Fakultas tidak tersedia' }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">Program Studi:</span>
                            <span class="text-sm font-semibold text-gray-800">{{ $student->study_program ?? 'Program studi tidak tersedia' }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">Kelompok:</span>
                            <span class="text-sm font-semibold text-gray-800">{{ $student->group->name }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">Pemandu:</span>
                            <span class="text-sm font-semibold text-gray-800">{{ $student->mentor->name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Sertifikat Section (Jika Ada) -->
                @if(isset($certificateUrl) && $certificateUrl)
                <div class="ui-panel rounded-[2rem] p-6 sm:p-8 border-2 border-green-200">
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zM6.293 9.293a1 1 0 011.414 0L10 11.586l2.293-2.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        Sertifikat Anda
                    </h3>
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <p class="text-sm text-green-700 mb-4">Selamat! Sertifikat kegiatan Anda telah diterbitkan.</p>
                        <a href="{{ $certificateUrl }}" download class="bg-gradient-to-r from-green-600 to-emerald-500 hover:from-green-700 hover:to-emerald-600 text-white font-medium py-2 px-6 rounded-xl transition duration-200 inline-flex items-center shadow-lg shadow-green-200">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                            Download Sertifikat
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="ui-panel rounded-[2rem] p-6 sm:p-8">
            <div class="text-center">
                <a href="{{ route('home.index') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                    </svg>
                    Kembali ke Beranda
                </a>

            </div>
        </div>

        <!-- Tips -->
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-100 rounded-2xl p-4 sm:p-6 mt-6">
            <h3 class="text-lg font-semibold text-yellow-800 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                Tips Penting
            </h3>
            <ul class="text-sm sm:text-base text-yellow-700 space-y-2">
                <li class="flex items-start">
                    <span class="text-yellow-600 mr-2 mt-1">•</span>
                    <span>Kode unik akan berubah setiap kali Anda memperbarui</span>
                </li>
                <li class="flex items-start">
                    <span class="text-yellow-600 mr-2 mt-1">•</span>
                    <span>Pastikan QR Code terlihat jelas saat di-scan oleh Pemandu</span>
                </li>
                <li class="flex items-start">
                    <span class="text-yellow-600 mr-2 mt-1">•</span>
                    <span>Jika QR Code tidak bisa di-scan, gunakan kode unik untuk input manual</span>
                </li>
                <li class="flex items-start">
                    <span class="text-yellow-600 mr-2 mt-1">•</span>
                    <span>Simpan halaman ini atau screenshot untuk referensi</span>
                </li>
            </ul>
        </div>
    </div>

    <script>
        let qrCanvas = null; // Simpan referensi canvas untuk unduh

        // Function untuk unduh QR Code
        function downloadQRCode() {
            const downloadButton = document.getElementById('download-qr');
            if (downloadButton) {
                downloadButton.disabled = true;
                downloadButton.classList.add('is-loading');
                downloadButton.setAttribute('aria-busy', 'true');
                downloadButton.innerHTML = '<span class="ui-spinner" aria-hidden="true"></span><span>Menyiapkan...</span>';
            }

            if (!qrCanvas) {
                alert('QR Code belum siap. Silakan tunggu sebentar.');
                if (downloadButton) {
                    downloadButton.disabled = false;
                    downloadButton.classList.remove('is-loading');
                    downloadButton.removeAttribute('aria-busy');
                    downloadButton.innerHTML = '<svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>Unduh QR Code';
                }
                return;
            }

            // Buat link download
            const link = document.createElement('a');
            link.download = 'QR-Code-{{ $student->student_id }}-{{ $student->name }}.png';
            link.href = qrCanvas.toDataURL('image/png');

            // Trigger download
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            if (downloadButton) {
                downloadButton.disabled = false;
                downloadButton.classList.remove('is-loading');
                downloadButton.removeAttribute('aria-busy');
                downloadButton.innerHTML = '<svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>Unduh QR Code';
            }
        }

        // Generate QR code saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const qrData = @json($rawBarcode);
            const qrContainer = document.getElementById('qr-code-container');

            if (qrContainer && qrData) {
                // Create canvas for QR code
                const canvas = document.createElement('canvas');
                qrContainer.appendChild(canvas);

                try {
                    QRCode.toCanvas(canvas, qrData, {
                        width: 300,
                        height: 300,
                        margin: 2,
                        color: {
                            dark: '#000000',
                            light: '#FFFFFF'
                        }
                    }, function (error) {
                        if (error) {
                            console.error('Error generating QR code:', error);
                            qrContainer.innerHTML = '<p class="text-red-500">Error generating QR code</p>';

                            // Tampilkan SweetAlert2 untuk QR code invalid
                            Swal.fire({
                                icon: 'error',
                                title: 'QR Code Invalid!',
                                text: 'QR Code tidak dapat dibuat. Silakan gunakan kode unik untuk presensi manual.',
                                confirmButtonColor: '#3b82f6',
                                confirmButtonText: 'Mengerti'
                            });
                        } else {
                            qrCanvas = canvas; // Simpan canvas untuk unduh
                        }
                    });
                } catch (error) {
                    console.error('Error generating QR code:', error);
                    qrContainer.innerHTML = '<p class="text-red-500">Error generating QR code</p>';

                    // Tampilkan SweetAlert2 untuk QR code invalid
                    Swal.fire({
                        icon: 'error',
                        title: 'QR Code Invalid!',
                        text: 'QR Code tidak dapat dibuat. Silakan gunakan kode unik untuk presensi manual.',
                        confirmButtonColor: '#3b82f6',
                        confirmButtonText: 'Mengerti'
                    });
                }
            }
        });
    </script>

    <!-- QR Code Library -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.2.2/build/qrcode.min.js"></script>
@endsection
