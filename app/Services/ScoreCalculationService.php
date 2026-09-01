<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceSubmission;
use App\Models\PresenceSession;
use App\Models\StudentAssessment;

class ScoreCalculationService
{
    /**
     * Menghitung poin yang diperoleh dari tipe sesi & status kehadiran.
     *
     * Aturan Poin:
     * - Sesi Datang:
     *   * Hadir (Tepat waktu) = 10
     *   * Terlambat           = 8
     *   * Sakit               = 6
     *   * Izin                = 5
     *   * Alpa                = 0
     * - Sesi Pulang:
     *   * Hadir (Sampai selesai) = 10
     *   * Sakit                  = 7
     *   * Izin                   = 5
     *   * Alpa                   = 0
     */
    public static function calculatePoints(string $sessionType, string $status): int
    {
        $status = strtolower(trim($status));
        $sessionType = strtolower(trim($sessionType));

        if ($sessionType === 'pulang') {
            return match ($status) {
                'hadir' => 10,
                'sakit' => 7,
                'izin'  => 5,
                default => 0,
            };
        }

        // Default: Sesi Datang
        return match ($status) {
            'hadir'     => 10,
            'terlambat' => 8,
            'sakit'     => 6,
            'izin'      => 5,
            default     => 0,
        };
    }

    /**
     * Konversi nilai akhir (0-100) ke Predikat & Keterangan.
     */
    public static function getGradeAndDescription(float $score): array
    {
        if ($score >= 90) {
            return ['grade' => 'A', 'description' => 'Sangat Baik', 'is_passed' => true];
        } elseif ($score >= 80) {
            return ['grade' => 'B', 'description' => 'Baik', 'is_passed' => true];
        } elseif ($score >= 70) {
            return ['grade' => 'C', 'description' => 'Cukup', 'is_passed' => true];
        } else {
            return ['grade' => 'D', 'description' => 'Perlu Ditingkatkan', 'is_passed' => false];
        }
    }

    /**
     * Mengkalkulasi ulang penilaian peserta & sinkronisasi ke tabel student_assessments dan status di attendances.
     */
    public static function recalculateForStudent(int $studentDbId): ?StudentAssessment
    {
        $student = Attendance::find($studentDbId);
        if (!$student) return null;

        $submissions = AttendanceSubmission::where('student_id', $studentDbId)->get();
        $totalPoints = (int) $submissions->sum('score_points');

        // Menghitung maksimal poin kegiatan (jumlah sesi aktif * 10, atau minimal 100 poin jika 5 hari)
        $totalSessionsCount = PresenceSession::count();
        $maxPossiblePoints = $totalSessionsCount > 0 ? ($totalSessionsCount * 10) : 100;
        if ($maxPossiblePoints < 100) {
            $maxPossiblePoints = 100; // Asumsi default 5 hari x 20 poin = 100
        }

        $attendanceScore = $maxPossiblePoints > 0 ? round(($totalPoints / $maxPossiblePoints) * 100, 2) : 0;
        if ($attendanceScore > 100) $attendanceScore = 100;

        $assessment = StudentAssessment::firstOrNew(['student_id' => $studentDbId]);
        $activityScore = $assessment->activity_score; // 1-10

        // Jika ada nilai keaktifan, kita bisa bobotkan: Kehadiran (misal 70%) + Keaktifan (misal 30%) atau full kehadiran
        if ($activityScore !== null && $activityScore > 0) {
            $normalizedActivity = $activityScore * 10; // Skala 100
            $finalScore = round(($attendanceScore * 0.7) + ($normalizedActivity * 0.3), 2);
        } else {
            $finalScore = $attendanceScore;
        }

        $gradeInfo = self::getGradeAndDescription($finalScore);

        $assessment->total_presence_points = $totalPoints;
        $assessment->attendance_score = $attendanceScore;
        $assessment->final_score = $finalScore;
        $assessment->grade = $gradeInfo['grade'];
        $assessment->status = $student->status ?? 'proses'; 
        $assessment->save();

        return $assessment;
    }

    /**
     * Ambil rincian matriks presensi per hari (Datang & Pulang) untuk peserta.
     */
    public static function getStudentPresenceMatrix(int $studentDbId): array
    {
        $sessions = PresenceSession::orderBy('day_number', 'asc')
            ->orderBy('session_type', 'asc')
            ->get();

        $submissions = AttendanceSubmission::where('student_id', $studentDbId)
            ->with(['presenceSession', 'mentor'])
            ->get()
            ->keyBy('presence_session_id');

        $days = [];
        $totalEarned = 0;

        // Group sesi per hari
        $groupedByDay = $sessions->groupBy('day_number');

        // Sesuaikan dengan hari yang benar-benar ada
        $maxDay = $groupedByDay->keys()->max() ?? 0;

        for ($day = 1; $day <= $maxDay; $day++) {
            $daySessions = $groupedByDay->get($day, collect());
            
            $datangSession = $daySessions->where('session_type', 'datang')->first(function($session) use ($submissions) {
                return $submissions->has($session->id);
            }) ?? $daySessions->firstWhere('session_type', 'datang');

            $pulangSession = $daySessions->where('session_type', 'pulang')->first(function($session) use ($submissions) {
                return $submissions->has($session->id);
            }) ?? $daySessions->firstWhere('session_type', 'pulang');

            $datangSub = $datangSession ? ($submissions->get($datangSession->id)) : null;
            $pulangSub = $pulangSession ? ($submissions->get($pulangSession->id)) : null;

            $datangPoints = $datangSub ? $datangSub->score_points : 0;
            $pulangPoints = $pulangSub ? $pulangSub->score_points : 0;
            $dayTotal = $datangPoints + $pulangPoints;
            $totalEarned += $dayTotal;

            $days[$day] = [
                'day' => $day,
                'datang' => [
                    'session' => $datangSession,
                    'submission' => $datangSub,
                    'status' => $datangSub ? ucfirst($datangSub->status) : '-',
                    'points' => $datangPoints,
                    'time' => $datangSub && $datangSub->submitted_at ? $datangSub->submitted_at->format('H:i:s') : null,
                ],
                'pulang' => [
                    'session' => $pulangSession,
                    'submission' => $pulangSub,
                    'status' => $pulangSub ? ucfirst($pulangSub->status) : '-',
                    'points' => $pulangPoints,
                    'time' => $pulangSub && $pulangSub->submitted_at ? $pulangSub->submitted_at->format('H:i:s') : null,
                ],
                'total' => $dayTotal
            ];
        }

        return [
            'days' => $days,
            'total_points' => $totalEarned,
        ];
    }
}
