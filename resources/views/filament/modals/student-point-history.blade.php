<div class="space-y-6">
    <!-- Header Summary Card -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-primary-50 dark:bg-primary-950/40 border border-primary-200 dark:border-primary-800 p-4 rounded-2xl text-center">
            <span class="text-xs text-primary-600 dark:text-primary-400 font-medium block">Total Poin Kehadiran</span>
            <span class="text-2xl font-black text-primary-700 dark:text-primary-300">{{ $matrix['total_points'] }}</span>
        </div>
        <div class="bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 p-4 rounded-2xl text-center">
            <span class="text-xs text-blue-600 dark:text-blue-400 font-medium block">Nilai Kehadiran</span>
            <span class="text-2xl font-black text-blue-700 dark:text-blue-300">{{ $assessment->attendance_score }}%</span>
        </div>
        <div class="bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800 p-4 rounded-2xl text-center">
            <span class="text-xs text-purple-600 dark:text-purple-400 font-medium block">Predikat Nilai</span>
            <span class="text-2xl font-black text-purple-700 dark:text-purple-300">{{ $assessment->grade }}</span>
        </div>
        <div class="p-4 rounded-2xl text-center border {{ $assessment->status === 'lulus' ? 'bg-success-50 dark:bg-success-950/40 border-success-200 dark:border-success-800' : 'bg-danger-50 dark:bg-danger-950/40 border-danger-200 dark:border-danger-800' }}">
            <span class="text-xs font-medium block {{ $assessment->status === 'lulus' ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">Status Kelulusan</span>
            <span class="text-2xl font-black {{ $assessment->status === 'lulus' ? 'text-success-700 dark:text-success-300' : 'text-danger-700 dark:text-danger-300' }}">{{ strtoupper($assessment->status) }}</span>
        </div>
    </div>

    <!-- Matriks Presensi Harian (Tabel PDF) -->
    <div>
        <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            Matriks Penilaian Kehadiran 5 Hari Kegiatan
        </h4>
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-2.5 font-semibold">Hari Ke-</th>
                        <th class="px-4 py-2.5 font-semibold">Sesi Datang</th>
                        <th class="px-4 py-2.5 font-semibold">Sesi Pulang</th>
                        <th class="px-4 py-2.5 font-semibold text-center">Total Poin Hari Ini</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                    @foreach($matrix['days'] as $dayNum => $d)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                            <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">Hari {{ $dayNum }}</td>
                            <td class="px-4 py-3">
                                @if($d['datang']['submission'])
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                            {{ $d['datang']['status'] }} (+{{ $d['datang']['points'] }})
                                        </span>
                                        @if($d['datang']['time'])
                                            <span class="text-xs text-gray-400">{{ $d['datang']['time'] }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Belum Hadir / Sesi Belum Ada</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($d['pulang']['submission'])
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                            {{ $d['pulang']['status'] }} (+{{ $d['pulang']['points'] }})
                                        </span>
                                        @if($d['pulang']['time'])
                                            <span class="text-xs text-gray-400">{{ $d['pulang']['time'] }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Belum Hadir / Sesi Belum Ada</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-primary-600 dark:text-primary-400">
                                {{ $d['total'] }} Poin
                            </td>
                        </tr>
                    @endforeach
                    <tr class="bg-gray-50 dark:bg-gray-800/80 font-bold border-t-2 border-gray-300 dark:border-gray-600">
                        <td colspan="3" class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">Akumulasi Total Poin:</td>
                        <td class="px-4 py-3 text-center text-primary-600 dark:text-primary-400 text-base">{{ $matrix['total_points'] }} / 100</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Log Timeline Riwayat Presensi -->
    <div>
        <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Log Riwayat Presensi & Poin Masuk
        </h4>
        @if($submissions->isEmpty())
            <div class="p-6 text-center text-sm text-gray-500 border border-dashed rounded-xl border-gray-300 dark:border-gray-700">
                Belum ada riwayat presensi atau poin yang tercatat untuk peserta ini.
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 max-h-60 overflow-y-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 sticky top-0">
                        <tr>
                            <th class="px-3 py-2">Waktu Presensi</th>
                            <th class="px-3 py-2">Nama Sesi</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Poin</th>
                            <th class="px-3 py-2">Metode</th>
                            <th class="px-3 py-2">Pemandu / Mentor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                        @foreach($submissions as $sub)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40">
                                <td class="px-3 py-2 text-gray-600 dark:text-gray-400 font-mono">{{ $sub->submitted_at ? $sub->submitted_at->format('d/m/Y H:i:s') : '-' }}</td>
                                <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">{{ $sub->presenceSession->session_name ?? '-' }}</td>
                                <td class="px-3 py-2">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-semibold bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200">
                                        {{ ucfirst($sub->status) }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 font-bold text-primary-600 dark:text-primary-400">+{{ $sub->score_points }}</td>
                                <td class="px-3 py-2 text-gray-500">{{ $sub->submission_method }}</td>
                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $sub->mentor->name ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
