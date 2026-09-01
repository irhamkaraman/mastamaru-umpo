<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    use HasFactory;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Attendance $attendance) {
            if (empty($attendance->unique_code)) {
                $attendance->unique_code = static::generateUniqueCode();
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'group_id',
        'mentor_id',
        'name',
        'student_id',
        'faculty',
        'study_program',
        'phone_number',
        'raw_barcode',
        'unique_code',
        'status',
    ];

    /**
     * Get the group that owns the attendance.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Cek apakah sertifikat sudah pernah diterbitkan.
     */
    public function hasCertificate(): bool
    {
        $slugName = \Illuminate\Support\Str::slug($this->name, '_');
        $pdfFileName = "{$this->student_id}_sertifikat_{$slugName}.pdf";

        return file_exists(storage_path('app/public/certificates/'.$pdfFileName));
    }

    /**
     * Get the mentor that owns the attendance.
     */
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }

    /**
     * Get the assessment for the attendance.
     */
    public function assessment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StudentAssessment::class, 'student_id', 'id');
    }

    /**
     * Get total points earned.
     */
    public function getTotalPointsAttribute(): int
    {
        return (int) $this->attendanceSubmissions()->sum('score_points');
    }

    /**
     * Get the attendance submissions for the attendance.
     */
    public function attendanceSubmissions(): HasMany
    {
        return $this->hasMany(AttendanceSubmission::class, 'student_id', 'id');
    }

    /**
     * Generate a unique code for attendance.
     */
    public static function generateUniqueCode(): string
    {
        do {
            $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $numbers = '0123456789';
            $characters = $letters.$numbers;
            $code = '';
            for ($i = 0; $i < 8; $i++) {
                $code .= $characters[rand(0, strlen($characters) - 1)];
            }
        } while (static::where('unique_code', $code)->exists());

        return $code;
    }
}
