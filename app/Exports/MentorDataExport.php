<?php

namespace App\Exports;

use App\Models\Mentor;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MentorDataExport implements FromQuery, ShouldAutoSize, WithChunkReading, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Mentor::query()
            ->with(['group'])
            ->withCount('attendances')
            ->select([
                'id',
                'name',
                'student_id',
                'phone_number',
                'group_id',
                'raw_password',
                'created_at',
            ]);
        if (! empty($this->filters['group_id']['value'])) {
            $query->where('group_id', $this->filters['group_id']['value']);
        }

        return $query->orderBy('name', 'asc');
    }

    public function map($mentor): array
    {
        return [
            $mentor->name,
            $mentor->student_id,
            $mentor->phone_number ?? '-',
            $mentor->group ? $mentor->group->name : 'Belum Ada Kelompok',
            $mentor->attendances_count ?? 0,
            $mentor->raw_password ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Pendamping',
            'NIM',
            'No. WhatsApp / Telp',
            'Kelompok',
            'Jumlah Peserta Binaan',
            'Kata Sandi Akun',
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
