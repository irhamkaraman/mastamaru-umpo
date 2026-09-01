<?php

namespace App\Observers;

use App\Models\AttendanceSubmission;
use App\Services\ScoreCalculationService;

class AttendanceSubmissionObserver
{
    /**
     * Handle the AttendanceSubmission "saved" event.
     */
    public function saved(AttendanceSubmission $attendanceSubmission): void
    {
        ScoreCalculationService::recalculateForStudent($attendanceSubmission->student_id);
    }

    /**
     * Handle the AttendanceSubmission "deleted" event.
     */
    public function deleted(AttendanceSubmission $attendanceSubmission): void
    {
        ScoreCalculationService::recalculateForStudent($attendanceSubmission->student_id);
    }

    /**
     * Handle the AttendanceSubmission "restored" event.
     */
    public function restored(AttendanceSubmission $attendanceSubmission): void
    {
        ScoreCalculationService::recalculateForStudent($attendanceSubmission->student_id);
    }

    /**
     * Handle the AttendanceSubmission "force deleted" event.
     */
    public function forceDeleted(AttendanceSubmission $attendanceSubmission): void
    {
        ScoreCalculationService::recalculateForStudent($attendanceSubmission->student_id);
    }
}
