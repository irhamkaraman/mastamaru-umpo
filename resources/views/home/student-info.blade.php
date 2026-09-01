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
                            <span class="text-sm font-medium text-gray-600">No. Telp Peserta:</span>
                            <span class="text-sm font-semibold text-gray-800">{{ $student->phone_number ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">Kelompok:</span>
                            <span class="text-sm font-semibold text-gray-800">{{ $student->group->name }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">Pemandu:</span>
                            <span class="text-sm font-semibold text-gray-800">{{ $student->mentor ? $student->mentor->name : 'Belum ditentukan' }}</span>
                        </div>
                        @if ($student->mentor)
                        <div class="flex justify-between items-center p-3 bg-green-50/70 border border-green-200 rounded-lg">
                            <span class="text-sm font-medium text-green-800 flex items-center">
                                <svg class="w-4 h-4 mr-1.5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.316 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.818-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                </svg>
                                No. WA Pemandu:
                            </span>
                            <div class="flex items-center gap-2">
                                @if ($student->mentor->phone_number)
                                    <span class="text-sm font-bold text-green-900">{{ $student->mentor->phone_number }}</span>
                                    @php
                                        $cleanPhone = preg_replace('/[^0-9]/', '', $student->mentor->phone_number);
                                        if (str_starts_with($cleanPhone, '0')) {
                                            $cleanPhone = '62' . substr($cleanPhone, 1);
                                        }
                                    @endphp
                                    <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="inline-flex items-center justify-center p-1.5 bg-green-600 hover:bg-green-700 text-white rounded-md text-xs transition shadow-sm" title="Hubungi via WhatsApp">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.316 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.818-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                        </svg>
                                    </a>
                                @else
                                    <span class="text-sm font-semibold text-gray-500 italic">Belum ditambahkan</span>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Matriks Poin & Predikat Penilaian Kegiatan -->
        @if(isset($matrix) && isset($assessment))
        <div class="grid grid-cols-1 gap-6 mb-6">
            <div class="ui-panel rounded-[2rem] p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 border-b border-gray-100 pb-4">
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Rekapitulasi Poin & Nilai Kehadiran
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">Perhitungan poin presensi Datang & Pulang selama 5 hari kegiatan.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($student->status === 'lulus')
                            <div class="px-6 py-3 bg-green-50 border border-green-200 rounded-xl text-center w-full shadow-sm">
                                <span class="text-xs text-green-600 font-bold block uppercase tracking-wider mb-1">Status Kelulusan</span>
                                <span class="text-2xl font-black text-green-700">LULUS</span>
                            </div>
                        @elseif($student->status === 'gagal')
                            <div class="px-6 py-3 bg-red-50 border border-red-200 rounded-xl text-center w-full shadow-sm">
                                <span class="text-xs text-red-600 font-bold block uppercase tracking-wider mb-1">Status Kelulusan</span>
                                <span class="text-2xl font-black text-red-700">GAGAL</span>
                            </div>
                        @else
                            <div class="px-4 py-2 bg-indigo-50 border border-indigo-100 rounded-xl text-center">
                                <span class="text-[11px] text-indigo-600 font-medium block">Total Poin</span>
                                <span class="text-lg font-black text-indigo-700">{{ $matrix['total_points'] }} / 100</span>
                            </div>
                            <div class="px-4 py-2 bg-purple-50 border border-purple-100 rounded-xl text-center">
                                <span class="text-[11px] text-purple-600 font-medium block">Predikat</span>
                                <span class="text-lg font-black text-purple-700">{{ $assessment->grade }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Matriks Tabel 5 Hari -->
                <div class="overflow-x-auto rounded-xl border border-gray-200 mb-6">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-gray-50 text-gray-700 font-semibold border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3">Hari</th>
                                <th class="px-4 py-3">Sesi Datang</th>
                                <th class="px-4 py-3">Sesi Materi</th>
                                <th class="px-4 py-3">Sesi Pulang</th>
                                <th class="px-4 py-3 text-right">Total Poin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @php
                                $participantDays = collect($matrix['days'])->filter(function($d) {
                                    return $d['datang']['submission'] !== null || $d['materi']['submission'] !== null || $d['pulang']['submission'] !== null;
                                });
                            @endphp
                            
                            @forelse($participantDays as $dayNum => $d)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="px-4 py-3 font-bold text-gray-900">Hari {{ $dayNum }}</td>
                                    <td class="px-4 py-3">
                                        @if($d['datang']['submission'])
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800">
                                                {{ $d['datang']['status'] }} ({{ $d['datang']['points'] }}p)
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Belum Hadir</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($d['materi']['submission'])
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-800">
                                                {{ $d['materi']['status'] }} ({{ $d['materi']['points'] }}p)
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Belum Hadir</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($d['pulang']['submission'])
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800">
                                                {{ $d['pulang']['status'] }} ({{ $d['pulang']['points'] }}p)
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Belum Hadir</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-indigo-600">
                                        {{ $d['total'] }} Poin
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-gray-500 text-sm">
                                        Belum ada riwayat presensi yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Petunjuk Predikat -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center text-xs">
                    <div class="p-2 rounded-lg bg-gray-50 border {{ $assessment->grade === 'A' ? 'border-indigo-400 bg-indigo-50/50 font-bold' : 'border-gray-200' }}">
                        <span class="text-gray-600 block">90 - 100</span>
                        <span class="font-bold text-indigo-700">A (Sangat Baik)</span>
                    </div>
                    <div class="p-2 rounded-lg bg-gray-50 border {{ $assessment->grade === 'B' ? 'border-indigo-400 bg-indigo-50/50 font-bold' : 'border-gray-200' }}">
                        <span class="text-gray-600 block">80 - 89</span>
                        <span class="font-bold text-indigo-700">B (Baik)</span>
                    </div>
                    <div class="p-2 rounded-lg bg-gray-50 border {{ $assessment->grade === 'C' ? 'border-indigo-400 bg-indigo-50/50 font-bold' : 'border-gray-200' }}">
                        <span class="text-gray-600 block">70 - 79</span>
                        <span class="font-bold text-indigo-700">C (Cukup)</span>
                    </div>
                    <div class="p-2 rounded-lg bg-gray-50 border {{ $assessment->grade === 'D' ? 'border-red-400 bg-red-50/50 font-bold' : 'border-gray-200' }}">
                        <span class="text-gray-600 block">< 70</span>
                        <span class="font-bold text-red-600">D (Perlu Ditingkatkan)</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Sertifikat Section (Full Width Grid) -->
        <div class="grid grid-cols-1 gap-6 mb-6">
            @if($student->status === 'lulus' && isset($certificateUrl) && $certificateUrl)
            <div class="ui-panel rounded-[2rem] p-6 sm:p-8 border-2 border-green-200">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zM6.293 9.293a1 1 0 011.414 0L10 11.586l2.293-2.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    Sertifikat Kelulusan
                </h3>
                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-green-700 mb-4">Selamat! Anda dinyatakan <strong class="uppercase">LULUS</strong> MASTAMARU 2026. Sertifikat kegiatan Anda telah diterbitkan.</p>
                    
                    <div class="mb-6 flex justify-center">
                        <div class="bg-white p-6 rounded-2xl shadow-md border border-green-100 flex flex-col items-center justify-center max-w-sm w-full">
                            <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM17 20H7v-2h10v2zm0-4H7v-2h10v2zm-3-4H7v-2h7v2z" />
                                </svg>
                            </div>
                            <h4 class="font-bold text-gray-800 text-lg mb-1">Dokumen Sertifikat</h4>
                            <p class="text-sm text-gray-500 mb-4 text-center">Format Dokumen PDF (.pdf)</p>
                        </div>
                    </div>

                    <a href="{{ $certificateUrl }}" download class="bg-gradient-to-r from-green-600 to-emerald-500 hover:from-green-700 hover:to-emerald-600 text-white font-medium py-2 px-6 rounded-xl transition duration-200 inline-flex items-center shadow-lg shadow-green-200">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        Download Sertifikat
                    </a>
                </div>
            </div>
            @elseif($student->status === 'gagal')
            <div class="ui-panel rounded-[2rem] p-6 sm:p-8 border-2 border-red-200 bg-red-50">
                <h3 class="text-lg sm:text-xl font-semibold text-red-800 mb-4 flex items-center justify-center">
                    <svg class="w-6 h-6 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    Informasi Kelulusan
                </h3>
                <div class="text-center">
                    <p class="text-base text-red-700 font-medium mb-2">Mohon maaf, Anda dinyatakan <strong>TIDAK LULUS</strong> MASTAMARU 2026.</p>
                    <p class="text-sm text-red-600">Anda diwajibkan untuk mengikuti kembali kegiatan MASTAMARU pada tahun 2027 mendatang.</p>
                </div>
            </div>
            @else
            <div class="ui-panel rounded-[2rem] p-6 sm:p-8 border-2 border-amber-200 bg-amber-50">
                <h3 class="text-lg sm:text-xl font-semibold text-amber-800 mb-2 flex items-center justify-center">
                    <svg class="w-6 h-6 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Sertifikat Sedang Diproses
                </h3>
                <div class="text-center">
                    <p class="text-sm text-amber-700">Rangkaian acara belum selesai atau data kelulusan Anda sedang dalam proses rekapitulasi oleh panitia. Sertifikat akan muncul di sini setelah Anda dinyatakan lulus.</p>
                </div>
            </div>
            @endif
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
    <script src="{{ asset('vendor/qrcode.min.js') }}?v=1.0"></script>
@endsection
