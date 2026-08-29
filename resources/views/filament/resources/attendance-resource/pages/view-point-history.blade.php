<x-filament-panels::page>
    <style>
        .point-history-page {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
        }
        .dark .point-history-page {
            color: #f1f5f9;
        }

        /* Profile Banner */
        .ph-profile-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1.25rem;
            padding: 1.5rem 1.75rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }
        .ph-profile-left {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }
        .ph-avatar {
            width: 3.75rem;
            height: 3.75rem;
            border-radius: 0.75rem;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            font-weight: 800;
            color: #ffffff;
        }
        .ph-info h2 {
            font-size: 1.35rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.02em;
            color: #ffffff;
        }
        .ph-info-meta {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 0.35rem;
            font-size: 0.85rem;
            color: #c7d2fe;
            flex-wrap: wrap;
        }
        .ph-badge-inline {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.2rem 0.6rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.75rem;
            color: #ffffff;
        }
        .ph-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.6rem 1.25rem;
            border-radius: 0.75rem;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.2s;
        }
        .ph-back-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateX(-2px);
            color: #ffffff;
        }

        /* Stat Cards Grid */
        .ph-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }
        @media (max-width: 768px) {
            .ph-stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        .ph-stat-card {
            padding: 1.25rem;
            border-radius: 1rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }
        .dark .ph-stat-card {
            background: #18181b;
            border: 1px solid #27272a;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
        }
        .ph-stat-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
        }
        .dark .ph-stat-title {
            color: #a1a1aa;
        }
        .ph-stat-number {
            font-size: 2rem;
            font-weight: 900;
            line-height: 1.1;
        }
        .ph-stat-desc {
            font-size: 0.75rem;
            color: #94a3b8;
        }
        .dark .ph-stat-desc {
            color: #71717a;
        }

        /* Sections */
        .ph-section-card {
            background: #ffffff;
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        }
        .dark .ph-section-card {
            background: #18181b;
            border: 1px solid #27272a;
        }
        .ph-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .dark .ph-section-header {
            border-bottom-color: #27272a;
        }
        .ph-section-title {
            font-size: 1.05rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            color: #0f172a;
            margin: 0;
        }
        .dark .ph-section-title {
            color: #ffffff;
        }

        /* Matrix Table */
        .ph-table-wrapper {
            overflow-x: auto;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
        }
        .dark .ph-table-wrapper {
            border-color: #27272a;
        }
        .ph-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
            text-align: left;
        }
        .ph-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .dark .ph-table th {
            background: #202023;
            color: #e4e4e7;
            border-bottom: 1px solid #27272a;
        }
        .ph-table td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
        }
        .dark .ph-table td {
            border-bottom: 1px solid #27272a;
            color: #e4e4e7;
        }
        .ph-table tr:last-child td {
            border-bottom: none;
        }

        /* Matrix Total Row */
        .ph-table-total-row {
            background: #f8fafc;
            font-weight: 800;
        }
        .dark .ph-table-total-row {
            background: #202023;
        }
        .dark .ph-table-total-row td {
            color: #ffffff;
        }

        .ph-status-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.3rem 0.65rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .ph-tag-hadir { background: #dcfce7; color: #15803d; }
        .dark .ph-tag-hadir { background: rgba(34, 197, 94, 0.2); color: #4ade80; }
        .ph-tag-terlambat { background: #fef9c3; color: #854d0e; }
        .dark .ph-tag-terlambat { background: rgba(234, 179, 8, 0.2); color: #facc15; }
        .ph-tag-izin { background: #e0f2fe; color: #0369a1; }
        .dark .ph-tag-izin { background: rgba(14, 165, 233, 0.2); color: #38bdf8; }
        .ph-tag-sakit { background: #ffedd5; color: #9a3412; }
        .dark .ph-tag-sakit { background: rgba(249, 115, 22, 0.2); color: #fb923c; }
        .ph-tag-empty { color: #94a3b8; font-style: italic; font-size: 0.8rem; }
        .dark .ph-tag-empty { color: #71717a; }

        /* Timeline Items */
        .ph-timeline {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .ph-timeline-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.85rem 1.15rem;
            border-radius: 0.75rem;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
        }
        .dark .ph-timeline-item {
            background: #202023;
            border-color: #27272a;
        }
        .ph-tl-time {
            font-size: 0.75rem;
            font-family: ui-monospace, monospace;
            color: #64748b;
        }
        .dark .ph-tl-time {
            color: #a1a1aa;
        }
        .ph-tl-title {
            font-weight: 700;
            font-size: 0.875rem;
            color: #0f172a;
        }
        .dark .ph-tl-title {
            color: #ffffff;
        }
        .ph-tl-mentor {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.15rem;
        }
        .dark .ph-tl-mentor {
            color: #a1a1aa;
        }
    </style>

    <div class="point-history-page">
        <!-- 1. Profile Banner -->
        <div class="ph-profile-banner">
            <div class="ph-profile-left">
                <div class="ph-avatar">
                    {{ strtoupper(substr($record->name, 0, 2)) }}
                </div>
                <div class="ph-info">
                    <h2>{{ $record->name }}</h2>
                    <div class="ph-info-meta">
                        <span class="ph-badge-inline">NIM: {{ $record->student_id }}</span>
                        <span>Fakultas: {{ $record->faculty ?? '-' }}</span>
                        <span>&bull;</span>
                        <span>Prodi: {{ $record->study_program ?? '-' }}</span>
                        <span>&bull;</span>
                        <span>Kelompok: {{ $record->group->name ?? 'Belum ada' }}</span>
                        <span>&bull;</span>
                        <span>Pemandu: {{ $record->mentor->name ?? '-' }}</span>
                        @if($record->phone_number)
                            <span>&bull;</span>
                            <span style="color:#86efac;font-weight:700;">WA: {{ $record->phone_number }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div>
                <a href="{{ \App\Filament\Resources\AttendanceResource::getUrl('index') }}" class="ph-back-btn">
                    <svg style="width:1.15rem;height:1.15rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali ke Daftar Peserta</span>
                </a>
            </div>
        </div>

        <!-- 2. Stat Cards Grid (Clean, Tanpa Border Kiri Tebal) -->
        <div class="ph-stats-grid">
            <div class="ph-stat-card">
                <span class="ph-stat-title">Total Poin Kehadiran</span>
                <span class="ph-stat-number" style="color:#6366f1;">
                    {{ $matrix['total_points'] }} <span style="font-size:1rem;color:#94a3b8;font-weight:600;">/ 100</span>
                </span>
                <span class="ph-stat-desc">Akumulasi 5 Hari Kegiatan</span>
            </div>
            <div class="ph-stat-card">
                <span class="ph-stat-title">Nilai Kehadiran</span>
                <span class="ph-stat-number" style="color:#0ea5e9;">{{ $assessment->attendance_score }}%</span>
                <span class="ph-stat-desc">Persentase Kehadiran Sesi</span>
            </div>
            <div class="ph-stat-card">
                <span class="ph-stat-title">Predikat Nilai</span>
                <span class="ph-stat-number" style="color:#a855f7;">{{ $assessment->grade }}</span>
                <span class="ph-stat-desc">
                    @if($assessment->grade === 'A') Sangat Baik (90-100)
                    @elseif($assessment->grade === 'B') Baik (80-89)
                    @elseif($assessment->grade === 'C') Cukup (70-79)
                    @else Perlu Ditingkatkan (<70)
                    @endif
                </span>
            </div>
            <div class="ph-stat-card">
                <span class="ph-stat-title">Status Kelulusan</span>
                <span class="ph-stat-number" style="color: {{ $assessment->status === 'lulus' ? '#22c55e' : '#ef4444' }};">
                    {{ strtoupper($assessment->status) }}
                </span>
                <span class="ph-stat-desc">Berdasarkan Capaian Poin</span>
            </div>
        </div>

        <!-- 3. Matriks 5 Hari Kegiatan -->
        <div class="ph-section-card">
            <div class="ph-section-header">
                <h3 class="ph-section-title">
                    <svg style="width:1.35rem;height:1.35rem;color:#6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Matriks Penilaian Kehadiran
                </h3>
            </div>

            <div class="ph-table-wrapper">
                <table class="ph-table">
                    <thead>
                        <tr>
                            <th style="width:15%;">Hari Ke-</th>
                            <th style="width:35%;">Sesi Datang (Bobot Maks: 10)</th>
                            <th style="width:35%;">Sesi Pulang (Bobot Maks: 10)</th>
                            <th style="width:15%;text-align:right;">Total Poin Hari Ini</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($matrix['days'] as $dayNum => $d)
                            <tr>
                                <td style="font-weight:800;">Hari {{ $dayNum }}</td>
                                <td>
                                    @if($d['datang']['submission'])
                                        <span class="ph-status-tag {{ $d['datang']['status'] === 'terlambat' ? 'ph-tag-terlambat' : ($d['datang']['status'] === 'izin' ? 'ph-tag-izin' : ($d['datang']['status'] === 'sakit' ? 'ph-tag-sakit' : 'ph-tag-hadir')) }}">
                                            {{ ucfirst($d['datang']['status']) }} (+{{ $d['datang']['points'] }} Poin)
                                        </span>
                                        @if($d['datang']['time'])
                                            <span style="font-size:0.75rem;color:#94a3b8;margin-left:0.35rem;">({{ $d['datang']['time'] }})</span>
                                        @endif
                                    @else
                                        <span class="ph-tag-empty">Belum Hadir / Sesi Belum Ada</span>
                                    @endif
                                </td>
                                <td>
                                    @if($d['pulang']['submission'])
                                        <span class="ph-status-tag {{ $d['pulang']['status'] === 'izin' ? 'ph-tag-izin' : ($d['pulang']['status'] === 'sakit' ? 'ph-tag-sakit' : 'ph-tag-hadir') }}">
                                            {{ ucfirst($d['pulang']['status']) }} (+{{ $d['pulang']['points'] }} Poin)
                                        </span>
                                        @if($d['pulang']['time'])
                                            <span style="font-size:0.75rem;color:#94a3b8;margin-left:0.35rem;">({{ $d['pulang']['time'] }})</span>
                                        @endif
                                    @else
                                        <span class="ph-tag-empty">Belum Hadir / Sesi Belum Ada</span>
                                    @endif
                                </td>
                                <td style="text-align:right;font-weight:800;font-size:1rem;color:#6366f1;">
                                    {{ $d['total'] }} Poin
                                </td>
                            </tr>
                        @endforeach
                        <tr class="ph-table-total-row">
                            <td colspan="3" style="text-align:right;font-size:0.95rem;">Akumulasi Total Poin:</td>
                            <td style="text-align:right;font-size:1.15rem;color:#6366f1;">{{ $matrix['total_points'] }} / 100</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. Timeline Log Riwayat Presensi -->
        <div class="ph-section-card">
            <div class="ph-section-header">
                <h3 class="ph-section-title">
                    <svg style="width:1.35rem;height:1.35rem;color:#0ea5e9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Log Riwayat Presensi & Poin Masuk
                </h3>
                <span style="font-size:0.8rem;color:#94a3b8;">Total Tercatat: {{ $submissions->count() }} aktivitas</span>
            </div>

            @if($submissions->isEmpty())
                <div style="text-align:center;padding:2.5rem;color:#94a3b8;border:2px dashed #e2e8f0;border-radius:0.75rem;" class="dark:border-zinc-800 dark:text-zinc-500">
                    Belum ada riwayat presensi yang tercatat untuk peserta ini.
                </div>
            @else
                <div class="ph-timeline">
                    @foreach($submissions as $sub)
                        <div class="ph-timeline-item">
                            <div>
                                <div class="ph-tl-title">{{ $sub->presenceSession->session_name ?? 'Sesi Presensi' }}</div>
                                <div class="ph-tl-mentor">
                                    Dicatat oleh: <strong style="color:#6366f1;">{{ $sub->mentor->name ?? 'Admin' }}</strong> &bull; Metode: {{ $sub->submission_method }}
                                </div>
                            </div>
                            <div style="display:flex;align-items:center;gap:1rem;">
                                <div style="text-align:right;">
                                    <span class="ph-status-tag {{ $sub->status === 'terlambat' ? 'ph-tag-terlambat' : ($sub->status === 'izin' ? 'ph-tag-izin' : ($sub->status === 'sakit' ? 'ph-tag-sakit' : 'ph-tag-hadir')) }}">
                                        {{ ucfirst($sub->status) }}
                                    </span>
                                    <div class="ph-tl-time" style="margin-top:0.25rem;">
                                        {{ $sub->submitted_at ? $sub->submitted_at->format('d M Y - H:i:s') : '-' }}
                                    </div>
                                </div>
                                <div style="background:#eef2ff;color:#4338ca;padding:0.4rem 0.75rem;border-radius:0.5rem;font-weight:800;font-size:0.9rem;" class="dark:bg-indigo-950/60 dark:text-indigo-300 dark:border dark:border-indigo-800/40">
                                    +{{ $sub->score_points }} Poin
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
