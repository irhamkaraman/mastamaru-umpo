<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'word_file',
        'applies_to',
        'number_format',
        'current_sequence',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Dapatkan template aktif yang berlaku untuk status peserta tertentu.
     */
    public static function getActiveFor(string $status): ?self
    {
        return static::where('is_active', true)
            ->where(function ($q) use ($status) {
                $q->where('applies_to', 'semua')
                  ->orWhere('applies_to', $status);
            })
            ->latest()
            ->first();
    }

    /**
     * Generate nomor sertifikat berikutnya dari format yang dikonfigurasi.
     */
    public function generateNextNumber(): string
    {
        $this->increment('current_sequence');
        $seq = str_pad($this->current_sequence, 4, '0', STR_PAD_LEFT);
        return str_replace('{seq}', $seq, $this->number_format);
    }
}
