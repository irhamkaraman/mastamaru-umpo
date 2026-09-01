<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceSubmission extends Model
{
    protected $fillable = [
        'presence_session_id',
        'group_id',
        'mentor_id',
        'student_id',
        'submitted_at',
        'status',
        'score_points',
        'notes',
        'submission_method',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'score_points' => 'integer',
    ];

    public function presenceSession(): BelongsTo
    {
        return $this->belongsTo(PresenceSession::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Attendance::class, 'student_id', 'id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }
}
