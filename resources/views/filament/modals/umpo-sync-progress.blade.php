<div class="umpo-sync-container" x-data="umpoSyncModal()" x-init="init()">
    <style>
        .umpo-sync-container {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            padding: 0.25rem;
        }
        .dark .umpo-sync-container {
            color: #f1f5f9;
        }
        .umpo-header-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            border-radius: 1rem;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
        }
        .dark .umpo-header-card {
            background: rgba(22, 101, 52, 0.2);
            border-color: rgba(34, 197, 94, 0.3);
        }
        .umpo-header-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 0.75rem;
            background: #16a34a;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);
        }
        .umpo-spin {
            animation: umpo-spinner 1s linear infinite;
        }
        @keyframes umpo-spinner {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .umpo-header-title {
            font-size: 1rem;
            font-weight: 700;
            margin: 0;
            color: #0f172a;
        }
        .dark .umpo-header-title {
            color: #ffffff;
        }
        .umpo-header-sub {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.25rem;
        }
        .dark .umpo-header-sub {
            color: #94a3b8;
        }
        .umpo-badge {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #475569;
        }
        .dark .umpo-badge {
            background: #1e293b;
            border-color: #334155;
            color: #cbd5e1;
        }
        .umpo-badge.running {
            background: #dcfce7;
            color: #15803d;
            border-color: #86efac;
        }
        .dark .umpo-badge.running {
            background: rgba(34, 197, 94, 0.25);
            color: #4ade80;
            border-color: rgba(34, 197, 94, 0.4);
        }
        .umpo-badge.success {
            background: #dcfce7;
            color: #166534;
            border-color: #4ade80;
        }
        .umpo-grid-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.75rem;
        }
        @media (max-width: 640px) {
            .umpo-grid-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        .umpo-stat-card {
            padding: 0.85rem 0.5rem;
            border-radius: 0.75rem;
            text-align: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .dark .umpo-stat-card {
            background: #1e293b;
            border-color: #334155;
        }
        .umpo-stat-label {
            font-size: 0.7rem;
            font-weight: 600;
            color: #64748b;
            display: block;
            margin-bottom: 0.25rem;
        }
        .dark .umpo-stat-label {
            color: #94a3b8;
        }
        .umpo-stat-value {
            font-size: 1.35rem;
            font-weight: 800;
            line-height: 1;
            color: #0f172a;
        }
        .dark .umpo-stat-value {
            color: #ffffff;
        }
        .umpo-stat-card.blue {
            background: #eff6ff;
            border-color: #bfdbfe;
        }
        .dark .umpo-stat-card.blue {
            background: rgba(37, 99, 235, 0.15);
            border-color: rgba(37, 99, 235, 0.3);
        }
        .umpo-stat-card.blue .umpo-stat-value {
            color: #1d4ed8;
        }
        .dark .umpo-stat-card.blue .umpo-stat-value {
            color: #60a5fa;
        }
        .umpo-stat-card.green {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }
        .dark .umpo-stat-card.green {
            background: rgba(22, 163, 74, 0.15);
            border-color: rgba(22, 163, 74, 0.3);
        }
        .umpo-stat-card.green .umpo-stat-value {
            color: #15803d;
        }
        .dark .umpo-stat-card.green .umpo-stat-value {
            color: #4ade80;
        }
        .umpo-stat-card.purple {
            background: #faf5ff;
            border-color: #e9d5ff;
        }
        .dark .umpo-stat-card.purple {
            background: rgba(147, 51, 234, 0.15);
            border-color: rgba(147, 51, 234, 0.3);
        }
        .umpo-stat-card.purple .umpo-stat-value {
            color: #7e22ce;
        }
        .dark .umpo-stat-card.purple .umpo-stat-value {
            color: #c084fc;
        }
        .umpo-progress-box {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }
        .umpo-progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
            font-weight: 600;
            color: #475569;
        }
        .dark .umpo-progress-label {
            color: #cbd5e1;
        }
        .umpo-progress-bar-bg {
            width: 100%;
            height: 0.85rem;
            background: #e2e8f0;
            border-radius: 9999px;
            overflow: hidden;
            border: 1px solid #cbd5e1;
        }
        .dark .umpo-progress-bar-bg {
            background: #0f172a;
            border-color: #334155;
        }
        .umpo-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #16a34a, #2563eb, #9333ea);
            border-radius: 9999px;
            transition: width 0.3s ease;
        }
        .umpo-terminal-log {
            background: #090d16;
            color: #e2e8f0;
            padding: 1rem;
            border-radius: 0.75rem;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.75rem;
            border: 1px solid #1e293b;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }
        .umpo-terminal-head {
            display: flex;
            justify-content: space-between;
            color: #64748b;
            font-size: 0.7rem;
            border-bottom: 1px solid #1e293b;
            padding-bottom: 0.35rem;
            margin-bottom: 0.25rem;
        }
        .umpo-log-active {
            color: #4ade80;
            font-weight: 600;
        }
        .umpo-log-msg {
            color: #94a3b8;
        }
        .umpo-actions-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 0 0.25rem;
            border-top: 1px solid #e2e8f0;
            margin-top: 0.5rem;
        }
        .dark .umpo-actions-row {
            border-color: #334155;
        }
        .umpo-btn-primary {
            background: #16a34a;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.875rem;
            padding: 0.7rem 1.5rem;
            border-radius: 0.75rem;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }
        .umpo-btn-primary:hover {
            background: #15803d;
            transform: translateY(-1px);
        }
        .umpo-btn-success {
            background: #2563eb;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.875rem;
            padding: 0.7rem 1.5rem;
            border-radius: 0.75rem;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }
        .umpo-btn-success:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }
    </style>

    <!-- Header Status Info -->
    <div class="umpo-header-card">
        <div class="umpo-header-icon">
            <svg style="width:1.5rem;height:1.5rem;" :class="{ 'umpo-spin': isRunning }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
        </div>
        <div style="flex:1;min-width:0;">
            <h4 class="umpo-header-title" x-text="stepText">Sinkronisasi Data Mahasiswa 2026</h4>
            <div class="umpo-header-sub" x-text="message">Klik tombol "Mulai Tarik Data Sekarang" di bawah.</div>
        </div>
        <div>
            <span class="umpo-badge" :class="statusBadgeClass" x-text="statusLabel">IDLE</span>
        </div>
    </div>

    <!-- Live Counter Cards -->
    <div class="umpo-grid-stats">
        <div class="umpo-stat-card">
            <span class="umpo-stat-label">Total Data API</span>
            <span class="umpo-stat-value" x-text="totalApi">0</span>
        </div>
        <div class="umpo-stat-card blue">
            <span class="umpo-stat-label">Diproses</span>
            <span class="umpo-stat-value" x-text="processed">0</span>
        </div>
        <div class="umpo-stat-card green">
            <span class="umpo-stat-label">Data Baru (+)</span>
            <span class="umpo-stat-value" x-text="created">0</span>
        </div>
        <div class="umpo-stat-card purple">
            <span class="umpo-stat-label">Diperbarui (🔄)</span>
            <span class="umpo-stat-value" x-text="updated">0</span>
        </div>
    </div>

    <!-- Progress Bar Realtime -->
    <div class="umpo-progress-box">
        <div class="umpo-progress-label">
            <span>Progres Penyimpanan:</span>
            <span style="font-family:monospace;font-weight:700;" x-text="percentage + '%'">0%</span>
        </div>
        <div class="umpo-progress-bar-bg">
            <div class="umpo-progress-fill" :style="'width: ' + percentage + '%'"></div>
        </div>
    </div>

    <!-- Realtime Log Status & Timer -->
    <div class="umpo-terminal-log">
        <div class="umpo-terminal-head">
            <span>Memproses ...</span>
            <span x-text="'Waktu: ' + elapsedTime + 's'">Waktu: 0s</span>
        </div>
        <div class="umpo-log-active" x-show="currentStudent" x-text="'[PROCESSING] ' + currentStudent"></div>
        <div class="umpo-log-msg" x-text="'[STATUS] ' + message"></div>
    </div>

    <!-- Controls Button Actions -->
    <div class="umpo-actions-row">
        <div style="font-size:0.75rem;color:#64748b;">
            Sumber: <span style="font-family:monospace;font-weight:600;color:#16a34a;">apikey.umpo.ac.id (2026)</span>
        </div>

        <div>
            <!-- Tombol Mulai -->
            <button type="button" 
                    x-show="!isRunning && !isCompleted" 
                    @click="startProcess()" 
                    class="umpo-btn-primary">
                <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span>Mulai Tarik Data Sekarang</span>
            </button>

            <!-- Tombol Loading -->
            <button type="button" 
                    x-show="isRunning" 
                    disabled 
                    style="opacity:0.7;cursor:not-allowed;"
                    class="umpo-btn-primary">
                <svg style="width:1.25rem;height:1.25rem;" class="umpo-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Sedang Menyinkronkan...</span>
            </button>

            <!-- Tombol Selesai -->
            <button type="button" 
                    x-show="isCompleted" 
                    @click="window.location.reload()" 
                    class="umpo-btn-success">
                <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>Selesai & Muat Ulang Tabel</span>
            </button>
        </div>
    </div>
</div>

<script>
    function umpoSyncModal() {
        return {
            isRunning: false,
            isCompleted: false,
            stepText: 'Siap untuk sinkronisasi mahasiswa 2026',
            message: 'Klik tombol "Mulai Tarik Data Sekarang" untuk memulai.',
            statusLabel: 'IDLE',
            statusBadgeClass: '',
            percentage: 0,
            totalApi: 0,
            processed: 0,
            created: 0,
            updated: 0,
            currentStudent: '',
            elapsedTime: 0,
            pollTimer: null,

            init() {
                // Check if process already running in background
                this.fetchProgress();
            },

            startProcess() {
                this.isRunning = true;
                this.isCompleted = false;
                this.stepText = 'Menghubungkan ke API UMPO...';
                this.statusLabel = 'BERJALAN';
                this.statusBadgeClass = 'running';

                // 1. Inisialisasi status di server
                fetch('/admin/umpo-sync/start', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });

                // 2. Mulai polling status tiap 1 detik (1000ms)
                this.pollTimer = setInterval(() => {
                    this.fetchProgress();
                }, 1000);

                // 3. Jalankan eksekusi batch di backend
                fetch('/admin/umpo-sync/execute', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(res => res.json()).then(res => {
                    this.fetchProgress();
                }).catch(err => {
                    this.stepText = 'Error: ' + err.message;
                    this.isRunning = false;
                    clearInterval(this.pollTimer);
                });
            },

            fetchProgress() {
                fetch('/admin/umpo-sync/progress')
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'processing' || data.status === 'fetching_api') {
                            this.isRunning = true;
                            this.isCompleted = false;
                            this.statusLabel = 'BERJALAN';
                            this.statusBadgeClass = 'running';
                        }
                        
                        this.stepText = data.step_text || this.stepText;
                        this.message = data.message || this.message;
                        this.percentage = data.percentage || 0;
                        this.totalApi = data.total_api || 0;
                        this.processed = data.processed || 0;
                        this.created = data.created || 0;
                        this.updated = data.updated || 0;
                        this.currentStudent = data.current_student || '';
                        this.elapsedTime = data.elapsed_seconds || this.elapsedTime;

                        if (data.status === 'completed') {
                            this.isRunning = false;
                            this.isCompleted = true;
                            this.percentage = 100;
                            this.statusLabel = 'SELESAI';
                            this.statusBadgeClass = 'success';
                            if (this.pollTimer) clearInterval(this.pollTimer);
                        } else if (data.status === 'error') {
                            this.isRunning = false;
                            this.statusLabel = 'ERROR';
                            this.statusBadgeClass = '';
                            if (this.pollTimer) clearInterval(this.pollTimer);
                        }
                    })
                    .catch(e => {});
            }
        };
    }
</script>
