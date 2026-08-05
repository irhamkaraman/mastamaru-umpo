<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'order',
    ];

    /**
     * Get the mentors for the group.
     */
    public function mentors(): HasMany
    {
        return $this->hasMany(Mentor::class);
    }

    /**
     * Get the attendances for the group.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}