<?php

$student = App\Models\Attendance::where('student_id', '26442187')->first();
if ($student) {
    $student->update(['status' => 'lulus']);
    Illuminate\Support\Facades\Cache::forget('student_data_'.$student->student_id);
    echo "Fixed and cache cleared\n";
} else {
    echo "Student not found\n";
}
