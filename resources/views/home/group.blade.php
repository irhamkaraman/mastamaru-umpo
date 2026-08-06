@extends('layouts.home')

@section('title', 'Daftar Kelompok Peserta')

@section('content')
    @php
        // Cache view data untuk optimasi performa
        $cacheKey = 'group_view_data_' . md5(serialize($groups));
        $cachedData = Cache::remember($cacheKey, 3600, function() use ($groups) {
            return [
                'total_groups' => $groups->count(),
                'total_participants' => $groups->sum(function ($group) {
                    return $group->attendances->count();
                }),
                'groups_data' => $groups->map(function($group) {
                    return [
                        'id' => $group->id,
                        'name' => $group->name,
                        'description' => $group->description,
                        'attendances_count' => $group->attendances->count(),
                        'mentors' => $group->mentors->map(function($mentor) {
                            return [
                                'name' => $mentor->name,
                                'student_id' => $mentor->student_id,
                                'initial' => strtoupper(substr($mentor->name, 0, 1))
                            ];
                        }),
                        'attendances' => $group->attendances->map(function($attendance, $index) {
                            return [
                                'name' => $attendance->name,
                                'student_id' => $attendance->student_id,
                                'initial' => strtoupper(substr($attendance->name, 0, 1)),
                                'index' => $index + 1
                            ];
                        })
                    ];
                })
            ];
        });
    @endphp
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 my-8 sm:my-14 relative z-10">
        <!-- Header Section -->
        <div class="groups-intro mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <p class="text-xs uppercase tracking-[.22em] text-sky-600 font-bold mb-2">Direktori peserta</p>
                    <h1 class="text-2xl sm:text-4xl font-bold text-slate-900">Daftar Kelompok Peserta Mastamaru 2026</h1>
                    <p class="text-gray-600 mt-1 text-sm sm:text-base">Informasi lengkap Kelompok, Pemandu dan Peserta</p>
                </div>
                <div class="groups-stats flex flex-col xs:flex-row sm:flex-row gap-2">
                    <span
                        class="inline-flex items-center px-3 py-2 rounded-full text-xs sm:text-sm font-medium bg-blue-100 text-blue-800 justify-center">
                        Total: {{ $cachedData['total_groups'] }} Kelompok
                    </span>
                    <span
                        class="inline-flex items-center px-3 py-2 rounded-full text-xs sm:text-sm font-medium bg-green-100 text-green-800 justify-center">
                        {{ $cachedData['total_participants'] }} Total Peserta
                    </span>
                </div>
            </div>
        </div>

        <!-- Directory Controls -->
        <div class="directory-controls ui-panel rounded-[2rem] p-4 sm:p-5 mb-6">
            <div class="grid grid-cols-1 lg:grid-cols-[minmax(220px,.7fr)_minmax(300px,1.3fr)] gap-4 items-start">
                <div>
                    <label for="group-filter-select" class="control-label">Pilih kelompok</label>
                    <select id="group-filter-select" class="group-filter-select" data-placeholder="Cari nama kelompok...">
                        <option value="all">Semua Kelompok</option>
                        @foreach ($cachedData['groups_data'] as $index => $groupData)
                            <option value="{{ $index }}">{{ $groupData['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="global-search-container">
                    <label for="global-search-input" class="control-label">Cari seluruh peserta</label>
                    <div class="global-search-box">
                        <i class="fas fa-search text-sky-500" aria-hidden="true"></i>
                        <input type="text" id="global-search-input" placeholder="Cari nama atau NIM dari semua kelompok...">
                        <div class="hidden" id="search-loading"><i class="fas fa-spinner fa-spin text-sky-500"></i></div>
                        <div class="hidden" id="search-clear">
                            <button type="button" onclick="clearGlobalSearch()" aria-label="Hapus pencarian" class="search-clear-button">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center justify-between gap-2">
                        <span class="text-xs text-slate-500">Pencarian berlaku untuk seluruh kelompok.</span>
                        <div id="search-results-count" class="hidden">
                            <span class="search-result-badge"><i class="fas fa-check-circle mr-1"></i><span id="results-text"></span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Groups Cards -->
        <div class="container mx-auto px-2 sm:px-4 py-3 sm:py-6">
            <div class="grid gap-4 sm:gap-6">
                @forelse($cachedData['groups_data'] as $groupData)
                    <div
                        class="group-card bg-white border-0 shadow-2xl bg-white/90 border-white rounded-[2rem] overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1 backdrop-blur-sm">
                        <div class="group-banner py-4 sm:py-6">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-4 sm:px-6">
                                <div class="group-card-heading mb-3 sm:mb-0">
                                    <h4 class="text-xl sm:text-2xl font-bold mb-1">{{ $groupData['name'] }}</h4>
                                     <p class="text-slate-600 mb-0 text-sm sm:text-base">{{ $groupData['description'] }}</p>
                                </div>
                                <div class="text-left sm:text-right">
                                     <div class="bg-white/80 text-slate-700 border border-white px-3 sm:px-4 py-2 rounded-full inline-flex items-center text-sm sm:text-base shadow-sm">
                                        <i class="fas fa-users mr-2"></i>
                                        {{ $groupData['attendances_count'] }} Peserta
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-0">
                            <div class="flex flex-col lg:flex-row">
                                <!-- Pendamping Section -->
                                <div class="mentor-panel lg:w-1/3 border-b lg:border-b-0 lg:border-r border-gray-200">
                                    <div class="p-4 sm:p-6">
                                        <h6
                                            class="text-blue-600 text-base sm:text-lg font-bold mb-3 sm:mb-4 flex items-center">
                                            <i class="fas fa-user-tie mr-2"></i>Pemandu
                                        </h6>
                                        @if (count($groupData['mentors']) > 0)
                                            <div class="space-y-2 sm:space-y-3">
                                                @foreach ($groupData['mentors'] as $mentor)
                                                    <div
                                                        class="bg-gray-50 rounded-lg p-3 sm:p-4 hover:bg-blue-50 hover:border-blue-200 border border-transparent transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                                                        <div class="flex items-center">
                                                            <div
                                                                class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-xs sm:text-sm mr-2 sm:mr-3 flex-shrink-0">
                                                                {{ $mentor['initial'] }}
                                                            </div>
                                                            <div class="min-w-0 flex-1">
                                                                <h6 class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $mentor['name'] }}
                                                                </h6>
                                                                <p class="text-gray-500 text-xs sm:text-sm truncate">NIM:
                                                                    {{ $mentor['student_id'] }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-6 sm:py-8">
                                                <i
                                                    class="fas fa-user-slash text-gray-400 text-2xl sm:text-3xl mb-2 sm:mb-3"></i>
                                                <p class="text-gray-500 text-sm sm:text-base">Belum ada Pemandu</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Peserta Section -->
                                <div class="participants-panel lg:w-2/3">
                                    <div class="p-4 sm:p-6">
                                        <div
                                            class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-3 sm:mb-4">
                                            <h6
                                                class="text-green-600 text-base sm:text-lg font-bold flex items-center mb-2 sm:mb-0">
                                                <i class="fas fa-users mr-2"></i>Daftar Peserta
                                            </h6>
                                            @if (count($groupData['attendances']) > 0)
                                                <div class="relative w-full sm:w-auto">
                                                    <input type="text"
                                                        class="participant-search w-full sm:w-auto px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                                                        placeholder="Cari nama atau NIM...">
                                                    <i class="fas fa-search absolute right-3 top-3 text-gray-400 text-sm"></i>
                                                </div>
                                            @endif
                                        </div>
                                        @if (count($groupData['attendances']) > 0)
                                            <div class="overflow-x-auto -mx-4 sm:mx-0">
                                                 <div class="inline-block min-w-full align-middle">
                                                     <table class="min-w-full divide-y divide-gray-200 participant-table responsive-participant-table">
                                                        <thead class="bg-gray-50">
                                                            <tr>
                                                                <th
                                                                    class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                                                    #</th>
                                                                <th
                                                                    class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                    Nama</th>
                                                                <th
                                                                    class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                    NIM</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="bg-white divide-y divide-gray-200">
                                                            @foreach ($groupData['attendances'] as $attendance)
                                                                <tr class="participant-row hover:bg-blue-50 transition-colors duration-150"
                                                                    data-name="{{ strtolower($attendance['name']) }}"
                                                                    data-nim="{{ $attendance['student_id'] }}">
                                                                    <td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-semibold text-gray-900 w-12">
                                                                        {{ $attendance['index'] }}
                                                                    </td>
                                                                    <td class="px-2 sm:px-4 py-2 sm:py-3">
                                                                        <div class="flex items-center">
                                                                            <div
                                                                                class="w-6 h-6 sm:w-8 sm:h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold text-xs mr-2 sm:mr-3 flex-shrink-0">
                                                                                {{ $attendance['initial'] }}
                                                                            </div>
                                                                            <span
                                                                                class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ $attendance['name'] }}</span>
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm text-gray-500">
                                                                        {{ $attendance['student_id'] }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center py-8 sm:py-12">
                                                <i
                                                    class="fas fa-user-friends text-gray-400 text-3xl sm:text-4xl mb-3 sm:mb-4"></i>
                                                <h6 class="text-gray-500 text-base sm:text-lg font-medium">Belum ada
                                                    peserta terdaftar
                                                </h6>
                                                <p class="text-gray-400 text-xs sm:text-sm mt-1">Peserta akan muncul
                                                    setelah melakukan
                                                    registrasi</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                @empty
                    <div class="w-full">
                        <div class="bg-white shadow-sm border-0 rounded-lg">
                            <div class="p-8 text-center">
                                <i class="fas fa-users-slash text-gray-400 text-6xl mb-6"></i>
                                <h4 class="text-gray-500 text-xl font-medium mb-3">Belum Ada Kelompok</h4>
                                <p class="text-gray-400">Data kelompok akan muncul setelah diinput melalui sistem admin</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        .groups-intro { position: relative; }
        .groups-stats span { background: rgba(255,255,255,.72); border: 1px solid rgba(148,163,184,.16); color: #516079; box-shadow: 0 8px 18px rgba(81,96,121,.06); }
        .directory-controls { background: rgba(255,255,255,.76); border-color: rgba(255,255,255,.92); box-shadow: 0 18px 44px rgba(75,58,146,.08); }
        .control-label { display:block; margin-bottom:8px; color:#53627b; font-size:12px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
        .directory-controls > div > div { min-width:0; }
        .directory-controls #global-search-container { min-width:0; }
        .directory-controls .select2-container { display:block; }
        .global-search-box { display:flex; align-items:center; gap:12px; min-height:50px; padding:0 15px; background:rgba(255,255,255,.86); border:1px solid rgba(148,163,184,.28); border-radius:15px; transition:box-shadow .2s,border-color .2s; }
        .global-search-box:focus-within { border-color:#7dd3fc; box-shadow:0 0 0 4px rgba(125,211,252,.16); }
        .global-search-box input { width:100%; border:0; outline:0; background:transparent; color:#334155; font-size:14px; }
        .search-clear-button { border:0; background:transparent; color:#94a3b8; cursor:pointer; }
        .search-clear-button:hover { color:#64748b; }
        .search-result-badge { display:inline-flex; align-items:center; padding:4px 9px; border-radius:999px; background:#ecfdf5; color:#15803d; font-size:11px; font-weight:700; white-space:nowrap; }
        .select2-container { width:100%!important; }
        .select2-container--default .select2-selection--single { height:50px; border:1px solid rgba(148,163,184,.28); border-radius:15px; background:rgba(255,255,255,.86); }
        .select2-container--default .select2-selection--single .select2-selection__rendered { padding:14px 42px 14px 15px; color:#334155; font-size:14px; line-height:21px; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { top:13px; right:12px; }
        .select2-container--default.select2-container--focus .select2-selection--single { border-color:#7dd3fc; box-shadow:0 0 0 4px rgba(125,211,252,.16); }
        .select2-dropdown { border:1px solid rgba(148,163,184,.22); border-radius:14px; overflow:hidden; box-shadow:0 18px 38px rgba(51,65,85,.14); max-width:calc(100vw - 24px); }
        .group-select-dropdown { z-index:1000; }
        .select2-search--dropdown { padding:10px; background:#f8fafc; }
        .select2-search--dropdown .select2-search__field { border:1px solid #cbd5e1; border-radius:10px; padding:8px 10px; outline:0; }
        .select2-results__option { padding:10px 12px; font-size:13px; }
        .select2-container--default .select2-results__option--highlighted[aria-selected] { background:#e0f2fe; color:#0369a1; }
        .responsive-participant-table th { white-space:nowrap; }
        .responsive-participant-table td { max-width:320px; }
        .participant-table tbody tr { transition:background-color .18s ease; }
        .participant-table tbody tr:hover { background:#f8fbff; }
        .group-card-heading { min-width:0; }
        .group-card-heading h4 { overflow-wrap:anywhere; }
        .mentor-panel { background:rgba(248,250,252,.56); }
        .participants-panel { background:rgba(255,255,255,.68); }
        .group-card .participant-search { background:#fff; border-color:rgba(148,163,184,.28); }

        .group-banner {
            background: linear-gradient(135deg, #eef6ff 0%, #f5f0ff 55%, #effbf5 100%);
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
        }

        .avatar-sm {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 12px;
        }

        .mentor-item {
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .mentor-item:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }

        .stat-item {
            padding: 8px;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.5em 0.75em;
        }

        @media (max-width: 768px) {

            .col-md-4,
            .col-md-8 {
                border-end: none !important;
                border-bottom: 1px solid #dee2e6;
            }

            .col-md-8 {
                border-bottom: none !important;
            }

            .responsive-participant-table { min-width: 0; width:100%; table-layout:fixed; }
        }

        @media (max-width: 520px) {
            .groups-intro h1 { font-size:1.55rem; line-height:1.2; }
            .groups-stats { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); width:100%; }
            .groups-stats span { min-width:0; padding:9px 8px; text-align:center; line-height:1.25; }
            .directory-controls { border-radius:1.5rem; padding:14px; }
            .global-search-box { min-height:48px; padding:0 12px; }
            .global-search-box input { min-width:0; font-size:13px; }
            .select2-container--default .select2-selection--single { height:48px; }
            .select2-container--default .select2-selection--single .select2-selection__rendered { padding-top:13px; padding-bottom:13px; }
            .select2-dropdown { width:calc(100vw - 24px)!important; max-width:calc(100vw - 24px)!important; }
            .responsive-participant-table { min-width:0; width:100%; border-collapse:separate; border-spacing:0 8px; }
            .responsive-participant-table thead { display:none; }
            .responsive-participant-table tbody { display:block; }
            .responsive-participant-table tbody tr {
                display:grid;
                grid-template-columns:38px minmax(0, 1fr);
                align-items:center;
                width:100%;
                padding:10px 12px;
                background:#fff;
                border:1px solid #edf1f7;
                border-radius:14px;
                box-shadow:0 5px 14px rgba(51,65,85,.05);
            }
            .responsive-participant-table tbody td {
                display:block;
                max-width:none;
                padding:0;
                border:0;
                min-width:0;
            }
            .responsive-participant-table tbody td:first-child {
                grid-row:span 2;
                width:28px;
                color:#94a3b8;
                font-size:12px;
                font-weight:800;
            }
            .responsive-participant-table tbody td:nth-child(2) { min-width:0; }
            .responsive-participant-table tbody td:nth-child(2) > div { min-width:0; }
            .responsive-participant-table tbody td:nth-child(2) span { display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
            .responsive-participant-table tbody td:nth-child(3) { display:block; color:#94a3b8; font-size:11px; line-height:1.25; margin-top:2px; }
            .responsive-participant-table tbody td:nth-child(3)::before { content:'NIM '; color:#cbd5e1; font-weight:700; }
            .responsive-participant-table tbody td:nth-child(2) .w-6,
            .responsive-participant-table tbody td:nth-child(2) .sm\:w-8 { flex-shrink:0; }
            .group-banner { padding-top:18px; padding-bottom:18px; }
            .group-banner .flex { gap:12px; }
            .group-banner .text-left { width:100%; }
            .group-banner .text-left > div { width:fit-content; }
            .group-banner .text-left.sm\:text-right { text-align:left; }
            .group-banner .bg-white\/80 { font-size:12px; }
            .lg\:w-1\/3, .lg\:w-2\/3 { width:100%; }
            .participant-search { min-width:0; width:100%; }
            .mentor-panel { background:rgba(248,250,252,.5); }
            .participants-panel { border-top:1px solid rgba(226,232,240,.75); }
        }
    </style>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        .directory-controls .select2-container { width:100% !important; }
        .directory-controls .select2-container--default .select2-selection--single { height:50px !important; border:1px solid rgba(148,163,184,.28) !important; border-radius:15px !important; background:rgba(255,255,255,.86) !important; box-sizing:border-box; }
        .directory-controls .select2-container--default .select2-selection--single .select2-selection__rendered { padding:14px 42px 14px 15px !important; color:#334155 !important; line-height:21px !important; }
        .directory-controls .select2-container--default .select2-selection--single .select2-selection__arrow { top:13px !important; right:12px !important; }
        .group-select-dropdown.select2-dropdown { width:min(100%, 420px) !important; max-width:calc(100vw - 24px) !important; }
        @media (max-width:520px) {
            .directory-controls .select2-container--default .select2-selection--single { height:48px !important; }
            .directory-controls .select2-container--default .select2-selection--single .select2-selection__rendered { padding-top:13px !important; padding-bottom:13px !important; }
            .group-select-dropdown.select2-dropdown { width:calc(100vw - 24px) !important; }
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

    <!-- JavaScript for Search and Filter Functionality -->
    <script>
        // Group filter functionality
        function showAllGroups() {
            const groups = document.querySelectorAll('.bg-white.border-0.shadow-2xl');
            groups.forEach(group => {
                group.style.display = 'block';
            });

            resetAllParticipantRows();
        }

        function showGroup(index) {
            const groups = document.querySelectorAll('.bg-white.border-0.shadow-2xl');
            groups.forEach((group, i) => {
                group.style.display = i === index ? 'block' : 'none';
            });

            resetAllParticipantRows();
        }

        // Reset all participant rows to visible
        function resetAllParticipantRows() {
            document.querySelectorAll('.participant-row').forEach(row => {
                row.style.display = '';
            });
        }

        // Clear global search function
        function clearGlobalSearch() {
            const globalSearchInput = document.getElementById('global-search-input');
            globalSearchInput.value = '';
            showAllGroups();
            hideSearchElements();
        }

        // Show/hide search elements
        function showSearchElements() {
            document.getElementById('search-clear').classList.remove('hidden');
        }

        function hideSearchElements() {
            document.getElementById('search-clear').classList.add('hidden');
            document.getElementById('search-results-count').classList.add('hidden');
        }

        function showSearchResults(count) {
            const resultsCount = document.getElementById('search-results-count');
            const resultsText = document.getElementById('results-text');
            const badge = resultsCount.querySelector('span');

            if (count > 0) {
                resultsText.textContent = `${count} peserta ditemukan`;
                badge.className =
                    'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800';
                badge.querySelector('i').className = 'fas fa-check-circle mr-1';
                resultsCount.classList.remove('hidden');
            } else {
                resultsText.textContent = 'Tidak ada peserta ditemukan';
                badge.className =
                    'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800';
                badge.querySelector('i').className = 'fas fa-exclamation-circle mr-1';
                resultsCount.classList.remove('hidden');
            }
        }

        // Global search functionality
        function performGlobalSearch(searchTerm) {
            const allRows = document.querySelectorAll('.participant-row');
            let visibleCount = 0;
            const groupsWithMatches = new Set();

            // First, hide all groups
            const allGroups = document.querySelectorAll('.bg-white.border-0.shadow-2xl');
            allGroups.forEach(group => {
                group.style.display = 'none';
            });

            // Process each participant row
            allRows.forEach(row => {
                const name = row.getAttribute('data-name');
                const nim = row.getAttribute('data-nim');

                if (name.includes(searchTerm) || nim.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                    // Track which group has matches
                    const parentGroup = row.closest('.bg-white.border-0.shadow-2xl');
                    if (parentGroup) {
                        groupsWithMatches.add(parentGroup);
                    }
                } else {
                    row.style.display = 'none';
                }
            });

            // Show only groups that have matching participants
            groupsWithMatches.forEach(group => {
                group.style.display = 'block';
            });

            showSearchResults(visibleCount);
        }

        // Participant search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const groupFilter = $('#group-filter-select');

            groupFilter.select2({
                width: '100%',
                minimumResultsForSearch: 0,
                dropdownAutoWidth: false,
                dropdownParent: $(document.body),
                dropdownCssClass: 'group-select-dropdown',
                placeholder: 'Cari nama kelompok...',
                allowClear: false
            });

            groupFilter.on('change', function() {
                const selectedGroup = this.value;
                document.getElementById('global-search-input').value = '';
                hideSearchElements();

                if (selectedGroup === 'all') {
                    showAllGroups();
                } else {
                    showGroup(Number(selectedGroup));
                }
            });

            // Individual group search inputs
            const searchInputs = document.querySelectorAll('.participant-search');

            searchInputs.forEach(searchInput => {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const participantSection = this.closest('[class*="lg:w-2/3"]');
                    const table = participantSection ? participantSection.querySelector(
                        '.participant-table') : null;

                    if (table) {
                        const rows = table.querySelectorAll('.participant-row');

                        rows.forEach(row => {
                            const name = row.getAttribute('data-name');
                            const nim = row.getAttribute('data-nim');

                            if (name.includes(searchTerm) || nim.includes(searchTerm)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                    }
                });
            });

            // Global search input
            const globalSearchInput = document.getElementById('global-search-input');
            if (globalSearchInput) {
                globalSearchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();

                    if (searchTerm.length === 0) {
                        // Show all groups and participants when search is empty
                        showAllGroups();
                        hideSearchElements();
                    } else {
                        showSearchElements();
                        performGlobalSearch(searchTerm);
                    }
                });

                globalSearchInput.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        clearGlobalSearch();
                    }
                });

                // The new search shell uses :focus-within for its visual state.
            }

            // Keep the directory on the all-groups view on first load.
            groupFilter.val('all').trigger('change.select2');
            showAllGroups();
        });
    </script>

@endsection
