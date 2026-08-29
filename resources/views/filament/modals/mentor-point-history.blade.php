<div class="space-y-6">
    <div class="bg-primary-50 dark:bg-primary-950/40 border border-primary-200 dark:border-primary-800 p-4 rounded-2xl flex items-center justify-between">
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">{{ $mentor->name }} (NIM: {{ $mentor->student_id }})</h4>
            <p class="text-xs text-gray-500">Kelompok: {{ $mentor->group->name ?? '-' }} &bull; Total Peserta: {{ $participants->count() }} orang</p>
        </div>
        <div class="text-right">
            <span class="text-xs text-primary-600 dark:text-primary-400 font-medium block">Total Poin Peserta</span>
            <span class="text-2xl font-black text-primary-700 dark:text-primary-300">
                {{ $participants->sum(function($p) { return $p->attendanceSubmissions->sum('score_points'); }) }} Poin
            </span>
        </div>
    </div>

    @if($participants->isEmpty())
        <div class="p-8 text-center text-sm text-gray-500 border border-dashed rounded-xl border-gray-300 dark:border-gray-700">
            Belum ada peserta di kelompok ini.
        </div>
    @else
        <div class="space-y-4">
            @foreach($participants as $participant)
                @php
                    $totalPoints = $participant->attendanceSubmissions->sum('score_points');
                    $submissionsCount = $participant->attendanceSubmissions->count();
                @endphp
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden bg-white dark:bg-gray-900">
                    <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 flex items-center justify-between border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <div class="font-bold text-gray-900 dark:text-white">{{ $participant->name }}</div>
                            <div class="text-xs text-gray-500 font-mono">{{ $participant->student_id }} &bull; Total Presensi: {{ $submissionsCount }} sesi</div>
                        </div>
                        <div class="text-right flex items-center gap-3">
                            <div class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ $totalPoints }} Poin</div>
                            <div>
                                @if($participant->status === 'lulus')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800">Lulus</span>
                                @elseif($participant->status === 'gagal')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-800">Gagal</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-yellow-100 text-yellow-800">Proses</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @if($submissionsCount > 0)
                        <div class="p-0">
                            <table class="w-full text-xs text-left">
                                <thead class="bg-gray-50/50 dark:bg-gray-800/50 text-gray-500">
                                    <tr>
                                        <th class="px-4 py-2 font-medium">Sesi</th>
                                        <th class="px-4 py-2 font-medium">Waktu</th>
                                        <th class="px-4 py-2 font-medium">Status</th>
                                        <th class="px-4 py-2 font-medium">Poin</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @foreach($participant->attendanceSubmissions as $sub)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40">
                                            <td class="px-4 py-2">{{ $sub->presenceSession->session_name ?? '-' }}</td>
                                            <td class="px-4 py-2 font-mono text-gray-500">{{ $sub->submitted_at ? $sub->submitted_at->format('d/m/Y H:i') : '-' }}</td>
                                            <td class="px-4 py-2">
                                                <span class="text-[10px] uppercase font-semibold text-gray-600">{{ $sub->status }}</span>
                                            </td>
                                            <td class="px-4 py-2 font-bold text-primary-600 dark:text-primary-400">+{{ $sub->score_points }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="px-4 py-3 text-xs text-gray-400 text-center italic">
                            Belum ada riwayat presensi
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
