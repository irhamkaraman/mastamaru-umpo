<?php

namespace Tests\Unit;

use App\Models\Attendance;
use App\Models\AttendanceSubmission;
use App\Models\PresenceSession;
use App\Services\ScoreCalculationService;
use Tests\TestCase;

class ScoreCalculationAndProtectionTest extends TestCase
{
    public function test_calculate_points_for_all_session_types_and_statuses(): void
    {
        // Sesi Datang
        $this->assertEquals(10, ScoreCalculationService::calculatePoints('datang', 'hadir'));
        $this->assertEquals(8, ScoreCalculationService::calculatePoints('datang', 'terlambat'));
        $this->assertEquals(6, ScoreCalculationService::calculatePoints('datang', 'sakit'));
        $this->assertEquals(5, ScoreCalculationService::calculatePoints('datang', 'izin'));
        $this->assertEquals(0, ScoreCalculationService::calculatePoints('datang', 'alpha'));
        $this->assertEquals(0, ScoreCalculationService::calculatePoints('datang', 'unknown'));

        // Sesi Pulang
        $this->assertEquals(10, ScoreCalculationService::calculatePoints('pulang', 'hadir'));
        $this->assertEquals(7, ScoreCalculationService::calculatePoints('pulang', 'sakit'));
        $this->assertEquals(5, ScoreCalculationService::calculatePoints('pulang', 'izin'));
        $this->assertEquals(0, ScoreCalculationService::calculatePoints('pulang', 'alpha'));

        // Sesi Materi
        $this->assertEquals(10, ScoreCalculationService::calculatePoints('materi', 'hadir'));
        $this->assertEquals(7, ScoreCalculationService::calculatePoints('materi', 'sakit'));
        $this->assertEquals(5, ScoreCalculationService::calculatePoints('materi', 'izin'));
        $this->assertEquals(0, ScoreCalculationService::calculatePoints('materi', 'alpha'));
    }

    public function test_grade_and_predicate_conversions(): void
    {
        $gradeA = ScoreCalculationService::getGradeAndDescription(95);
        $this->assertEquals('A', $gradeA['grade']);
        $this->assertTrue($gradeA['is_passed']);

        $gradeB = ScoreCalculationService::getGradeAndDescription(85);
        $this->assertEquals('B', $gradeB['grade']);
        $this->assertTrue($gradeB['is_passed']);

        $gradeC = ScoreCalculationService::getGradeAndDescription(75);
        $this->assertEquals('C', $gradeC['grade']);
        $this->assertTrue($gradeC['is_passed']);

        $gradeD = ScoreCalculationService::getGradeAndDescription(50);
        $this->assertEquals('D', $gradeD['grade']);
        $this->assertFalse($gradeD['is_passed']);
    }
}
