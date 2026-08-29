<div class="space-y-6">
    <div class="bg-primary-50 dark:bg-primary-950/40 border border-primary-200 dark:border-primary-800 p-4 rounded-2xl flex items-center justify-between">
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">{{ $mentor->name }} (NIM: {{ $mentor->student_id }})</h4>
            <p class="text-xs text-gray-500">Kelompok: {{ $mentor->group->name ?? '-' }} &bull; Total Presensi Dicatat: {{ $submissions->count() }} kali</p>
        </div>
        <div class="text-right">
            <span class="text-xs text-primary-600 dark:text-primary-400 font-medium block">Total Poin Dikeluarkan</span>
            <span class="text-2xl font-black text-primary-700 dark:text-primary-300">{{ $submissions->sum('score_points') }} Poin</span>
        </div>
    </div>

    @if($submissions->isEmpty())
        <div class="p-8 text-center text-sm text-gray-500 border border-dashed rounded-xl border-gray-300 dark:border-gray-700">
            Mentor ini belum pernah mencatat presensi untuk peserta.
        </div>
    @else
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 max-h-96 overflow-y-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 sticky top-0">
                    <tr>
                        <th class="px-3 py-2">Waktu</th>
                        <th class="px-3 py-2">Nama Peserta</th>
                        <th class="px-3 py-2">NIM</th>
                        <th class="px-3 py-2">Sesi Presensi</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Poin Diberikan</th>
                        <th class="px-3 py-2">Metode</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                    @foreach($submissions as $sub)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40">
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-400 font-mono">{{ $sub->submitted_at ? $sub->submitted_at->format('d/m/Y H:i:s') : '-' }}</td>
                            <td class="px-3 py-2 font-semibold text-gray-900 dark:text-white">{{ $sub->student->name ?? '-' }}</td>
                            <td class="px-3 py-2 font-mono text-gray-600 dark:text-gray-400">{{ $sub->student->student_id ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $sub->presenceSession->session_name ?? '-' }}</td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-semibold bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200">
                                    {{ ucfirst($sub->status) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 font-bold text-primary-600 dark:text-primary-400">+{{ $sub->score_points }}</td>
                            <td class="px-3 py-2 text-gray-500">{{ $sub->submission_method }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
