<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;

class AttendanceDataExport implements FromQuery, WithHeadings, ShouldAutoSize, WithMapping, WithChunkReading, ShouldQueue
{
    use Exportable;

    /**
     * Query untuk export data
     * Menggunakan FromQuery untuk menghindari memory exhausted
     */
    public function query()
    {
        return Attendance::query()
            ->with(['group', 'mentor'])
            ->select(['id', 'name', 'student_id', 'faculty', 'study_program', 'group_id', 'mentor_id']);
    }

    /**
     * Mapping data untuk setiap row
     */
    public function map($attendance): array
    {
        return [
            $attendance->name,
            $attendance->student_id,
            $attendance->faculty ?? 'Tidak tersedia',
            $attendance->study_program ?? 'Tidak tersedia',
            $attendance->mentor ? $attendance->mentor->name : 'Tidak tersedia',
            $attendance->group ? $attendance->group->name : 'Tidak tersedia',
        ];
    }

    /**
     * Header kolom untuk file Excel
     */
    public function headings(): array
    {
        return [
            'Nama Peserta',
            'NIM',
            'Fakultas',
            'Program Studi',
            'Nama Pendamping',
            'Nama Kelompok'
        ];
    }

    /**
     * Chunk size untuk membaca data
     * Mengurangi penggunaan memory dengan membaca data dalam chunk
     */
    public function chunkSize(): int
    {
        return 1000;
    }
}