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
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 my-8 sm:my-20">
        <!-- Header Section -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Daftar Kelompok Peserta Mastamaru 2026</h1>
                    <p class="text-gray-600 mt-1 text-sm sm:text-base">Informasi lengkap Kelompok, Pemandu dan Peserta</p>
                </div>
                <div class="flex flex-col xs:flex-row sm:flex-row gap-2">
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

        <!-- Group Filter Buttons -->
        <div class="mb-4 sm:mb-6">
            <div class="flex flex-wrap gap-1.5 sm:gap-2 mb-4">
                <button onclick="showAllGroups()"
                    class="group-filter-btn active px-3 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm sm:text-base flex-shrink-0">
                    Semua Kelompok
                </button>
                @foreach ($cachedData['groups_data'] as $index => $groupData)
                    <button onclick="showGroup({{ $index }})"
                        class="group-filter-btn px-3 sm:px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 text-sm sm:text-base flex-shrink-0">
                        {{ $groupData['name'] }}
                    </button>
                @endforeach
            </div>

            <!-- Global Search Input (shown only when all groups are displayed) -->
            <div id="global-search-container" class="mb-4 sm:mb-6">
                <div class="relative max-w-lg mx-auto px-2 sm:px-0">
                    <div class="relative group">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl blur opacity-25 group-hover:opacity-40 transition duration-300">
                        </div>
                        <div
                            class="relative bg-white rounded-xl border-2 border-gray-200 hover:border-blue-300 transition-all duration-300 shadow-lg hover:shadow-xl">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 pl-3 sm:pl-4">
                                    <i class="fas fa-search text-blue-500 text-base sm:text-lg"></i>
                                </div>
                                <input type="text" id="global-search-input"
                                    class="w-full px-3 sm:px-4 py-3 sm:py-4 bg-transparent border-0 focus:ring-0 focus:outline-none text-gray-700 placeholder-gray-400 text-base sm:text-lg"
                                    placeholder="🔍 Cari peserta dari semua kelompok...">
                                <div class="flex-shrink-0 pr-3 sm:pr-4">
                                    <div class="hidden" id="search-loading">
                                        <i class="fas fa-spinner fa-spin text-blue-500"></i>
                                    </div>
                                    <div class="hidden" id="search-clear">
                                        <button onclick="clearGlobalSearch()"
                                            class="text-gray-400 hover:text-red-500 transition-colors duration-200">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 text-center px-2">
                        <span class="text-xs sm:text-sm text-gray-500 font-medium">Ketik untuk mencari nama atau NIM
                            peserta</span>
                    </div>
                    <div id="search-results-count" class="hidden mt-2 text-center px-2">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>
                            <span id="results-text"></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Groups Cards -->
        <div class="container mx-auto px-2 sm:px-4 py-3 sm:py-6">
            <div class="grid gap-4 sm:gap-6">
                @forelse($cachedData['groups_data'] as $groupData)
                    <div
                        class="bg-white border-0 shadow-2xl rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 sm:py-6">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-4 sm:px-6">
                                <div class="mb-3 sm:mb-0">
                                    <h4 class="text-xl sm:text-2xl font-bold mb-1">{{ $groupData['name'] }}</h4>
                                    <p class="text-blue-100 mb-0 text-sm sm:text-base">{{ $groupData['description'] }}</p>
                                </div>
                                <div class="text-left sm:text-right">
                                    <div class="bg-white text-blue-600 px-3 sm:px-4 py-2 rounded-full inline-flex items-center text-sm sm:text-base">
                                        <i class="fas fa-users mr-2"></i>
                                        {{ $groupData['attendances_count'] }} Peserta
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-0">
                            <div class="flex flex-col lg:flex-row">
                                <!-- Pendamping Section -->
                                <div class="lg:w-1/3 border-b lg:border-b-0 lg:border-r border-gray-200">
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
                                <div class="lg:w-2/3">
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
                                                    <table class="min-w-full divide-y divide-gray-200 participant-table">
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

            // Show global search and hide individual search inputs
            document.getElementById('global-search-container').style.display = 'block';
            document.querySelectorAll('.participant-search').forEach(input => {
                input.closest('.relative').style.display = 'block';
            });

            // Clear global search
            document.getElementById('global-search-input').value = '';
            resetAllParticipantRows();

            // Update active button
            document.querySelectorAll('.group-filter-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-blue-600', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700');
            });
            document.querySelector('.group-filter-btn').classList.add('active', 'bg-blue-600', 'text-white');
            document.querySelector('.group-filter-btn').classList.remove('bg-gray-200', 'text-gray-700');
        }

        function showGroup(index) {
            const groups = document.querySelectorAll('.bg-white.border-0.shadow-2xl');
            groups.forEach((group, i) => {
                group.style.display = i === index ? 'block' : 'none';
            });

            // Hide global search when showing specific group
            document.getElementById('global-search-container').style.display = 'none';

            // Clear global search and reset all rows
            document.getElementById('global-search-input').value = '';
            resetAllParticipantRows();

            // Update active button
            document.querySelectorAll('.group-filter-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-blue-600', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700');
            });
            document.querySelectorAll('.group-filter-btn')[index + 1].classList.add('active', 'bg-blue-600', 'text-white');
            document.querySelectorAll('.group-filter-btn')[index + 1].classList.remove('bg-gray-200', 'text-gray-700');
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

                // Add focus and blur effects
                globalSearchInput.addEventListener('focus', function() {
                    const absoluteElement = this.closest('.relative').querySelector('.absolute');
                    if (absoluteElement) {
                        absoluteElement.classList.add('opacity-40');
                    }
                });

                globalSearchInput.addEventListener('blur', function() {
                    const absoluteElement = this.closest('.relative').querySelector('.absolute');
                    if (absoluteElement) {
                        absoluteElement.classList.remove('opacity-40');
                    }
                });
            }
        });
    </script>

@endsection
