<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Group;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    /**
     * Tampilkan halaman utama untuk peserta
     */
    public function index()
    {
        return view('home.index');
    }

    /**
     * Periksa peserta berdasarkan NIM dan generate QR code
     */
    public function checkStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|max:20'
        ]);

        $studentId = $request->input('student_id');

        // Cari peserta berdasarkan student_id dengan cache (5 menit)
        $student = Cache::remember('student_data_' . $studentId, 300, function () use ($studentId) {
            return Attendance::with(['group', 'mentor'])
                ->where('student_id', $studentId)
                ->first();
        });

        if (!$student) {
            return back()->with('error', 'Peserta dengan NIM tersebut tidak terdaftar dalam sistem.');
        }

        // Generate kode unik baru (8 karakter kombinasi huruf besar dan angka)
        $uniqueCode = $this->generateUniqueCode();

        // Update kode unik di database
        $student->update([
            'unique_code' => $uniqueCode
        ]);

        // Invalidate cache untuk student yang di-update
            Cache::forget('student_data_' . $student->student_id);

        // Buat rawBarcode dalam format JSON
        $rawBarcode = json_encode([
            'nama' => $student->name,
            'student_id' => $student->student_id,
            'fakultas' => $student->faculty,
            'mentor' => $student->mentor ? $student->mentor->name : 'Belum ditentukan'
        ], JSON_UNESCAPED_UNICODE);

        return view('home.student-info', [
            'student' => $student,
            'uniqueCode' => $uniqueCode,
            'rawBarcode' => $rawBarcode
        ]);
    }

    /**
     * Generate kode unik 8 karakter (huruf besar + angka)
     * Pastikan tidak ada duplikasi dengan peserta lain
     */
    private function generateUniqueCode(): string
    {
        do {
            // Generate 8 karakter random (huruf besar A-Z dan angka 0-9)
            $code = '';
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

            for ($i = 0; $i < 8; $i++) {
                $code .= $characters[rand(0, strlen($characters) - 1)];
            }

            // Cek apakah kode sudah ada di database
            $exists = Attendance::where('unique_code', $code)->exists();

        } while ($exists);

        return $code;
    }

    /**
     * Refresh kode unik untuk peserta
     */
    public function refreshCode(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|exists:attendances,student_id'
        ]);

        $student = Attendance::with(['group', 'mentor'])
            ->where('student_id', $request->student_id)
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Peserta tidak ditemukan'
            ]);
        }

        // Generate kode unik baru
        $uniqueCode = $this->generateUniqueCode();

        // Update kode unik di database
        $student->update([
            'unique_code' => $uniqueCode
        ]);

        // Invalidate cache untuk student yang di-update
        Cache::forget('student_data_' . $student->student_id);

        // Buat rawBarcode dalam format JSON
        $rawBarcode = json_encode([
            'nama' => $student->name,
            'student_id' => $student->student_id,
            'fakultas' => $student->faculty,
            'mentor' => $student->mentor ? $student->mentor->name : 'Belum ditentukan'
        ], JSON_UNESCAPED_UNICODE);

        return response()->json([
            'success' => true,
            'uniqueCode' => $uniqueCode,
            'rawBarcode' => $rawBarcode
        ]);
    }

    /**
     * Tampilkan halaman daftar kelompok dengan pendamping dan peserta
     */
    public function groups()
    {
        // Ambil data groups dengan relasi langsung tanpa caching
        // Caching sudah dihandle di view level
        $groups = Group::with(['mentors', 'attendances'])
            ->orderBy('order')
            ->get();

        return view('home.group', compact('groups'));
    }

    /**
     * Tampilkan halaman form input manual peserta
     */
    public function remake()
    {
        // Ambil data fakultas dan prodi yang unik dari tabel attendance dengan cache
        $faculties = Cache::remember('faculties_list', 3600, function () {
            return Attendance::whereNotNull('faculty')
                ->distinct()
                ->pluck('faculty')
                ->sort()
                ->values();
        });

        $studyPrograms = Cache::remember('study_programs_list', 3600, function () {
            return Attendance::whereNotNull('study_program')
                ->distinct()
                ->pluck('study_program')
                ->sort()
                ->values();
        });

        return view('home.remake', compact('faculties', 'studyPrograms'));
    }

    /**
     * Simpan data peserta baru dari form input manual
     */
    public function storeParticipant(Request $request)
    {
        // Validasi dasar
        $request->validate([
            'name' => 'required|string|max:255',
            'student_id' => 'required|numeric|digits_between:1,20|unique:attendances,student_id',
            'faculty' => 'required|string|max:255',
            'study_program' => 'required|string|max:255'
        ], [
            'name.required' => 'Nama wajib diisi',
            'student_id.required' => 'NIM wajib diisi',
            'student_id.numeric' => 'NIM harus berupa angka',
            'student_id.digits_between' => 'NIM harus terdiri dari 1-20 digit angka',
            'student_id.unique' => 'NIM sudah terdaftar dalam sistem',
            'faculty.required' => 'Fakultas wajib dipilih',
            'study_program.required' => 'Program studi wajib dipilih'
        ]);

        // Validasi anti-bot: Cek apakah nama sudah ada di sistem
        $existingName = Attendance::where('name', 'LIKE', '%' . trim($request->name) . '%')
            ->orWhere('name', 'LIKE', trim($request->name) . '%')
            ->orWhere('name', 'LIKE', '%' . trim($request->name))
            ->first();

        if ($existingName) {
            return back()->withErrors([
                'name' => 'Nama peserta sudah terdaftar dalam sistem. Silakan gunakan nama yang berbeda atau hubungi admin jika ini adalah kesalahan.'
            ])->withInput();
        }

        // Validasi anti-bot: Cek apakah fakultas dan program studi valid dari data yang tersedia
        $availableFaculties = $this->getFaculties();
        $availablePrograms = $this->getStudyPrograms();

        if (!in_array($request->faculty, $availableFaculties)) {
            return back()->withErrors([
                'faculty' => 'Fakultas yang dipilih tidak valid.'
            ])->withInput();
        }

        if (!in_array($request->study_program, $availablePrograms)) {
            return back()->withErrors([
                'study_program' => 'Program studi yang dipilih tidak valid.'
            ])->withInput();
        }

        // Validasi rate limiting: Cek apakah ada submission dalam 5 menit terakhir dari IP yang sama
        $recentSubmission = Attendance::where('created_at', '>=', now()->subMinutes(5))
            ->where('raw_barcode', 'LIKE', '%' . $request->ip() . '%')
            ->exists();

        if ($recentSubmission) {
            return back()->withErrors([
                'general' => 'Terlalu banyak submission dalam waktu singkat. Silakan tunggu beberapa menit sebelum mencoba lagi.'
            ])->withInput();
        }

        try {
            // Auto-assign kelompok berdasarkan distribusi yang merata
            $assignedGroup = $this->autoAssignGroup();

            // Generate kode unik untuk peserta baru
            $uniqueCode = $this->generateUniqueCode();

            // Generate raw barcode dalam format JSON
            $rawBarcode = json_encode([
                'nama' => $request->name,
                'student_id' => $request->student_id,
                'fakultas' => $request->faculty,
                'mentor' => 'Akan ditentukan' // Mentor akan di-update setelah assignment
            ], JSON_UNESCAPED_UNICODE);

            // Simpan data peserta baru
            $newParticipant = Attendance::create([
                'group_id' => $assignedGroup['group_id'],
                'mentor_id' => $assignedGroup['mentor_id'],
                'name' => $request->name,
                'student_id' => $request->student_id,
                'faculty' => $request->faculty,
                'study_program' => $request->study_program,
                'raw_barcode' => $rawBarcode,
                'unique_code' => $uniqueCode
            ]);

            // Load relasi untuk mendapatkan nama kelompok dan mentor
            $newParticipant->load(['group', 'mentor']);

            // Update rawBarcode dengan informasi mentor yang sebenarnya
            $updatedRawBarcode = json_encode([
                'nama' => $newParticipant->name,
                'student_id' => $newParticipant->student_id,
                'fakultas' => $newParticipant->faculty,
                'mentor' => $newParticipant->mentor ? $newParticipant->mentor->name : 'Belum ditentukan'
            ], JSON_UNESCAPED_UNICODE);

            $newParticipant->update(['raw_barcode' => $updatedRawBarcode]);

            // Invalidate cache setelah data baru ditambahkan
            Cache::forget('faculties_list');
            Cache::forget('faculties_validation');
            Cache::forget('study_programs_list');
            Cache::forget('study_programs_validation');
            Cache::forget('groups_with_participants');
            Cache::forget('groups_for_assignment');
            Cache::forget('student_data_' . $newParticipant->student_id);

            // Simpan data ke session untuk ditampilkan di cache browser
            session([
                'new_participant' => [
                    'name' => $newParticipant->name,
                    'student_id' => $newParticipant->student_id,
                    'faculty' => $newParticipant->faculty,
                    'study_program' => $newParticipant->study_program,
                    'group_name' => $newParticipant->group->name,
                    'mentor_name' => $newParticipant->mentor->name,
                    'unique_code' => $newParticipant->unique_code,
                    'saved_to_database' => true
                ]
            ]);

            return back()->with('success', 'Peserta berhasil ditambahkan ke sistem!');

        } catch (\Exception $e) {
            // Log error untuk debugging
            Log::error('Error saat menyimpan peserta: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors([
                'general' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi atau hubungi administrator.'
            ])->withInput();
        }
    }

    /**
     * Auto-assign kelompok berdasarkan distribusi yang merata
     * Pilih kelompok dengan jumlah peserta paling sedikit
     */
    private function autoAssignGroup(): array
    {
        // Ambil semua kelompok dengan jumlah peserta saat ini (cache 2 menit karena data berubah)
        $groups = Cache::remember('groups_for_assignment', 120, function () {
            return Group::withCount('attendances')
                ->with('mentors')
                ->orderBy('attendances_count', 'asc')
                ->orderBy('order', 'asc')
                ->get();
        });

        if ($groups->isEmpty()) {
            throw new \Exception('Tidak ada kelompok yang tersedia dalam sistem.');
        }

        // Pilih kelompok dengan peserta paling sedikit
        $selectedGroup = $groups->first();

        // Pilih mentor dari kelompok tersebut
        $mentors = $selectedGroup->mentors;
        if ($mentors->isEmpty()) {
            throw new \Exception('Kelompok ' . $selectedGroup->name . ' tidak memiliki mentor.');
        }

        // Pilih mentor pertama (bisa dikembangkan untuk distribusi yang lebih kompleks)
        $selectedMentor = $mentors->first();

        return [
            'group_id' => $selectedGroup->id,
            'mentor_id' => $selectedMentor->id
        ];
    }

    /**
     * Ambil daftar fakultas yang tersedia dari database dengan cache
     */
    private function getFaculties(): array
    {
        return Cache::remember('faculties_validation', 3600, function () {
            return Attendance::whereNotNull('faculty')
                ->distinct()
                ->pluck('faculty')
                ->toArray();
        });
    }

    /**
     * Ambil daftar program studi yang tersedia dari database dengan cache
     */
    private function getStudyPrograms(): array
    {
        return Cache::remember('study_programs_validation', 3600, function () {
            return Attendance::whereNotNull('study_program')
                ->distinct()
                ->pluck('study_program')
                ->toArray();
        });
    }
}
