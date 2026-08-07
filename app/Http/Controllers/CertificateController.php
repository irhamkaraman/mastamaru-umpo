<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function generateForAttendance(\App\Models\Attendance $attendance)
    {
        $config = \App\Models\CertificateConfiguration::where('is_active', true)->first();
        if (!$config || !$config->background_image) {
            throw new \Exception('Konfigurasi sertifikat aktif tidak ditemukan atau background image kosong.');
        }

        $fontPath = public_path('fonts/Roboto-Regular.ttf');
        if (!file_exists($fontPath)) {
            throw new \Exception('Font TTF tidak ditemukan di public/fonts/Roboto-Regular.ttf');
        }

        // Generate number
        $config->increment('current_sequence');
        $sequenceStr = str_pad($config->current_sequence, 3, '0', STR_PAD_LEFT);
        $certificateNumber = str_replace('{seq}', $sequenceStr, $config->number_format);

        // Path
        $templatePath = storage_path('app/public/' . $config->background_image);
        if (!file_exists($templatePath)) {
            throw new \Exception('Background image tidak ditemukan: ' . $templatePath);
        }

        // Generate
        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
        $image = $manager->decodePath($templatePath);

        // Helper untuk menulis teks
        $writeText = function ($img, $text, $x, $y, $size) use ($fontPath, $config) {
            if (!$text || $x === null || $y === null) return;
            $img->text($text, $x, $y, function ($font) use ($fontPath, $config, $size) {
                $font->file($fontPath);
                $font->size($size);
                $font->color($config->text_color ?? '#000000');
                $font->align('left', 'top');
            });
        };

        $writeText($image, $attendance->name, $config->name_x, $config->name_y, $config->font_size_name);
        $writeText($image, $attendance->student_id, $config->nim_x, $config->nim_y, $config->font_size_nim);
        $writeText($image, $certificateNumber, $config->number_x, $config->number_y, $config->font_size_number);
        
        if ($attendance->faculty) {
            $writeText($image, $attendance->faculty, $config->faculty_x, $config->faculty_y, $config->font_size_nim);
        }

        // Ensure directory exists
        $certDir = storage_path('app/public/certificates');
        if (!is_dir($certDir)) {
            mkdir($certDir, 0755, true);
        }

        // Hapus sertifikat lama jika ada untuk NIM ini
        $oldFiles = glob($certDir . '/' . $attendance->student_id . '_*.png');
        foreach ($oldFiles as $file) {
            unlink($file);
        }

        // Save (nim_nama_tanggal.png)
        $date = date('Ymd');
        $slugName = \Illuminate\Support\Str::slug($attendance->name);
        $fileName = "{$attendance->student_id}_{$slugName}_{$date}.png";
        $savePath = $certDir . '/' . $fileName;

        $image->save($savePath);

        return $fileName;
    }
}
