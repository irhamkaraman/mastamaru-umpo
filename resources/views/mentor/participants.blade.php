@extends('layouts.mentor')

@section('title', 'Daftar Peserta')

@section('content')
    <div class="mentor-dashboard-page min-h-screen bg-gray-50 pb-12">
        <div class="mentor-dashboard-container container mx-auto py-6 sm:py-8" style="padding-left: 1.5rem; padding-right: 1.5rem; max-width: 1200px; margin: 0 auto;">
            <!-- Header -->
            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <a href="{{ route('mentor.dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium mb-3 rounded-full transition text-sm w-fit">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Dashboard
                    </a>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Daftar Peserta</h1>
                    <p class="text-gray-500">Kelompok: <span class="font-semibold text-gray-700">{{ $mentor->group->name ?? 'Belum ada kelompok' }}</span></p>
                </div>
            </div>

            <!-- Banner Aturan Sertifikat -->
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4 sm:p-5 shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-blue-800">Petunjuk Penerbitan Sertifikat</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Sertifikat <b>hanya dapat diterbitkan</b> untuk peserta yang telah berstatus <b>LULUS</b>.</li>
                                <li>Kelulusan ditentukan berdasarkan perolehan Total Poin presensi peserta.</li>
                                <li>Pilih satu atau beberapa peserta LULUS dengan mencentang kotak di tabel, lalu klik tombol <b>Cetak Sertifikat Massal</b> di atas.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifikasi -->
            @if (session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Aksi Tabel -->
            <div class="mb-4">
                <form id="generate-cert-form" action="{{ route('mentor.participants.generate-certificates') }}" method="POST" class="flex flex-col sm:flex-row flex-wrap gap-4 items-end sm:items-center justify-end bg-gray-50/50 p-3 rounded-xl border border-gray-100">
                    @csrf
                    <input type="hidden" name="selected_students" id="selected_students_input" value="[]">
                    <input type="hidden" name="mentor_nim" id="mentor_nim_input" value="">
                    <input type="hidden" name="mentor_password" id="mentor_password_input" value="">
                    <input type="hidden" name="status" id="status_input" value="">
                    
                    <div class="flex items-center gap-2 border-r border-gray-300 pr-4">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mr-1">Ubah Status:</span>
                        <button type="button" onclick="confirmBulkAction('lulus')" id="btn-set-lulus" disabled class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-3 rounded-lg transition duration-200 shadow-sm flex items-center opacity-50 cursor-not-allowed text-xs">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Lulus
                        </button>
                        <button type="button" onclick="confirmBulkAction('gagal')" id="btn-set-gagal" disabled class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-3 rounded-lg transition duration-200 shadow-sm flex items-center opacity-50 cursor-not-allowed text-xs">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Gagal
                        </button>
                    </div>

                    <div class="flex items-center gap-2 pl-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mr-1">Penerbitan:</span>
                        <button type="button" onclick="confirmBulkAction('cetak')" id="btn-cetak-sertifikat" disabled class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 shadow-sm flex items-center opacity-50 cursor-not-allowed text-xs">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Cetak Sertifikat
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Peserta -->
            <div class="bg-white rounded-2xl md:rounded-3xl shadow-xl overflow-hidden border border-gray-100 mb-10">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 md:px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">
                                    <input type="checkbox" id="selectAll" class="w-5 h-5 md:w-6 md:h-6 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </th>
                                <th scope="col" class="px-2 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">No</th>
                                <th scope="col" class="px-4 md:px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama / NIM</th>
                                <th scope="col" class="px-4 md:px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Fakultas / Prodi</th>
                                <th scope="col" class="px-4 md:px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Poin</th>
                                <th scope="col" class="px-4 md:px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-4 md:px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($participants as $index => $participant)
                                @php
                                    $hasCert = $participant->hasCertificate();
                                @endphp
                                <tr class="hover:bg-blue-50 transition-colors cursor-pointer" onclick="showPointHistory('{{ $participant->id }}', '{{ addslashes($participant->name) }}')">
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap" onclick="event.stopPropagation()">
                                        @if($hasCert)
                                            <input type="checkbox" disabled class="w-5 h-5 md:w-6 md:h-6 rounded border-gray-300 text-gray-300 bg-gray-100 cursor-not-allowed shadow-sm" title="Sertifikat sudah diterbitkan">
                                        @else
                                            <input type="checkbox" class="student-checkbox w-5 h-5 md:w-6 md:h-6 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" value="{{ $participant->id }}" data-status="{{ $participant->status }}">
                                        @endif
                                    </td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-4 md:px-6 py-4">
                                        <div class="text-sm font-semibold text-gray-900 leading-tight">{{ $participant->name }}</div>
                                        <div class="text-xs text-gray-500 mt-1">{{ $participant->student_id }}</div>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 hidden md:table-cell">
                                        <div class="text-sm text-gray-900">{{ $participant->faculty ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $participant->study_program ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-sm md:text-base font-bold text-blue-600">
                                            {{ $participant->assessment->total_presence_points ?? 0 }}
                                        </div>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-center">
                                        @if($participant->status === 'lulus')
                                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                LULUS
                                            </span>
                                            @if($hasCert)
                                                <span class="block mt-1 text-[10px] text-gray-500 font-medium">Sertifikat Terbit</span>
                                            @endif
                                        @elseif($participant->status === 'gagal')
                                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                GAGAL
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                PROSES
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm font-medium text-center hidden md:table-cell">
                                        <div class="flex flex-col gap-2 justify-center items-center">
                                            @if(!$hasCert)
                                                <div class="flex gap-1 mb-1">
                                                    <button type="button" onclick="singleAction('lulus', '{{ $participant->id }}')" class="text-[10px] uppercase font-bold text-green-700 bg-green-100 hover:bg-green-200 px-2 py-1 rounded shadow-sm transition-colors" title="Set Lulus">Lulus</button>
                                                    <button type="button" onclick="singleAction('gagal', '{{ $participant->id }}')" class="text-[10px] uppercase font-bold text-red-700 bg-red-100 hover:bg-red-200 px-2 py-1 rounded shadow-sm transition-colors" title="Set Gagal">Gagal</button>
                                                </div>
                                            @endif
                                            <button type="button" onclick="showPointHistory('{{ $participant->id }}', '{{ addslashes($participant->name) }}')" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors flex items-center justify-center mx-auto w-full" title="Riwayat Poin">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>Riwayat Poin</span>
                                            </button>
                                            @if($hasCert)
                                            <a href="{{ asset('storage/' . $participant->certificate_file) }}" target="_blank" onclick="event.stopPropagation()" class="text-emerald-600 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg transition-colors flex items-center justify-center mx-auto w-full" title="Lihat Sertifikat">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <span>Lihat Sertifikat</span>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        Belum ada peserta yang dimasukkan ke dalam kelompok ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Riwayat Poin Peserta -->
    <div id="student-history-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl relative max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900" id="hist-student-name">Riwayat Poin Peserta</h3>
                        <p class="text-xs text-gray-500" id="hist-student-nim">NIM: -</p>
                    </div>
                </div>
                <button type="button" onclick="closeStudentHistoryModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-semibold">&times;</button>
            </div>
            
            <div class="overflow-y-auto py-4 space-y-4 flex-1" id="hist-modal-body">
                <div class="text-center py-8 text-gray-400">Memuat data riwayat poin...</div>
            </div>
            
            <div class="pt-3 border-t border-gray-100 flex justify-end flex-shrink-0">
                <button type="button" onclick="closeStudentHistoryModal()" class="px-5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Penanganan Select All dan Checkbox
    const selectAllCheckbox = document.getElementById('selectAll');
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');
    const btnCetak = document.getElementById('btn-cetak-sertifikat');
    const btnLulus = document.getElementById('btn-set-lulus');
    const btnGagal = document.getElementById('btn-set-gagal');
    const inputSelectedStudents = document.getElementById('selected_students_input');

    function updateCetakButtonState() {
        const selectedCount = document.querySelectorAll('.student-checkbox:checked').length;
        if (selectedCount > 0) {
            btnCetak.removeAttribute('disabled');
            btnCetak.classList.remove('opacity-50', 'cursor-not-allowed');
            btnLulus.removeAttribute('disabled');
            btnLulus.classList.remove('opacity-50', 'cursor-not-allowed');
            btnGagal.removeAttribute('disabled');
            btnGagal.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            btnCetak.setAttribute('disabled', 'disabled');
            btnCetak.classList.add('opacity-50', 'cursor-not-allowed');
            btnLulus.setAttribute('disabled', 'disabled');
            btnLulus.classList.add('opacity-50', 'cursor-not-allowed');
            btnGagal.setAttribute('disabled', 'disabled');
            btnGagal.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    function updateSelectedStudentsInput() {
        const selectedIds = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);
        inputSelectedStudents.value = JSON.stringify(selectedIds);
    }

    if(selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            studentCheckboxes.forEach(checkbox => {
                if(!checkbox.disabled) checkbox.checked = this.checked;
            });
            updateCetakButtonState();
            updateSelectedStudentsInput();
        });
    }

    studentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const enabledCheckboxes = Array.from(studentCheckboxes).filter(cb => !cb.disabled);
            const allChecked = enabledCheckboxes.length > 0 && enabledCheckboxes.every(cb => cb.checked);
            const someChecked = enabledCheckboxes.some(cb => cb.checked);
            
            if(selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            }
            
            updateCetakButtonState();
            updateSelectedStudentsInput();
        });
    });

    let currentActionType = '';

    window.singleAction = function(actionType, studentId) {
        document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = false);
        const cb = document.querySelector(`.student-checkbox[value="${studentId}"]`);
        if(cb) cb.checked = true;
        
        updateCetakButtonState();
        updateSelectedStudentsInput();
        
        confirmBulkAction(actionType);
    };

    window.confirmBulkAction = function(actionType) {
        currentActionType = actionType;
        const form = document.getElementById('generate-cert-form');
        
        if (actionType === 'lulus' || actionType === 'gagal') {
            form.action = "{{ route('mentor.participants.set-status') }}";
            document.getElementById('status_input').value = actionType;
            promptAuthorization(form, actionType);
        } else if (actionType === 'cetak') {
            form.action = "{{ route('mentor.participants.generate-certificates') }}";
            
            const selectedCheckboxes = document.querySelectorAll('.student-checkbox:checked');
            let hasNonLulus = false;

            selectedCheckboxes.forEach(cb => {
                if (cb.getAttribute('data-status') !== 'lulus') {
                    hasNonLulus = true;
                }
            });

            if (hasNonLulus) {
                Swal.fire({
                    title: 'Perhatian!',
                    text: "Anda memilih beberapa peserta yang statusnya belum LULUS. Peserta ini tetap akan diterbitkan sertifikatnya secara paksa. Lanjutkan?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Tetap Terbitkan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        promptAuthorization(form, actionType);
                    }
                });
            } else {
                promptAuthorization(form, actionType);
            }
        }
    };

    function promptAuthorization(form, actionType) {
        let actionText = actionType === 'cetak' ? 'Penerbitan' : 'Perubahan Status';
        let btnText = actionType === 'cetak' ? 'Verifikasi & Terbitkan' : 'Verifikasi & Simpan';

        Swal.fire({
            title: 'Otorisasi ' + actionText,
            html: `
                <div class="mt-2 text-left">
                    <p class="text-sm text-gray-600 mb-3 text-center">Masukkan NIM dan Kata Sandi Anda untuk memverifikasi tindakan ini.</p>
                    <input type="text" id="swal-nim" class="swal2-input mx-auto flex w-4/5" placeholder="NIM Pendamping" autocomplete="off">
                    <input type="password" id="swal-password" class="swal2-input mx-auto flex w-4/5" placeholder="Password Pendamping">
                </div>
            `,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: btnText,
            cancelButtonText: 'Batal',
            confirmButtonColor: '#2563eb',
            preConfirm: () => {
                const nim = document.getElementById('swal-nim').value;
                const password = document.getElementById('swal-password').value;
                if (!nim || !password) {
                    Swal.showValidationMessage('NIM dan Password wajib diisi');
                }
                return { nim: nim, password: password }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('mentor_nim_input').value = result.value.nim;
                document.getElementById('mentor_password_input').value = result.value.password;
                
                processSubmit(form, actionType);
            }
        });
    }

    function processSubmit(form, actionType) {
        let msg = actionType === 'cetak' 
            ? 'Mohon tunggu, sertifikat sedang dibuat dan akan otomatis terunduh saat selesai.'
            : 'Mohon tunggu, status peserta sedang diperbarui...';

        Swal.fire({
            title: 'Sedang Memproses...',
            text: msg,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        form.submit();
    }

    // Fungsi untuk menutup modal
    function closeStudentHistoryModal() {
        document.getElementById('student-history-modal').classList.add('hidden');
    }

    // Fungsi untuk menampilkan riwayat poin
    function showPointHistory(studentId, studentName) {
        const modal = document.getElementById('student-history-modal');
        const body = document.getElementById('hist-modal-body');
        modal.classList.remove('hidden');
        body.innerHTML = '<div class="text-center py-8 text-gray-400">Memuat data riwayat poin...</div>';

        fetch(`/mentor/student/${studentId}/point-history`)
            .then(res => res.json())
            .then(res => {
                if (!res.success) {
                    body.innerHTML = '<div class="text-center py-6 text-red-500">Gagal memuat riwayat poin.</div>';
                    return;
                }
                const d = res.data;
                document.getElementById('hist-student-name').textContent = d.student.name;
                document.getElementById('hist-student-nim').textContent = `NIM: ${d.student.nim} | ${d.student.study_program || ''}`;

                let timelineHtml = '';
                if (d.history && d.history.length > 0) {
                    d.history.forEach(item => {
                        timelineHtml += `
                            <div class="flex items-center justify-between p-2.5 bg-gray-50 rounded-xl text-xs">
                                <div>
                                    <div class="font-semibold text-gray-800">${item.session_name} (${item.session_type})</div>
                                    <div class="text-gray-500 text-[11px]">${item.time} &bull; Dicatat oleh ${item.mentor_name}</div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-block px-2 py-0.5 rounded font-bold text-xs bg-purple-100 text-purple-800">+${item.score_points} Poin</span>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    timelineHtml = '<div class="text-xs text-gray-400 italic text-center py-2">Belum ada aktivitas presensi tercatat.</div>';
                }

                body.innerHTML = `
                    <!-- Stats Grid -->
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="bg-purple-50 p-3 rounded-2xl border border-purple-100">
                            <div class="text-[11px] text-purple-600 font-medium">Total Poin</div>
                            <div class="text-xl font-bold text-purple-700">${d.assessment.total_points}</div>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-2xl border border-blue-100">
                            <div class="text-[11px] text-blue-600 font-medium">Nilai Akhir</div>
                            <div class="text-xl font-bold text-blue-700">${d.assessment.final_score}%</div>
                        </div>
                        <div class="bg-emerald-50 p-3 rounded-2xl border border-emerald-100">
                            <div class="text-[11px] text-emerald-600 font-medium">Predikat</div>
                            <div class="text-xl font-bold text-emerald-700">${d.assessment.grade}</div>
                        </div>
                    </div>

                    <!-- Timeline Log -->
                    <div class="mt-4">
                        <h5 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Log Riwayat Presensi</h5>
                        <div class="space-y-1.5 max-h-48 overflow-y-auto">
                            ${timelineHtml}
                        </div>
                    </div>
                `;
            })
            .catch(err => {
                body.innerHTML = '<div class="text-center py-6 text-red-500">Terjadi kesalahan koneksi.</div>';
            });
    }
</script>
@endpush
