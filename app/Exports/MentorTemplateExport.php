<?php

namespace App\Exports;

use App\Models\Group;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MentorTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        $groups = Group::orderBy('order')->get();
        $data = [];
        
        if ($groups->isNotEmpty()) {
            foreach ($groups as $index => $group) {
                $data[] = [
                    $group->name,
                    'Pendamping ' . ($index + 1),
                    '2024000' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'password123'
                ];
            }
        } else {
            $data = [
                ['Kelompok A', 'Ahmad Mentor', '2024000001', 'password123'],
                ['Kelompok B', 'Budi Pendamping', '2024000002', 'password456'],
            ];
        }
        
        return $data;
    }

    public function headings(): array
    {
        return ['Nama Kelompok', 'Nama Pendamping', 'NIM', 'Kata Sandi'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 20, 'B' => 25, 'C' => 15, 'D' => 15];
    }
}