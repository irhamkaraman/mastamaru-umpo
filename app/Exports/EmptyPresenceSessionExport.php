<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmptyPresenceSessionExport implements FromArray, WithHeadings, WithTitle, WithStyles
{
    /**
     * Data kosong untuk sheet
     */
    public function array(): array
    {
        return [
            ['Tidak ada data sesi presensi yang tersedia.']
        ];
    }

    /**
     * Header kolom
     */
    public function headings(): array
    {
        return [
            'Informasi'
        ];
    }

    /**
     * Title untuk worksheet
     */
    public function title(): string
    {
        return 'Tidak Ada Data';
    }

    /**
     * Styling untuk worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FF6B6B']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]
            ],
            2 => [
                'font' => ['italic' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]
        ];
    }
}