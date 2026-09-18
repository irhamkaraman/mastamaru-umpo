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
        if ($sessionType === 'pulang' || $sessionType === 'materi') {
            return match ($status) {
                'hadir' => 10,
                'sakit' => 7,
                'izin' => 5,
                default => 0,
            };
        }

        return match ($status) {
            'hadir' => 10,
            'terlambat' => 8,
            'sakit' => 6,
            'izin' => 5,
            default => 0,
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
        if (! $student) {
            return null;
        }
        $submissions = AttendanceSubmission::where('student_id', $studentDbId)
            ->whereHas('presenceSession')
            ->get();
        $totalPoints = (int) $submissions->sum('score_points');
        $totalSessionsCount = PresenceSession::count();
        $maxPossiblePoints = $totalSessionsCount > 0 ? ($totalSessionsCount * 10) : 100;
        if ($maxPossiblePoints < 100) {
            $maxPossiblePoints = 100;
        }
        $attendanceScore = $maxPossiblePoints > 0 ? round(($totalPoints / $maxPossiblePoints) * 100, 2) : 0;
        if ($attendanceScore > 100) {
            $attendanceScore = 100;
        }
        $assessment = StudentAssessment::firstOrNew(['student_id' => $studentDbId]);
        $activityScore = $assessment->activity_score;
        if ($activityScore !== null && $activityScore > 0) {
            $normalizedActivity = $activityScore * 10;
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
     * Ambil rincian matriks presensi per hari untuk peserta (menampilkan semua sesi).
     */
    public static function getStudentPresenceMatrix(int $studentDbId): array
    {
        $sessions = PresenceSession::orderBy('day_number', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
        $submissions = AttendanceSubmission::where('student_id', $studentDbId)
            ->with(['presenceSession', 'mentor'])
            ->get()
            ->keyBy('presence_session_id');
        $days = [];
        $totalEarned = 0;
        $groupedByDay = $sessions->groupBy('day_number');
        $maxDay = $groupedByDay->keys()->max() ?? 0;

        $formatSessionSlot = function (PresenceSession $session, ?AttendanceSubmission $sub) {
            if ($sub) {
                return [
                    'session' => $session,
                    'submission' => $sub,
                    'status' => ucfirst($sub->status),
                    'status_type' => strtolower($sub->status),
                    'points' => (int) $sub->score_points,
                    'time' => $sub->submitted_at ? $sub->submitted_at->format('H:i:s') : null,
                    'is_closed' => true,
                ];
            }

            $isClosed = (! $session->is_active || ($session->end_time && $session->end_time < now()));

            return [
                'session' => $session,
                'submission' => null,
                'status' => $isClosed ? 'Alpha' : 'Belum Presensi',
                'status_type' => $isClosed ? 'alpha' : 'pending',
                'points' => 0,
                'time' => null,
                'is_closed' => $isClosed,
            ];
        };

        for ($day = 1; $day <= $maxDay; $day++) {
            $daySessions = $groupedByDay->get($day, collect());
            $daySlots = [];
            $dayTotal = 0;

            foreach ($daySessions as $session) {
                $sub = $submissions->get($session->id);
                $slotData = $formatSessionSlot($session, $sub);
                $daySlots[] = $slotData;
                $dayTotal += $slotData['points'];
            }

            $totalEarned += $dayTotal;

            $days[$day] = [
                'day' => $day,
                'sessions' => $daySlots,
                'total' => $dayTotal,
            ];
        }

        return [
            'days' => $days,
            'total_points' => $totalEarned,
        ];
    }
}
