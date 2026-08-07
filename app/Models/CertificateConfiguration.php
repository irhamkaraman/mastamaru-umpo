<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'background_image',
        'number_format',
        'current_sequence',
        'name_x',
        'name_y',
        'nim_x',
        'nim_y',
        'number_x',
        'number_y',
        'faculty_x',
        'faculty_y',
        'font_size_name',
        'font_size_nim',
        'font_size_number',
        'text_color',
        'is_active',
    ];
}
