@extends('layouts.home')

@section('title', 'Sistem Presensi')

@section('content')
    <div class="home-layout container mx-auto px-4 py-8 sm:py-12 max-w-md lg:max-w-6xl relative z-10">
        <div class="home-hero-column">
        <!-- Header Card -->
        <div class="home-hero relative overflow-hidden rounded-[2rem] shadow-xl p-6 sm:p-10 mb-6 text-slate-800">
            <div class="text-center">
                {{-- <div class="w-20 h-20 sm:w-24 sm:h-24 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 sm:w-12 sm:h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 11h8V3H3v8zm2-6h4v4H5V5zM3 21h8v-8H3v8zm2-6h4v4H5v-4zM13 3v8h8V3h-8zm6 6h-4V5h4v4zM13 13h2v2h-2v-2zM15 15h2v2h-2v-2zM13 17h2v2h-2v-2zM15 19h2v2h-2v-2zM17 13h2v2h-2v-2zM19 15h2v2h-2v-2zM17 17h2v2h-2v-2zM19 19h2v2h-2v-2z"/>
                    </svg>
                </div> --}}
                <p class="uppercase tracking-[.24em] text-xs font-bold text-slate-500 mb-3">Presensi digital MASTAMARU</p>
                <h1 class="text-2xl sm:text-4xl font-bold mb-2">Selamat Datang Peserta</h1>
                <p class="text-slate-600 text-sm sm:text-base">Sistem Presensi {{ config('app.name') }}</p>
            </div>
        </div>
        </div>

        <!-- Form Input NIM -->
        <div class="home-action-column">
        <div class="ui-panel rounded-[2rem] p-6 sm:p-10 mb-6">
            <div class="text-center mb-6">
                <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-2">Dapatkan QR Code Presensi</h2>
                <p class="text-gray-600 text-sm sm:text-base">Masukkan NIM Anda untuk mendapatkan QR Code dan kode unik presensi</p>
            </div>

            <form action="{{ route('home.check-student') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="student_id" class="block text-sm sm:text-base font-medium text-gray-700 mb-2">
                        <svg class="w-5 h-5 inline-block mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                        Nomor Induk Mahasiswa (NIM)
                    </label>
                    <input type="text"
                           id="student_id"
                           name="student_id"
                           value="{{ old('student_id') }}"
                           class="w-full px-4 py-3 sm:py-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-center text-lg sm:text-xl font-mono tracking-wider"
                           placeholder="Contoh: 24020304"
                           required
                           autocomplete="off">
                    @error('student_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                         class="w-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-semibold py-3 sm:py-4 px-6 rounded-xl transition duration-300 transform hover:-translate-y-1 shadow-lg shadow-red-200">
                    <svg class="w-5 h-5 inline-block mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                    </svg>
                     Cek NIM
                 </button>
            </form>
        </div>

        <!-- Informasi Tambahan -->
        <div class="home-info-panel border border-blue-100 rounded-2xl p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-blue-800 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                Informasi Penting
            </h3>
            <ul class="text-sm sm:text-base text-blue-700 space-y-2">
                <li class="flex items-start">
                    <span class="text-blue-600 mr-2 mt-1">•</span>
                    <span>Pastikan NIM yang Anda masukkan sudah terdaftar dalam sistem</span>
                </li>
                <li class="flex items-start">
                    <span class="text-blue-600 mr-2 mt-1">•</span>
                    <span>Kode unik akan berubah setiap kali Anda mengakses halaman ini</span>
                </li>
                <li class="flex items-start">
                    <span class="text-blue-600 mr-2 mt-1">•</span>
                    <span>Gunakan QR Code atau kode unik untuk melakukan presensi kepada Pemandu</span>
                </li>
                <li class="flex items-start">
                    <span class="text-blue-600 mr-2 mt-1">•</span>
                    <span>Jika mengalami masalah, hubungi Pemandu atau Panitia</span>
                </li>
            </ul>
        </div>
        </div>
    </div>

    <style>
        .home-hero {
            background: linear-gradient(135deg, #eaf4ff 0%, #f3efff 55%, #e9faf5 100%);
            border: 1px solid rgba(155, 173, 204, .22);
            isolation: isolate;
            min-height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .home-layout {
            width: calc(100% - 32px);
            max-width: 1180px;
            margin-left: auto;
            margin-right: auto;
        }

        .home-hero-column { min-height: 520px; min-width: 0; }
        .home-action-column { min-width: 0; }
        .home-action-column .ui-panel { box-shadow: 0 22px 60px rgba(75, 58, 146, .12); }

        @media (min-width: 1024px) {
            .home-layout {
                display: grid;
                grid-template-columns: minmax(360px, .85fr) minmax(0, 1.15fr);
                gap: 28px;
                align-items: stretch;
            }
            .home-hero-column { display: flex; }
            .home-hero-column .home-hero { width: 100%; margin-bottom: 0; }
        }

        @media (min-width: 641px) and (max-width: 1023px) {
            .home-layout { max-width: 720px; }
            .home-hero-column { min-height: 0; }
            .home-hero { min-height: 280px; }
            .home-action-column { width: 100%; }
        }

        .home-hero::before {
            content: '';
            position: absolute;
            width: 175px;
            height: 175px;
            right: -52px;
            top: -58px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .48);
            border: 18px solid rgba(255, 255, 255, .42);
            z-index: -1;
        }

        .home-info-panel {
            background: linear-gradient(135deg, #eff7ff 0%, #f8fbff 100%);
            box-shadow: 0 12px 30px rgba(15, 76, 154, .08);
        }

        @media (max-width: 640px) {
            .home-layout { width: calc(100% - 24px); }
            .home-hero { border-radius: 1.65rem; }
            .home-hero-column { min-height: 0; }
        }
    </style>

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


@endsection
