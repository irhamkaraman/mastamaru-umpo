<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PresenceSession extends Model
{
    protected $fillable = [
        'session_name',
        'slug',
        'session_type',
        'day_number',
        'description',
        'start_time',
        'end_time',
        'is_active',
        'session_code',
    ];

    protected $casts = [
        'day_number' => 'integer',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->session_code)) {
                do {
                    $model->session_code = '';
                    for ($i = 0; $i < 4; $i++) {
                        $model->session_code .= chr(rand(65, 90));
                    }
                } while (self::where('session_code', $model->session_code)->exists());
            }
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->session_name).'-'.strtolower(Str::random(6));
            }
        });
        static::updating(function ($model) {
            if ($model->isDirty('session_name') && ! empty($model->session_name)) {
                $model->slug = Str::slug($model->session_name).'-'.strtolower(Str::random(6));
            }
        });
    }

    public function attendanceSubmissions(): HasMany
    {
        return $this->hasMany(AttendanceSubmission::class);
    }

    public function isActive(): bool
    {
        $now = Carbon::now();
        $startTime = Carbon::parse($this->start_time);
        $endTime = Carbon::parse($this->end_time);

        return $this->is_active && $now->between($startTime, $endTime);
    }
}
