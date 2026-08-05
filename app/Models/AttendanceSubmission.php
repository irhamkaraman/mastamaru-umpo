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
        'notes',
        'submission_method',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    // Relasi dengan PresenceSession
    public function presenceSession(): BelongsTo
    {
        return $this->belongsTo(PresenceSession::class);
    }

    // Relasi dengan Student (dari tabel attendances)
    public function student(): BelongsTo
    {
        return $this->belongsTo(Attendance::class, 'student_id', 'id');
    }

    // Relasi dengan Group
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    // Relasi dengan Mentor
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }
}
