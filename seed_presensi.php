<?php

use Illuminate\Support\Str;

// 1. Get the student NAFISAH (NIM: 26340699)
$student = \App\Models\Attendance::where('student_id', '26340699')->first();
if (!$student) {
    echo "Student not found!\n";
    exit;
}

// Hapus riwayat presensi yang lama agar tidak menumpuk
\App\Models\AttendanceSubmission::where('student_id', $student->id)->delete();
\App\Models\PresenceSession::where('slug', 'like', 'dummy-sesi-%')->delete();

$sessionTypes = [
    ['name' => 'Presensi Datang', 'type' => 'datang', 'time' => '07:00'],
    ['name' => 'Penyampaian Materi', 'type' => 'materi', 'time' => '10:00'],
    ['name' => 'Presensi Pulang', 'type' => 'pulang', 'time' => '15:00'],
];

$submissions_count = 0;

// Buat sesi untuk 5 Hari berturut-turut (misalnya tanggal 8 hingga 12 September 2026)
$startDate = \Carbon\Carbon::create(2026, 9, 8);

for ($hari = 1; $hari <= 5; $hari++) {
    $currentDate = $startDate->copy()->addDays($hari - 1);
    
    foreach ($sessionTypes as $sType) {
        // Buat Session baru
        $session = \App\Models\PresenceSession::create([
            'session_name' => $sType['name'],
            'session_type' => $sType['type'],
            'day_number' => $hari,
            'slug' => 'dummy-sesi-' . uniqid(),
            'description' => 'Sesi dummy untuk testing tabel Hari ' . $hari,
            'start_time' => $currentDate->copy()->setTimeFromTimeString($sType['time'] . ':00'),
            'end_time' => $currentDate->copy()->setTimeFromTimeString($sType['time'] . ':00')->addHours(2),
            'is_active' => true,
            'session_code' => strtoupper(Str::random(4)),
        ]);
        
        // Buat Submisi Kehadiran untuk NAFISAH
        \App\Models\AttendanceSubmission::create([
            'presence_session_id' => $session->id,
            'student_id' => $student->id,
            'group_id' => $student->group_id ?? 1,
            'mentor_id' => $student->mentor_id ?? 1,
            'status' => 'hadir',
            'score_points' => 10,
            'submitted_at' => $currentDate->copy()->setTimeFromTimeString($sType['time'] . ':00')->addMinutes(rand(1, 15)), // Telat dikit biar natural
            'notes' => 'Hadir',
            'latitude' => null,
            'longitude' => null,
        ]);
        
        $submissions_count++;
    }
}

echo "Berhasil membuat $submissions_count riwayat presensi (Hari 1 s/d Hari 5) untuk " . $student->name . "\n";
