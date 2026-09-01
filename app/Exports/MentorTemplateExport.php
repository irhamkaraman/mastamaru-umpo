<?php

namespace App\Exports;

use App\Models\Group;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MentorTemplateExport implements FromArray, WithColumnWidths, WithHeadings, WithStyles
{
    public function array(): array
    {
        $groups = Group::orderBy('order')->get();
        $data = [];
        if ($groups->isNotEmpty()) {
            foreach ($groups->take(5) as $index => $group) {
                $data[] = [
                    $group->name,
                    'Pendamping '.($index + 1),
                    '2024000'.str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    '0812345678'.str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                    'password123',
                ];
            }
        } else {
            $data = [
                ['Kelompok 1', 'Ahmad Mentor', '2024000001', '081234567890', 'password123'],
                ['Kelompok 2', 'Budi Pendamping', '2024000002', '085712345678', 'password456'],
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Nama Kelompok',
            'Nama Pendamping',
            'NIM',
            'Nomor WhatsApp / Telp',
            'Kata Sandi',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '2563EB'],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 30,
            'C' => 18,
            'D' => 22,
            'E' => 18,
        ];
    }
}
