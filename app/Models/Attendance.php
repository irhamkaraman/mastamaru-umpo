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
        'raw_barcode',
        'unique_code',
    ];

    /**
     * Get the group that owns the attendance.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the mentor that owns the attendance.
     */
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
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
            // Generate kode dengan kombinasi huruf besar dan angka (8 karakter)
            $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $numbers = '0123456789';
            $characters = $letters . $numbers;
            
            $code = '';
            for ($i = 0; $i < 8; $i++) {
                $code .= $characters[rand(0, strlen($characters) - 1)];
            }
        } while (static::where('unique_code', $code)->exists());

        return $code;
    }
}