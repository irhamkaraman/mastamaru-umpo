<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceDataExport implements FromQuery, ShouldAutoSize, WithChunkReading, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Query untuk export data dengan filter aktif dari Filament Table
     */
    public function query()
    {
        $query = Attendance::query()
            ->with(['group', 'mentor', 'assessment'])
            ->select([
                'id',
                'name',
                'student_id',
                'faculty',
                'study_program',
                'phone_number',
                'status',
                'group_id',
                'mentor_id',
            ]);
        if (! empty($this->filters['group_id']['value'])) {
            $query->where('group_id', $this->filters['group_id']['value']);
        }
        if (! empty($this->filters['mentor_id']['value'])) {
            $query->where('mentor_id', $this->filters['mentor_id']['value']);
        }
        if (! empty($this->filters['faculty']['value'])) {
            $query->where('faculty', $this->filters['faculty']['value']);
        }
        if (! empty($this->filters['study_program']['value'])) {
            $query->where('study_program', $this->filters['study_program']['value']);
        }

        return $query->orderBy('name', 'asc');
    }

    /**
     * Mapping data untuk setiap row
     */
    public function map($attendance): array
    {
        $totalPoints = $attendance->assessment ? $attendance->assessment->total_presence_points : 0;
        $attendanceScore = $attendance->assessment ? $attendance->assessment->attendance_score : 0;
        $grade = $attendance->assessment ? $attendance->assessment->grade : 'D';
        $statusKelulusan = strtoupper($attendance->status ?? ($attendance->assessment ? $attendance->assessment->status : 'GAGAL'));

        return [
            $attendance->name,
            $attendance->student_id,
            $attendance->phone_number ?? '-',
            $attendance->faculty ?? '-',
            $attendance->study_program ?? '-',
            $attendance->group ? $attendance->group->name : 'Belum Ada Kelompok',
            $attendance->mentor ? $attendance->mentor->name : 'Belum Ada Pendamping',
            $totalPoints,
            $attendanceScore.'%',
            $grade,
            $statusKelulusan,
        ];
    }

    /**
     * Header kolom untuk file Excel/CSV
     */
    public function headings(): array
    {
        return [
            'Nama Peserta',
            'NIM',
            'No. WhatsApp / Telp',
            'Fakultas',
            'Program Studi',
            'Kelompok',
            'Pendamping (Mentor)',
            'Total Poin Presensi (Maks 100)',
            'Nilai Kehadiran (%)',
            'Predikat (Grade)',
            'Status Kelulusan',
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

    /**
     * Chunk size untuk membaca data
     */
    public function chunkSize(): int
    {
        return 500;
    }
}
