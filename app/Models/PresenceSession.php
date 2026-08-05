<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PresenceSession extends Model
{
    protected $fillable = [
        'session_name',
        'slug',
        'description',
        'start_time',
        'end_time',
        'is_active',
        'session_code',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->session_code)) {
                // Generate 4 digit huruf besar tanpa angka
                do {
                    $model->session_code = '';
                    for ($i = 0; $i < 4; $i++) {
                        $model->session_code .= chr(rand(65, 90)); // A-Z
                    }
                } while (self::where('session_code', $model->session_code)->exists());
            }
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->session_name) . '-' . strtolower(Str::random(6));
            }
        });
        
        static::updating(function ($model) {
            if ($model->isDirty('session_name') && !empty($model->session_name)) {
                $model->slug = Str::slug($model->session_name) . '-' . strtolower(Str::random(6));
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
