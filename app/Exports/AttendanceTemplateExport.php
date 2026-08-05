<?php

namespace App\Exports;

use App\Models\Group;
use App\Models\Mentor;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        // Daftar fakultas untuk contoh
        $faculties = [
            'Fakultas Teknik',
            'Fakultas Ekonomi dan Bisnis',
            'Fakultas Ilmu Sosial dan Politik',
            'Fakultas Hukum',
            'Fakultas Pertanian',
            'Fakultas Kedokteran',
            'Fakultas Keguruan dan Ilmu Pendidikan',
            'Fakultas Matematika dan Ilmu Pengetahuan Alam',
            'Fakultas Peternakan',
            'Fakultas Kehutanan',
            'Fakultas Ilmu Kelautan dan Perikanan',
            'Fakultas Kesehatan Masyarakat',
            'Fakultas Farmasi',
            'Fakultas Ilmu Budaya'
        ];

        // Daftar program studi untuk contoh
        $studyPrograms = [
            'Teknik Informatika',
            'Sistem Informasi',
            'Teknik Elektro',
            'Manajemen',
            'Akuntansi',
            'Ilmu Komunikasi',
            'Hukum',
            'Agroteknologi',
            'Kedokteran',
            'Pendidikan Bahasa Indonesia'
        ];

        // Data contoh untuk template
        $data = [
            ['Ahmad Budi Santoso', '2024010001', $faculties[array_rand($faculties)], $studyPrograms[array_rand($studyPrograms)]],
            ['Siti Nurhaliza', '2024010002', $faculties[array_rand($faculties)], $studyPrograms[array_rand($studyPrograms)]],
            ['Budi Setiawan', '2024010003', $faculties[array_rand($faculties)], $studyPrograms[array_rand($studyPrograms)]],
            ['Citra Dewi', '2024010004', $faculties[array_rand($faculties)], $studyPrograms[array_rand($studyPrograms)]],
            ['Dani Pratama', '2024010005', $faculties[array_rand($faculties)], $studyPrograms[array_rand($studyPrograms)]],
        ];

        return $data;
    }

    public function headings(): array
    {
        return ['nama_peserta', 'nim_peserta', 'fakultas', 'program_studi'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 30, 'B' => 15, 'C' => 40, 'D' => 30];
    }
}