<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAssessment extends Model
{
    protected $fillable = [
        'student_id',
        'total_presence_points',
        'attendance_score',
        'activity_score',
        'final_score',
        'grade',
        'status',
        'notes',
    ];

    protected $casts = [
        'attendance_score' => 'float',
        'activity_score' => 'float',
        'final_score' => 'float',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Attendance::class, 'student_id', 'id');
    }
}
