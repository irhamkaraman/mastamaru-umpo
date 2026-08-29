<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        // Data contoh untuk template import peserta
        return [
            ['Khansa Salma Nabilah', '26442187', '089666087221', 'Ekonomi', 'Akuntansi'],
            ['Nafisah Khoirunnisa', '26340699', '085746676786', 'Keguruan dan Ilmu Pendidikan', 'Pendidikan Guru Pendidikan Anak Usia Dini'],
            ['Muhammad Roni Nur Hakim', '26241353', '085232145678', 'Ilmu Sosial dan Ilmu Politik', 'Ilmu Komunikasi'],
            ['Arya Bagas Febriansyah', '26632977', '082332285921', 'Ilmu Kesehatan', 'Keperawatan'],
            ['Ahmad Budi Santoso', '26110001', '081234567890', 'Teknik', 'Teknik Informatika'],
        ];
    }

    public function headings(): array
    {
        return [
            'nama_peserta',
            'nim_peserta',
            'no_telp_wa',
            'fakultas',
            'program_studi'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => '2563EB']
                ]
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30, // Nama
            'B' => 18, // NIM
            'C' => 20, // No. WA / Telp
            'D' => 35, // Fakultas
            'E' => 35, // Program Studi
        ];
    }
}