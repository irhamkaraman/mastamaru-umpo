<?php

namespace App\Exports;

use App\Models\Group;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GroupDataExport implements FromQuery, ShouldAutoSize, WithChunkReading, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Group::query()
            ->with(['mentors'])
            ->withCount('attendances');
        if (! empty($this->filters['has_mentors']['value'])) {
            if ($this->filters['has_mentors']['value'] === 'with') {
                $query->has('mentors');
            } elseif ($this->filters['has_mentors']['value'] === 'without') {
                $query->doesntHave('mentors');
            }
        }
        if (! empty($this->filters['has_students']['value'])) {
            if ($this->filters['has_students']['value'] === 'with') {
                $query->has('attendances');
            } elseif ($this->filters['has_students']['value'] === 'without') {
                $query->doesntHave('attendances');
            }
        }

        return $query->orderBy('order', 'asc');
    }

    public function map($group): array
    {
        $mentors = $group->mentors->map(function ($m) {
            $phone = $m->phone_number ? ' ('.$m->phone_number.')' : '';

            return $m->name.$phone;
        })->implode(', ');

        return [
            $group->order,
            $group->name,
            $group->slug,
            $mentors ?: 'Belum Ada Pendamping',
            $group->attendances_count ?? 0,
        ];
    }

    public function headings(): array
    {
        return [
            'Urutan',
            'Nama Kelompok',
            'Slug URL',
            'Nama Pendamping (No. WA)',
            'Jumlah Peserta',
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
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => '16A34A'],
                ],
            ],
        ];
    }

    public function chunkSize(): int
    {
        return 200;
    }
}
