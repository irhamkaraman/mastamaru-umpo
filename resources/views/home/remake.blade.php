@extends('layouts.home')

@section('title', 'Input Manual Peserta Mastamaru 2026')

@php
    // Konfigurasi formulir - ubah ke false untuk menutup formulir
    $formIsOpen = true;
@endphp

@section('content')

    <!-- Message Display menggunakan SweetAlert2 -->
    @if (session('success') && session('new_participant'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Data peserta baru dari session
                const newParticipant = @json(session('new_participant'));

                // Jika data berhasil disimpan ke database, tandai device dan simpan ke cache permanen
                if (newParticipant.saved_to_database) {
                    // Tandai device sudah submit
                    markDeviceAsSubmitted();

                    // Simpan ke localStorage untuk cache browser (tidak bisa dihapus)
                    let participantHistory = JSON.parse(localStorage.getItem('participant_history') || '[]');
                    let permanentHistory = JSON.parse(localStorage.getItem('permanent_participant_history') || '[]');

                    const participantData = {
                        ...newParticipant,
                        timestamp: new Date().toISOString(),
                        permanent: true,
                        database_saved: true
                    };

                    // Tambah ke history biasa
                    participantHistory.unshift(participantData);

                    // Tambah ke history permanen (tidak bisa dihapus)
                    permanentHistory.unshift(participantData);

                    // Batasi history maksimal 10 peserta terakhir
                    if (participantHistory.length > 10) {
                        participantHistory = participantHistory.slice(0, 10);
                    }
                    if (permanentHistory.length > 50) {
                        permanentHistory = permanentHistory.slice(0, 50);
                    }

                    localStorage.setItem('participant_history', JSON.stringify(participantHistory));
                    localStorage.setItem('permanent_participant_history', JSON.stringify(permanentHistory));
                }

                // Tampilkan notifikasi sukses dengan detail
                Swal.fire({
                    icon: 'success',
                    title: 'Peserta Berhasil Didaftarkan!',
                    html: `
                        <div class="text-left">
                            <p><strong>Nama:</strong> ${newParticipant.name}</p>
                            <p><strong>NIM:</strong> ${newParticipant.student_id}</p>
                            <p><strong>Fakultas:</strong> ${newParticipant.faculty}</p>
                            <p><strong>Program Studi:</strong> ${newParticipant.study_program}</p>
                            <p><strong>Kelompok:</strong> ${newParticipant.group_name}</p>
                            <p><strong>Mentor:</strong> ${newParticipant.mentor_name}</p>
                            <p><strong>Kode Unik:</strong> <span class="font-mono bg-gray-100 px-2 py-1 rounded">${newParticipant.unique_code}</span></p>
                            ${newParticipant.saved_to_database ? '<p class="text-green-600 text-sm mt-2"><i class="fas fa-check-circle"></i> Data tersimpan di database</p>' : ''}
                        </div>
                    `,
                    timer: 8000,
                    timerProgressBar: true,
                    showConfirmButton: true,
                    confirmButtonText: 'OK'
                });

                // Tampilkan history di halaman
                displayParticipantHistory();
            });
        </script>
    @elseif (session('success'))
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

    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="text-center">
                <h1 class="text-3xl sm:text-4xl font-bold mb-2">Input Manual Peserta</h1>
                <p class="text-blue-100 text-lg">Khusus untuk peserta yang belum memiliki NIM resmi</p>
                <p class="text-blue-200 text-sm mt-2">Anda dapat menggunakan angka sesuai keinginan sebagai pengganti NIM</p>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($formIsOpen)
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Formulir Data Peserta</h2>
                    <p class="text-gray-600 mt-1">Lengkapi semua field yang diperlukan</p>
                </div>

                <form action="{{ route('home.store-participant') }}" method="POST" class="p-6">
                @csrf

                <!-- General Error Message -->
                @if($errors->has('general'))
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                            <div>
                                <h4 class="text-red-800 font-medium">Peringatan Keamanan</h4>
                                <p class="text-red-700 text-sm mt-1">{{ $errors->first('general') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Grid Layout untuk Form -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Nama Lengkap -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('name') border-red-500 @enderror"
                               placeholder="Masukkan nama lengkap peserta"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NIM -->
                    <div>
                        <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">
                            NIM / Nomor Identitas <span class="text-red-500">*</span>
                            <span class="text-xs text-gray-500 font-normal">(Hanya angka)</span>
                        </label>
                        <input type="number"
                               id="student_id"
                               name="student_id"
                               value="{{ old('student_id') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('student_id') border-red-500 @enderror"
                               placeholder="Masukkan angka sesuai keinginan (contoh: 123456789)"
                               min="1"
                               max="99999999999999999999"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               required>
                        <p class="mt-1 text-xs text-gray-500">Gunakan angka sesuai keinginan Anda sebagai pengganti NIM resmi</p>
                        @error('student_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fakultas -->
                    <div>
                        <label for="faculty" class="block text-sm font-medium text-gray-700 mb-2">
                            Fakultas <span class="text-red-500">*</span>
                        </label>
                        <select id="faculty"
                                name="faculty"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('faculty') border-red-500 @enderror"
                                required>
                            <option value="">Pilih Fakultas</option>
                            @foreach($faculties as $faculty)
                                <option value="{{ $faculty }}" {{ old('faculty') == $faculty ? 'selected' : '' }}>
                                    {{ $faculty }}
                                </option>
                            @endforeach
                        </select>
                        @error('faculty')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Program Studi -->
                    <div>
                        <label for="study_program" class="block text-sm font-medium text-gray-700 mb-2">
                            Program Studi <span class="text-red-500">*</span>
                        </label>
                        <select id="study_program"
                                name="study_program"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('study_program') border-red-500 @enderror"
                                required>
                            <option value="">Pilih Program Studi</option>
                            @foreach($studyPrograms as $program)
                                <option value="{{ $program }}" {{ old('study_program') == $program ? 'selected' : '' }}>
                                    {{ $program }}
                                </option>
                            @endforeach
                        </select>
                        @error('study_program')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>


                </div>

                <!-- Submit Button -->
                <div class="mt-8">
                    <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 px-6 rounded-lg font-medium hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 transform hover:scale-105">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Kirim Data
                    </button>
                    </div>
                </form>
            </div>
        @else
            <!-- Pesan Pendaftaran Ditutup -->
            <div class="bg-white rounded-xl shadow-lg border border-red-200 overflow-hidden">
                <div class="bg-red-50 px-6 py-4 border-b border-red-200">
                    <h2 class="text-xl font-semibold text-red-800">Pendaftaran Telah Ditutup</h2>
                    <p class="text-red-600 mt-1">Maaf, pendaftaran MASTAMARU 2025 sudah tidak tersedia</p>
                </div>

                <div class="p-6">
                    <div class="text-center">
                        <div class="mb-6">
                            <i class="fas fa-times-circle text-red-500 text-6xl mb-4"></i>
                            <h3 class="text-2xl font-bold text-red-800 mb-2">Pendaftaran Ditutup</h3>
                            <p class="text-red-600 text-lg mb-4">Periode pendaftaran MASTAMARU 2025 telah berakhir</p>
                        </div>

                        <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-red-500 text-xl"></i>
                                </div>
                                <div class="ml-3 text-left">
                                    <h4 class="text-red-800 font-medium mb-2">Informasi Penting</h4>
                                    <div class="text-red-700 text-sm space-y-2">
                                        <p><strong>• Peserta tidak dapat mengikuti MASTAMARU tahun 2025</strong></p>
                                        <p>• Silakan tunggu dan ikuti MASTAMARU yang akan datang</p>
                                        <p>• Pantau terus informasi resmi dari panitia untuk pendaftaran tahun berikutnya</p>
                                        <p>• Pastikan untuk mendaftar lebih awal pada periode pendaftaran yang akan datang</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <div class="text-center">
                                <i class="fas fa-heart text-blue-500 text-2xl mb-3"></i>
                                <h4 class="text-blue-800 font-medium text-lg mb-2">Terima Kasih</h4>
                                <p class="text-blue-700">Terima kasih atas minat dan antusiasme Anda untuk mengikuti MASTAMARU 2025. Meskipun pendaftaran telah ditutup, kami sangat menghargai keinginan Anda untuk bergabung dengan keluarga besar MASTAMARU.</p>
                                <p class="text-blue-700 mt-2">Sampai jumpa di MASTAMARU yang akan datang!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif


        <!-- History Peserta Terdaftar -->
        <div id="participant-history" class="mt-8" style="display: none;">
            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-green-800">
                        <i class="fas fa-history mr-2"></i>
                        Peserta Terdaftar Terakhir
                    </h3>
                    <button onclick="clearParticipantHistory()" class="text-sm text-red-600 hover:text-red-800 underline">
                        Hapus History
                    </button>
                </div>
                <div id="history-list" class="space-y-3">
                    <!-- History items akan ditampilkan di sini -->
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Informasi Penting</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li><strong>Halaman ini khusus untuk peserta yang belum memiliki NIM resmi</strong></li>
                            <li>Gunakan angka sesuai keinginan Anda sebagai pengganti NIM (hanya angka, 1-20 digit)</li>
                            <li>Pastikan angka yang dimasukkan belum terdaftar dalam sistem</li>
                            <li>Data fakultas dan program studi akan diambil dari data yang sudah ada</li>
                            <li>Kelompok dan mentor akan ditentukan secara otomatis berdasarkan distribusi yang merata</li>
                            <li>Setiap peserta akan mendapatkan kode unik secara otomatis</li>
                            <li>Data yang sudah disimpan dapat dilihat di halaman daftar kelompok</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Custom Styles -->
    <style>
        /* Custom focus styles */
        .focus\:ring-2:focus {
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
        }

        /* Smooth transitions */
        input, select {
            transition: all 0.2s ease-in-out;
        }

        /* Hover effects */
        input:hover, select:hover {
            border-color: #93c5fd;
        }

        /* Mobile responsiveness */
        @media (max-width: 640px) {
            .grid {
                gap: 1rem;
            }

            .px-6 {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
    </style>

    <!-- JavaScript untuk History Management dan Keamanan -->
    <script>
        // Fungsi untuk generate device fingerprint sederhana
        function generateDeviceFingerprint() {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            ctx.textBaseline = 'top';
            ctx.font = '14px Arial';
            ctx.fillText('Device fingerprint', 2, 2);

            const fingerprint = [
                navigator.userAgent,
                navigator.language,
                screen.width + 'x' + screen.height,
                new Date().getTimezoneOffset(),
                canvas.toDataURL()
            ].join('|');

            // Hash sederhana untuk fingerprint
            let hash = 0;
            for (let i = 0; i < fingerprint.length; i++) {
                const char = fingerprint.charCodeAt(i);
                hash = ((hash << 5) - hash) + char;
                hash = hash & hash; // Convert to 32bit integer
            }
            return Math.abs(hash).toString(36);
        }

        // Fungsi untuk cek apakah device sudah berhasil menyimpan data ke database
        function checkDeviceSubmission() {
            const deviceId = generateDeviceFingerprint();
            const submittedDevices = JSON.parse(localStorage.getItem('database_submitted_devices') || '[]');
            return submittedDevices.includes(deviceId);
        }

        // Fungsi untuk menandai device sudah berhasil menyimpan ke database
        function markDeviceAsSubmitted() {
            const deviceId = generateDeviceFingerprint();
            let submittedDevices = JSON.parse(localStorage.getItem('database_submitted_devices') || '[]');
            if (!submittedDevices.includes(deviceId)) {
                submittedDevices.push(deviceId);
                localStorage.setItem('database_submitted_devices', JSON.stringify(submittedDevices));
            }
        }

        // Fungsi untuk disable form jika sudah berhasil menyimpan ke database
        function disableFormIfSubmitted() {
            if (checkDeviceSubmission()) {
                const form = document.querySelector('form');
                const inputs = form.querySelectorAll('input, select, button');
                const submitBtn = form.querySelector('button[type="submit"]');

                inputs.forEach(input => {
                    input.disabled = true;
                });

                submitBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Data Sudah Tersimpan di Sistem';
                submitBtn.classList.remove('hover:from-blue-700', 'hover:to-purple-700', 'hover:scale-105');
                submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed');

                // Tampilkan pesan peringatan
                const warningDiv = document.createElement('div');
                warningDiv.className = 'mt-4 bg-green-50 border border-green-200 rounded-lg p-4';
                warningDiv.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <div>
                            <h4 class="text-green-800 font-medium">Data Sudah Tersimpan di Sistem</h4>
                            <p class="text-green-700 text-sm mt-1">Perangkat ini sudah berhasil menyimpan data ke sistem. Untuk mencegah duplikasi, formulir telah dinonaktifkan.</p>
                        </div>
                    </div>
                `;
                form.parentNode.insertBefore(warningDiv, form.nextSibling);
            }
        }

        // Fungsi untuk menampilkan history peserta
        function displayParticipantHistory() {
            const participantHistory = JSON.parse(localStorage.getItem('participant_history') || '[]');
            const permanentHistory = JSON.parse(localStorage.getItem('permanent_participant_history') || '[]');
            const historyContainer = document.getElementById('participant-history');
            const historyList = document.getElementById('history-list');

            // Gabungkan history biasa dan permanen, hilangkan duplikat
            const allHistory = [...participantHistory];
            permanentHistory.forEach(permanent => {
                if (!allHistory.find(item => item.unique_code === permanent.unique_code)) {
                    allHistory.push(permanent);
                }
            });

            // Urutkan berdasarkan timestamp terbaru
            allHistory.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));

            if (allHistory.length === 0) {
                historyContainer.style.display = 'none';
                return;
            }

            historyContainer.style.display = 'block';
            historyList.innerHTML = '';

            allHistory.forEach((participant, index) => {
                const timestamp = new Date(participant.timestamp).toLocaleString('id-ID');
                const isPermanent = participant.permanent || participant.database_saved;
                const historyItem = document.createElement('div');
                historyItem.className = `bg-white border rounded-lg p-4 ${
                    isPermanent ? 'border-green-300 bg-green-50' : 'border-green-200'
                }`;
                historyItem.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div>
                            <p><strong>Nama:</strong> ${participant.name}</p>
                            <p><strong>NIM:</strong> ${participant.student_id}</p>
                            <p><strong>Fakultas:</strong> ${participant.faculty}</p>
                            <p><strong>Program Studi:</strong> ${participant.study_program}</p>
                        </div>
                        <div>
                            <p><strong>Kelompok:</strong> ${participant.group_name}</p>
                            <p><strong>Mentor:</strong> ${participant.mentor_name}</p>
                            <p><strong>Kode Unik:</strong> <span class="font-mono bg-gray-100 px-2 py-1 rounded text-xs">${participant.unique_code}</span></p>
                            <p class="text-gray-500 text-xs mt-2"><strong>Didaftarkan:</strong> ${timestamp}</p>
                            ${isPermanent ? '<p class="text-green-600 text-xs mt-1"><i class="fas fa-database"></i> Bila terjadi kekeliruan data, silakan hubungi panitia.</p>' : ''}
                        </div>
                    </div>
                `;
                historyList.appendChild(historyItem);
            });
        }

        // Fungsi untuk menghapus history (hanya yang tidak permanen)
        function clearParticipantHistory() {
            const permanentHistory = JSON.parse(localStorage.getItem('permanent_participant_history') || '[]');
            const hasPermanentData = permanentHistory.length > 0;

            let confirmMessage = 'Apakah Anda yakin ingin menghapus history peserta?';
            if (hasPermanentData) {
                confirmMessage += '\n\nCatatan: Data yang sudah tersimpan di database tidak akan dihapus.';
            }

            if (confirm(confirmMessage)) {
                // Hanya hapus history biasa, biarkan permanent history
                localStorage.removeItem('participant_history');

                // Refresh tampilan
                displayParticipantHistory();

                // Jika tidak ada data permanen, sembunyikan container
                if (!hasPermanentData) {
                    document.getElementById('participant-history').style.display = 'none';
                }
            }
        }

        // Fungsi untuk validasi form submission (hanya cek jika sudah tersimpan ke database)
        function validateFormSubmission(event) {
            if (checkDeviceSubmission()) {
                event.preventDefault();
                Swal.fire({
                    icon: 'info',
                    title: 'Data Sudah Tersimpan di Database',
                    text: 'Perangkat ini sudah berhasil menyimpan data ke database. Formulir telah dinonaktifkan untuk mencegah duplikasi.',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            return true;
        }

        // Event listener saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Cek dan disable form jika sudah berhasil menyimpan ke database
            disableFormIfSubmitted();

            // Tampilkan history peserta
            displayParticipantHistory();

            // Tambahkan event listener untuk form submission
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', validateFormSubmission);
            }
        });
    </script>

@endsection
