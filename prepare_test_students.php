<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Grab 4 students except Nafisah to not mess her up, or just any 4
$students = \App\Models\Attendance::where('student_id', '!=', '26340699')->take(4)->get();

if ($students->count() < 4) {
    echo "Tidak cukup mahasiswa di database untuk test 4 predikat.\n";
    exit;
}

// Target points for each student
$targets = [
    ['predikat' => 'A', 'points' => 95, 'hari' => 5], // A (>=90)
    ['predikat' => 'B', 'points' => 85, 'hari' => 4], // B (80-89)
    ['predikat' => 'C', 'points' => 75, 'hari' => 4], // C (70-79)
    ['predikat' => 'D', 'points' => 65, 'hari' => 2], // D (<70), tapi hari >= 2 biar gak GAGAL
];

// Ensure we have presence sessions to use
$sessions = \App\Models\PresenceSession::where('day_number', '>', 0)->get()->groupBy('day_number');
if ($sessions->isEmpty()) {
    echo "Tidak ada PresenceSession!\n";
    exit;
}

foreach ($students as $index => $student) {
    $target = $targets[$index];
    
    // Clear old
    \App\Models\AttendanceSubmission::where('student_id', $student->id)->delete();
    
    $points_needed = $target['points'];
    $days_to_attend = $target['hari'];
    
    $points_per_day = floor($points_needed / $days_to_attend);
    $remainder = $points_needed % $days_to_attend;
    
    // Generate submissions
    for ($d = 1; $d <= $days_to_attend; $d++) {
        if (!isset($sessions[$d])) continue;
        
        $day_sessions = $sessions[$d];
        $points_for_this_day = $points_per_day + ($d == 1 ? $remainder : 0); // Put remainder on day 1
        
        // Let's just give the points to the first session of the day
        $firstSession = $day_sessions->first();
        
        \App\Models\AttendanceSubmission::create([
            'presence_session_id' => $firstSession->id,
            'student_id' => $student->id,
            'group_id' => $student->group_id ?? 1,
            'mentor_id' => 1,
            'status' => 'hadir',
            'score_points' => $points_for_this_day,
            'submitted_at' => now()->subDays(5 - $d),
        ]);
    }
    
    echo "NIM: " . $student->student_id . " | Predikat Target: " . $target['predikat'] . " | Total Points: " . $target['points'] . "\n";
}
