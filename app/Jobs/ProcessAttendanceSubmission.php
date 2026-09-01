<?php

namespace App\Jobs;

use App\Models\AttendanceSubmission;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAttendanceSubmission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $attendanceData;

    public $tries = 3;

    public $timeout = 60;

    public $backoff = [10, 30, 60];

    /**
     * Create a new job instance.
     */
    public function __construct($attendanceData)
    {
        $this->attendanceData = $attendanceData;
        $this->onQueue('attendance');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $studentId = $this->attendanceData['student_id'] ?? $this->attendanceData['user_id'];
            Log::info('Processing attendance submission', [
                'student_id' => $studentId,
                'session_id' => $this->attendanceData['presence_session_id'],
                'attempt' => $this->attempts(),
            ]);
            $existingSubmission = AttendanceSubmission::where('student_id', $studentId)
                ->where('presence_session_id', $this->attendanceData['presence_session_id'])
                ->first();
            if ($existingSubmission) {
                Log::warning('Attendance already exists, skipping', $this->attendanceData);

                return;
            }
            $submission = AttendanceSubmission::create([
                'presence_session_id' => $this->attendanceData['presence_session_id'],
                'group_id' => $this->attendanceData['group_id'],
                'mentor_id' => $this->attendanceData['mentor_id'],
                'student_id' => $studentId,
                'submitted_at' => $this->attendanceData['submitted_at'] ?? now(),
                'status' => $this->attendanceData['status'] ?? 'hadir',
                'submission_method' => $this->attendanceData['submission_method'] ?? 'qr_code',
                'notes' => $this->attendanceData['notes'] ?? null,
            ]);
            Log::info('Attendance processed successfully', [
                'submission_id' => $submission->id,
                'student_id' => $submission->student_id,
                'session_id' => $submission->presence_session_id,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to process attendance: '.$e->getMessage(), [
                'attendance_data' => $this->attendanceData,
                'attempt' => $this->attempts(),
                'error' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error('Attendance job failed permanently', [
            'attendance_data' => $this->attendanceData,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        $studentId = $this->attendanceData['student_id'] ?? $this->attendanceData['user_id'];

        return [
            'attendance',
            'student:'.$studentId,
            'session:'.$this->attendanceData['presence_session_id'],
        ];
    }
}
