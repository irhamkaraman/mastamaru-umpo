<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSubmission;
use App\Models\Group;
use App\Models\StudentAssessment;
use App\Services\ScoreCalculationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
            'student_id' => 'required|string|max:20',
        ]);
        $studentId = $request->input('student_id');
        $student = Cache::remember('student_data_'.$studentId, 300, function () use ($studentId) {
            return Attendance::with(['group', 'mentor'])
                ->where('student_id', $studentId)
                ->first();
        });
        if (! $student) {
            return back()->with('error', 'Peserta dengan NIM tersebut tidak terdaftar dalam sistem.');
        }
        $uniqueCode = $this->generateUniqueCode();
        $student->update([
            'unique_code' => $uniqueCode,
        ]);
        Cache::forget('student_data_'.$student->student_id);
        $rawBarcode = json_encode([
            'nama' => $student->name,
            'student_id' => $student->student_id,
            'fakultas' => $student->faculty,
            'mentor' => $student->mentor ? $student->mentor->name : 'Belum ditentukan',
        ], JSON_UNESCAPED_UNICODE);
        $assessment = StudentAssessment::firstOrCreate(
            ['student_id' => $student->id],
            [
                'total_presence_points' => 0,
                'activity_score' => null,
                'attendance_score' => 0,
                'final_score' => 0,
                'grade' => 'D',
                'status' => 'proses',
            ]
        );
        $matrix = ScoreCalculationService::getStudentPresenceMatrix($student->id);
        $submissions = AttendanceSubmission::where('student_id', $student->id)
            ->with(['presenceSession', 'mentor'])
            ->orderBy('submitted_at', 'desc')
            ->get();
        $student->refresh();
        $certificateFile = null;
        if (!empty($student->certificate_file)) {
            $path = storage_path('app/public/' . $student->certificate_file);
            if (file_exists($path)) {
                $certificateFile = asset('storage/' . $student->certificate_file);
            }
        }

        return view('home.student-info', [
            'student' => $student,
            'uniqueCode' => $uniqueCode,
            'rawBarcode' => $rawBarcode,
            'certificateUrl' => $certificateFile,
            'assessment' => $assessment,
            'matrix' => $matrix,
            'submissions' => $submissions,
        ]);
    }

    /**
     * Generate kode unik 8 karakter (huruf besar + angka)
     * Pastikan tidak ada duplikasi dengan peserta lain
     */
    private function generateUniqueCode(): string
    {
        do {
            $code = '';
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            for ($i = 0; $i < 8; $i++) {
                $code .= $characters[rand(0, strlen($characters) - 1)];
            }
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
            'student_id' => 'required|string|exists:attendances,student_id',
        ]);
        $student = Attendance::with(['group', 'mentor'])
            ->where('student_id', $request->student_id)
            ->first();
        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'Peserta tidak ditemukan',
            ]);
        }
        $uniqueCode = $this->generateUniqueCode();
        $student->update([
            'unique_code' => $uniqueCode,
        ]);
        Cache::forget('student_data_'.$student->student_id);
        $rawBarcode = json_encode([
            'nama' => $student->name,
            'student_id' => $student->student_id,
            'fakultas' => $student->faculty,
            'mentor' => $student->mentor ? $student->mentor->name : 'Belum ditentukan',
        ], JSON_UNESCAPED_UNICODE);

        return response()->json([
            'success' => true,
            'uniqueCode' => $uniqueCode,
            'rawBarcode' => $rawBarcode,
        ]);
    }

    /**
     * Tampilkan halaman daftar kelompok dengan pendamping dan peserta
     */
    public function groups()
    {
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
        $request->validate([
            'name' => 'required|string|max:255',
            'student_id' => 'required|numeric|digits_between:1,20|unique:attendances,student_id',
            'faculty' => 'required|string|max:255',
            'study_program' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama wajib diisi',
            'student_id.required' => 'NIM wajib diisi',
            'student_id.numeric' => 'NIM harus berupa angka',
            'student_id.digits_between' => 'NIM harus terdiri dari 1-20 digit angka',
            'student_id.unique' => 'NIM sudah terdaftar dalam sistem',
            'faculty.required' => 'Fakultas wajib dipilih',
            'study_program.required' => 'Program studi wajib dipilih',
        ]);
        $trimmedName = trim($request->name);
        $existingName = Attendance::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($trimmedName)])->first();
        if ($existingName) {
            return back()->withErrors([
                'name' => 'Peserta dengan nama "'.$existingName->name.'" sudah terdaftar dalam sistem (NIM: '.$existingName->student_id.').',
            ])->withInput();
        }
        $availableFaculties = $this->getFaculties();
        $availablePrograms = $this->getStudyPrograms();
        if (! in_array($request->faculty, $availableFaculties)) {
            return back()->withErrors([
                'faculty' => 'Fakultas yang dipilih tidak valid.',
            ])->withInput();
        }
        if (! in_array($request->study_program, $availablePrograms)) {
            return back()->withErrors([
                'study_program' => 'Program studi yang dipilih tidak valid.',
            ])->withInput();
        }
        try {
            $assignedGroup = $this->autoAssignGroup();
            $uniqueCode = $this->generateUniqueCode();
            $rawBarcode = json_encode([
                'nama' => $request->name,
                'student_id' => $request->student_id,
                'fakultas' => $request->faculty,
                'mentor' => 'Akan ditentukan',
            ], JSON_UNESCAPED_UNICODE);
            $newParticipant = Attendance::create([
                'group_id' => $assignedGroup['group_id'],
                'mentor_id' => $assignedGroup['mentor_id'],
                'name' => $request->name,
                'student_id' => $request->student_id,
                'faculty' => $request->faculty,
                'study_program' => $request->study_program,
                'raw_barcode' => $rawBarcode,
                'unique_code' => $uniqueCode,
            ]);
            $newParticipant->load(['group', 'mentor']);
            $updatedRawBarcode = json_encode([
                'nama' => $newParticipant->name,
                'student_id' => $newParticipant->student_id,
                'fakultas' => $newParticipant->faculty,
                'mentor' => $newParticipant->mentor ? $newParticipant->mentor->name : 'Belum ditentukan',
            ], JSON_UNESCAPED_UNICODE);
            $newParticipant->update(['raw_barcode' => $updatedRawBarcode]);
            Cache::forget('faculties_list');
            Cache::forget('faculties_validation');
            Cache::forget('study_programs_list');
            Cache::forget('study_programs_validation');
            Cache::forget('groups_with_participants');
            Cache::forget('groups_for_assignment');
            Cache::forget('student_data_'.$newParticipant->student_id);
            session([
                'new_participant' => [
                    'name' => $newParticipant->name,
                    'student_id' => $newParticipant->student_id,
                    'faculty' => $newParticipant->faculty,
                    'study_program' => $newParticipant->study_program,
                    'group_name' => $newParticipant->group->name,
                    'mentor_name' => $newParticipant->mentor->name,
                    'unique_code' => $newParticipant->unique_code,
                    'saved_to_database' => true,
                ],
            ]);

            return back()->with('success', 'Peserta berhasil ditambahkan ke sistem!');
        } catch (Exception $e) {
            Log::error('Error saat menyimpan peserta: '.$e->getMessage(), [
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'general' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi atau hubungi administrator.',
            ])->withInput();
        }
    }

    /**
     * Auto-assign kelompok berdasarkan distribusi yang merata
     * Pilih kelompok dengan jumlah peserta paling sedikit
     */
    private function autoAssignGroup(): array
    {
        $groups = Cache::remember('groups_for_assignment', 120, function () {
            return Group::withCount('attendances')
                ->with('mentors')
                ->orderBy('attendances_count', 'asc')
                ->orderBy('order', 'asc')
                ->get();
        });
        if ($groups->isEmpty()) {
            throw new Exception('Tidak ada kelompok yang tersedia dalam sistem.');
        }
        $selectedGroup = $groups->first();
        $mentors = $selectedGroup->mentors;
        if ($mentors->isEmpty()) {
            throw new Exception('Kelompok '.$selectedGroup->name.' tidak memiliki mentor.');
        }
        $selectedMentor = $mentors->first();

        return [
            'group_id' => $selectedGroup->id,
            'mentor_id' => $selectedMentor->id,
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
