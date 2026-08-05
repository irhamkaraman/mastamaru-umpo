<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSubmission;
use App\Models\PresenceSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PresenceController extends Controller
{
    public function show($slug)
    {
        $session = PresenceSession::where('slug', $slug)->firstOrFail();
        $mentorId = session('mentor_id');
        $groupId = session('mentor_group_id');

        // Ambil semua peserta dalam kelompok
        $allStudents = Attendance::where('group_id', $groupId)->get();

        // Ambil peserta yang sudah presensi
        $presentStudents = AttendanceSubmission::where('presence_session_id', $session->id)
            ->where('group_id', $groupId)
            ->with('student')
            ->latest('submitted_at')
            ->get();

        // Ambil ID peserta yang sudah presensi
        $presentStudentIds = $presentStudents->pluck('student_id')->toArray();

        // Ambil peserta yang belum presensi menggunakan query database
        $absentStudents = Attendance::where('group_id', $groupId)
            ->whereNotIn('id', $presentStudentIds)
            ->get();

        return view('mentor.presence-detail', [
            'session' => $session,
            'allStudents' => $allStudents,
            'presentStudents' => $presentStudents,
            'absentStudents' => $absentStudents,
            'totalPresent' => $presentStudents->count(),
            'totalAbsent' => $absentStudents->count(),
            'totalStudents' => $allStudents->count(),
            'slug' => $slug
        ]);
    }

    public function checkSessionStatus(Request $request, $slug)
    {
        try {
            $request->validate([
                'current_time' => 'required|string'
            ]);

            $session = PresenceSession::where('slug', $slug)->firstOrFail();

            // Parse waktu dari JavaScript (format MySQL datetime)
            $currentTime = Carbon::createFromFormat('Y-m-d H:i:s', $request->input('current_time'));
            $endTime = Carbon::parse($session->end_time);

            $isSessionActive = $currentTime->lte($endTime);

            return response()->json([
                'success' => true,
                'session_active' => $isSessionActive,
                'session_end_time' => $endTime->format('Y-m-d H:i:s'),
                'current_time' => $currentTime->format('Y-m-d H:i:s'),
                'redirect_url' => $isSessionActive ? null : route('mentor.dashboard')
            ]);
        } catch (\Exception $e) {
            // Jika terjadi error (data tidak valid, session tidak ditemukan, dll)
            return redirect()->route('home.index')->with('error', 'Terjadi kesalahan saat memproses permintaan. Silakan coba lagi.');
        }
    }

    public function getAttendanceData($slug)
    {
        $session = PresenceSession::where('slug', $slug)->firstOrFail();
        $mentorId = session('mentor_id');
        $groupId = session('mentor_group_id');

        // Ambil semua peserta dalam kelompok
        $allStudents = Attendance::where('group_id', $groupId)->get();

        // Ambil peserta yang sudah presensi
        $presentStudents = AttendanceSubmission::where('presence_session_id', $session->id)
            ->where('group_id', $groupId)
            ->with('student')
            ->latest('submitted_at')
            ->get();

        // Ambil ID peserta yang sudah presensi
        $presentStudentIds = $presentStudents->pluck('student_id')->toArray();

        // Ambil peserta yang belum presensi menggunakan query database
        $absentStudents = Attendance::where('group_id', $groupId)
            ->whereNotIn('id', $presentStudentIds)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'presentStudents' => $presentStudents->map(function ($submission) {
                    return [
                        'id' => $submission->id,
                        'student_name' => $submission->student->name,
                        'student_id' => $submission->student->student_id,
                        'faculty' => $submission->student->faculty ?? 'Tidak tersedia',
                        'study_program' => $submission->student->study_program ?? 'Tidak tersedia',
                        'submitted_at' => $submission->submitted_at->format('H:i:s'),
                        'submission_method' => $submission->submission_method,
                        'status' => $submission->status
                    ];
                }),
                'absentStudents' => $absentStudents->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->name,
                        'student_id' => $student->student_id,
                        'faculty' => $student->faculty ?? 'Tidak tersedia',
                        'study_program' => $student->study_program ?? 'Tidak tersedia',
                        'unique_code' => $student->unique_code
                    ];
                }),
                'totalPresent' => $presentStudents->count(),
                'totalAbsent' => $absentStudents->count(),
                'totalStudents' => $allStudents->count()
            ]
        ]);
    }

    public function processScan(Request $request, $slug)
    {
        $request->validate([
            'code' => 'required|string',
            'device_timestamp' => 'required|string'
        ]);

        $session = PresenceSession::where('slug', $slug)->firstOrFail();
        $mentorId = session('mentor_id');
        $groupId = session('mentor_group_id');
        $rawCode = $request->input('code');

        // Parse JSON data dari QR code
        try {
            $qrData = json_decode($rawCode, true);
            if (!$qrData || !isset($qrData['student_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format QR code tidak valid. Data harus berupa JSON dengan field student_id.'
                ], 400);
            }
            $studentId = $qrData['student_id'];
        } catch (\Exception $e) {
            // Jika bukan JSON, gunakan sebagai kode biasa
            $studentId = $rawCode;
        }

        // Validasi waktu sesi menggunakan waktu dari perangkat
        // Timestamp sudah dalam format MySQL datetime dari JavaScript
        $deviceTime = Carbon::createFromFormat('Y-m-d H:i:s', $request->input('device_timestamp'));
        $startTime = Carbon::parse($session->start_time);
        $endTime = Carbon::parse($session->end_time);

        if ($deviceTime->lt($startTime)) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi presensi belum dimulai. Waktu mulai: ' . $startTime->format('d/m/Y H:i')
            ], 400);
        }

        if ($deviceTime->gt($endTime)) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi presensi sudah berakhir. Waktu berakhir: ' . $endTime->format('d/m/Y H:i')
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Cari peserta berdasarkan student_id, kode unik atau raw_barcode
            $student = Attendance::where('group_id', $groupId)
                ->where(function ($query) use ($studentId, $rawCode) {
                    $query->where('student_id', $studentId)
                        ->orWhere('unique_code', $studentId)
                        ->orWhere('raw_barcode', $studentId)
                        ->orWhere('unique_code', $rawCode)
                        ->orWhere('raw_barcode', $rawCode);
                })
                ->first();

            if (!$student) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Kode tidak valid atau peserta tidak ditemukan dalam kelompok Anda.'
                ], 404);
            }

            // Cek apakah sudah presensi
            $existingSubmission = AttendanceSubmission::where('presence_session_id', $session->id)
                ->where('student_id', $student->id)
                ->first();

            if ($existingSubmission) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Peserta ' . $student->name . ' sudah melakukan presensi sebelumnya.',
                    'type' => 'warning'
                ], 409);
            }

            // Simpan data presensi langsung ke database
            AttendanceSubmission::create([
                'presence_session_id' => $session->id,
                'group_id' => $groupId,
                'mentor_id' => $mentorId,
                'student_id' => $student->id,
                'submitted_at' => $deviceTime,
                'status' => 'hadir',
                'submission_method' => 'qr_scan',
                'notes' => 'Presensi melalui scan QR code'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Presensi untuk ' . $student->name . ' berhasil disimpan.',
                'student_name' => $student->name
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function processManual(Request $request, $slug)
    {
        try {
            $request->validate([
                'manual_code' => 'required|string|size:8',
                'device_timestamp' => 'required|string'
            ], [
                'manual_code.required' => 'Kode unik wajib diisi.',
                'manual_code.size' => 'Kode unik harus 8 karakter.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ], 422);
        }

        $session = PresenceSession::where('slug', $slug)->firstOrFail();
        $mentorId = session('mentor_id');
        $groupId = session('mentor_group_id');
        $code = $request->input('manual_code');

        // Validasi waktu sesi menggunakan waktu dari perangkat
        // Timestamp sudah dalam format MySQL datetime dari JavaScript
        $deviceTime = Carbon::createFromFormat('Y-m-d H:i:s', $request->input('device_timestamp'));
        $startTime = Carbon::parse($session->start_time);
        $endTime = Carbon::parse($session->end_time);

        if ($deviceTime->lt($startTime)) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi presensi belum dimulai. Waktu mulai: ' . $startTime->format('d/m/Y H:i')
            ], 400);
        }

        if ($deviceTime->gt($endTime)) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi presensi sudah berakhir. Waktu berakhir: ' . $endTime->format('d/m/Y H:i')
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Cari peserta berdasarkan kode unik atau raw_barcode
            $student = Attendance::where('group_id', $groupId)
                ->where(function ($query) use ($code) {
                    $query->where('unique_code', $code)
                        ->orWhere('raw_barcode', $code);
                })
                ->first();

            if (!$student) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Kode tidak valid atau peserta tidak ditemukan dalam kelompok Anda.'
                ], 404);
            }

            // Cek apakah sudah presensi
            $existingSubmission = AttendanceSubmission::where('presence_session_id', $session->id)
                ->where('student_id', $student->id)
                ->first();

            if ($existingSubmission) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Peserta ' . $student->name . ' sudah melakukan presensi sebelumnya.',
                    'type' => 'warning'
                ], 409);
            }

            // Simpan data presensi langsung ke database
            AttendanceSubmission::create([
                'presence_session_id' => $session->id,
                'group_id' => $groupId,
                'mentor_id' => $mentorId,
                'student_id' => $student->id,
                'submitted_at' => $deviceTime,
                'status' => 'hadir',
                'submission_method' => 'manual',
                'notes' => 'Presensi melalui input manual'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Presensi untuk ' . $student->name . ' berhasil disimpan.',
                'student_name' => $student->name
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createAttendanceRecord(Request $request, $slug)
    {
        try {
            $request->validate([
                'student_id' => 'required|integer|exists:attendances,id',
                'status' => 'required|string|in:terlambat,izin,sakit',
                'device_timestamp' => 'required|string'
            ]);

            $session = PresenceSession::where('slug', $slug)->firstOrFail();
            $mentorId = session('mentor_id');
            $groupId = session('mentor_group_id');

            // Validasi waktu sesi menggunakan waktu dari perangkat
            // Timestamp sudah dalam format MySQL datetime dari JavaScript
            $deviceTime = Carbon::createFromFormat('Y-m-d H:i:s', $request->input('device_timestamp'));
            $startTime = Carbon::parse($session->start_time);
            $endTime = Carbon::parse($session->end_time);

            if ($deviceTime->lt($startTime)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi presensi belum dimulai. Waktu mulai: ' . $startTime->format('d/m/Y H:i')
                ], 400);
            }

            if ($deviceTime->gt($endTime)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi presensi sudah berakhir. Waktu berakhir: ' . $endTime->format('d/m/Y H:i')
                ], 400);
            }

            DB::beginTransaction();

            // Cari student berdasarkan ID
            $student = Attendance::where('id', $request->student_id)
                ->where('group_id', $groupId)
                ->first();

            if (!$student) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Peserta tidak ditemukan dalam kelompok Anda.'
                ], 404);
            }

            // Cek apakah sudah ada record presensi
            $existingSubmission = AttendanceSubmission::where('presence_session_id', $session->id)
                ->where('student_id', $student->id)
                ->first();

            if ($existingSubmission) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Peserta ' . $student->name . ' sudah memiliki record presensi.'
                ], 409);
            }

            // Buat record presensi baru
            AttendanceSubmission::create([
                'presence_session_id' => $session->id,
                'group_id' => $groupId,
                'mentor_id' => $mentorId,
                'student_id' => $student->id,
                'submitted_at' => $deviceTime,
                'status' => $request->status,
                'submission_method' => 'manual_mentor',
                'notes' => 'Presensi dibuat oleh mentor dengan status: ' . $request->status
            ]);

            DB::commit();

            $statusText = [
                'hadir' => 'Hadir',
                'terlambat' => 'Terlambat',
                'izin' => 'Izin',
                'sakit' => 'Sakit'
            ];

            return response()->json([
                'success' => true,
                'message' => 'Record presensi untuk ' . $student->name . ' berhasil dibuat dengan status ' . $statusText[$request->status] . '.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
